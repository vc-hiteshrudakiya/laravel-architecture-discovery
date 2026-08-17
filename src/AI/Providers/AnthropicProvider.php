<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

use Illuminate\Support\Facades\Http;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Contracts\AIProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\DTO\AIAnalysisResponse;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\ArchitectureReviewPrompt;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\DocumentationPrompt;
use RuntimeException;

class AnthropicProvider implements AIProvider
{
    private const API_BASE = 'https://api.anthropic.com/v1';
    private const NAME     = 'anthropic';

    // Minimum API version that supports the Messages endpoint
    private const API_VERSION = '2023-06-01';

    public function __construct(private readonly array $config) {}

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
            return AIAnalysisResponse::fromArray($parsed, self::NAME, $model, $raw);
        } catch (\Throwable $e) {
            return AIAnalysisResponse::error('Anthropic analysis failed: ' . $e->getMessage(), self::NAME, $model);
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
            throw new RuntimeException('Anthropic chat failed: ' . $e->getMessage(), 0, $e);
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

    private function request(string $prompt, string $model, bool $jsonMode): string
    {
        $apiKey = $this->config['api_key'] ?? '';
        $model  = $model ?: ($this->config['model'] ?? '');

        if (empty($apiKey)) {
            throw new RuntimeException(
                'Anthropic API key is not configured. Set ANTHROPIC_API_KEY in your .env file.'
            );
        }

        if (empty($model)) {
            throw new RuntimeException(
                'Anthropic model is not configured. Set ANTHROPIC_MODEL=claude-sonnet-4-6 in your .env file.'
            );
        }

        $body = [
            'model'      => $model,
            'max_tokens' => 8192,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ];

        // Anthropic has no response_format field; use a system prompt to enforce JSON output
        if ($jsonMode) {
            $body['system'] = 'You are a JSON API. Respond only with a valid JSON object — no markdown fences, no explanation, no text outside the JSON.';
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => self::API_VERSION,
                'Content-Type'      => 'application/json',
            ])
            ->post(self::API_BASE . '/messages', $body);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException("Anthropic API error ({$response->status()}): {$error}");
        }

        $text = $response->json('content.0.text');

        if ($text === null) {
            throw new RuntimeException('Anthropic returned an empty response. The prompt may have been blocked.');
        }

        return $text;
    }

    private function extractJson(string $raw): array
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
                'Could not parse JSON from Anthropic response: ' . substr($clean, 0, 200)
            );
        }

        return $decoded;
    }
}
