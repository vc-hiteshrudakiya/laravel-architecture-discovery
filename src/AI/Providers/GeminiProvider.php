<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

use Illuminate\Support\Facades\Http;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Contracts\AIProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\DTO\AIAnalysisResponse;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\ArchitectureReviewPrompt;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\DocumentationPrompt;
use RuntimeException;

class GeminiProvider implements AIProvider
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models';
    private const NAME     = 'gemini';

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
            $raw     = $this->request($prompt, $model, true);
            $parsed  = $this->extractJson($raw);
            return AIAnalysisResponse::fromArray($parsed, self::NAME, $model, $raw);
        } catch (\Throwable $e) {
            return AIAnalysisResponse::error('Gemini analysis failed: ' . $e->getMessage(), self::NAME, $model);
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
Answer the following question about this Laravel application. Be specific — reference actual class names, method counts, and URIs from the context when available. Use markdown formatting (bold, bullet lists, code blocks with backticks) to structure your answer clearly.

Question: {$message}
PROMPT;

        try {
            return $this->request($prompt, $model, false);
        } catch (\Throwable $e) {
            throw new RuntimeException('Gemini chat failed: ' . $e->getMessage(), 0, $e);
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
            throw new RuntimeException('Gemini API key is not configured. Set GEMINI_API_KEY in your .env file.');
        }

        if (empty($model)) {
            throw new RuntimeException('Gemini model is not configured. Set GEMINI_MODEL=gemini-2.5-flash in your .env file.');
        }

        $url  = self::API_BASE . "/{$model}:generateContent?key={$apiKey}";
        $body = [
            'contents' => [
                ['parts' => [['text' => $prompt]], 'role' => 'user'],
            ],
            'generationConfig' => [
                'temperature'     => (float) ($this->config['temperature'] ?? 0.2),
                'maxOutputTokens' => 8192,
            ],
        ];

        if ($jsonMode) {
            $body['generationConfig']['responseMimeType'] = 'application/json';
        }

        $response = Http::timeout(60)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $body);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException("Gemini API error ({$response->status()}): {$error}");
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if ($text === null) {
            throw new RuntimeException('Gemini returned an empty response. The prompt may have been blocked.');
        }

        return $text;
    }

    private function extractJson(string $raw): array
    {
        // Strip markdown fences if present
        $clean = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $clean = preg_replace('/\s*```$/m', '', $clean);
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Try to find first { ... } block
            if (preg_match('/\{.*\}/s', $clean, $m)) {
                $decoded = json_decode($m[0], true);
            }
        }

        if (!is_array($decoded)) {
            throw new RuntimeException('Could not parse JSON from Gemini response: ' . substr($clean, 0, 200));
        }

        return $decoded;
    }
}
