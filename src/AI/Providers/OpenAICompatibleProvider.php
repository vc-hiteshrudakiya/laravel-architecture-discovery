<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

use Illuminate\Support\Facades\Http;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Contracts\AIProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\DTO\AIAnalysisResponse;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\ArchitectureReviewPrompt;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\DocumentationPrompt;
use RuntimeException;

/**
 * Base provider for APIs that follow the OpenAI chat-completions format.
 * Extend this class and implement apiBase() and name().
 */
abstract class OpenAICompatibleProvider implements AIProvider
{
    public function __construct(protected readonly array $config) {}

    abstract protected function apiBase(): string;

    abstract protected function name(): string;

    public function analyze(array $architectureData): AIAnalysisResponse
    {
        return $this->reviewArchitecture($architectureData);
    }

    public function reviewArchitecture(array $architectureData): AIAnalysisResponse
    {
        $prompt = (new ArchitectureReviewPrompt())->build($architectureData);
        $model  = $this->config['model'] ?? '';

        try {
            $raw    = $this->request($prompt, $model, true);
            $parsed = $this->extractJson($raw);
            return AIAnalysisResponse::fromArray($parsed, $this->name(), $model, $raw);
        } catch (\Throwable $e) {
            return AIAnalysisResponse::error(
                ucfirst($this->name()) . ' analysis failed: ' . $e->getMessage(),
                $this->name(),
                $model
            );
        }
    }

    public function chat(string $message, array $context = []): string
    {
        $model       = $this->config['model'] ?? '';
        $contextJson = empty($context) ? '' : "\n\nRelevant architecture context:\n```json\n"
            . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n";

        $prompt = <<<PROMPT
You are an expert Laravel architect and senior PHP engineer.
{$contextJson}
Answer the following question about this Laravel application. Be specific — reference actual class names, method counts, and URIs from the context when available. Use markdown formatting (bold, bullet lists, code blocks) to structure your answer clearly.

Question: {$message}
PROMPT;

        try {
            return $this->request($prompt, $model, false);
        } catch (\Throwable $e) {
            throw new RuntimeException(ucfirst($this->name()) . ' chat failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function generateDocumentation(array $architectureData, string $type = 'architecture'): string
    {
        $prompt = (new DocumentationPrompt())->build($architectureData, $type);
        $model  = $this->config['model'] ?? '';

        try {
            return $this->request($prompt, $model, false);
        } catch (\Throwable $e) {
            throw new RuntimeException('Documentation generation failed: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function request(string $prompt, string $model, bool $jsonMode): string
    {
        $apiKey = $this->config['api_key'] ?? '';
        $model  = $model ?: ($this->config['model'] ?? '');

        if (empty($apiKey)) {
            throw new RuntimeException(
                ucfirst($this->name()) . ' API key is not configured. Set the api_key in your ai config.'
            );
        }

        if (empty($model)) {
            throw new RuntimeException(
                ucfirst($this->name()) . ' model is not configured. Set the model in your ai config.'
            );
        }

        $url  = rtrim($this->apiBase(), '/') . '/chat/completions';
        $body = [
            'model'       => $model,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => (float) ($this->config['temperature'] ?? 0.2),
            'max_tokens'  => 8192,
        ];

        if ($jsonMode) {
            $body['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post($url, $body);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException(ucfirst($this->name()) . " API error ({$response->status()}): {$error}");
        }

        $text = $response->json('choices.0.message.content');

        if ($text === null) {
            throw new RuntimeException(ucfirst($this->name()) . ' returned an empty response.');
        }

        return $text;
    }

    protected function extractJson(string $raw): array
    {
        $clean   = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $clean   = preg_replace('/\s*```$/m', '', $clean);
        $clean   = trim($clean);
        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (preg_match('/\{.*\}/s', $clean, $m)) {
                $decoded = json_decode($m[0], true);
            }
        }

        if (!is_array($decoded)) {
            throw new RuntimeException(
                'Could not parse JSON from ' . $this->name() . ' response: ' . substr($clean, 0, 200)
            );
        }

        return $decoded;
    }
}
