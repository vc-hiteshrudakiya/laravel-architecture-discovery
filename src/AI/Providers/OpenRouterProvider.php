<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

use Illuminate\Support\Facades\Http;
use Viitorcloud\LaravelArchitectureDiscovery\AI\DTO\AIAnalysisResponse;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts\ArchitectureReviewPrompt;
use RuntimeException;

/**
 * OpenRouter provider — a unified gateway to 200+ models (Gemini, GPT-4, Claude, Llama, etc.)
 * via a single API key. Uses the OpenAI chat-completions format.
 *
 * Required .env:
 *   AI_PROVIDER=openrouter
 *   OPENROUTER_API_KEY=sk-or-xxxxxxxxxxxxxxxx
 *   OPENROUTER_MODEL=google/gemini-2.5-flash   (or any model on openrouter.ai/models)
 *
 * Free models available: google/gemini-2.5-flash, meta-llama/llama-3.3-70b, etc.
 */
class OpenRouterProvider extends OpenAICompatibleProvider
{
    private const API_BASE   = 'https://openrouter.ai/api/v1';
    private const SITE_TITLE = 'Laravel Architecture Discovery';

    protected function apiBase(): string { return self::API_BASE; }
    protected function name(): string    { return 'openrouter'; }

    /**
     * Override request to add the HTTP-Referer and X-Title headers that OpenRouter
     * uses for model routing analytics, and to skip response_format since not all
     * OpenRouter models support it — JSON output is enforced via system prompt instead.
     */
    protected function request(string $prompt, string $model, bool $jsonMode): string
    {
        $apiKey       = $this->config['api_key'] ?? '';
        $resolvedModel = $model ?: ($this->config['model'] ?? '');

        if (empty($apiKey)) {
            throw new RuntimeException(
                'OpenRouter API key is not configured. Set OPENROUTER_API_KEY in your .env file.'
            );
        }

        if (empty($resolvedModel)) {
            throw new RuntimeException(
                'OpenRouter model is not configured. Set OPENROUTER_MODEL=google/gemini-2.5-flash in your .env file.'
            );
        }

        $messages = [];

        // Use a system message to enforce JSON output instead of response_format,
        // because many OpenRouter models do not support the response_format parameter.
        if ($jsonMode) {
            $messages[] = [
                'role'    => 'system',
                'content' => 'You are a JSON API. Respond only with a valid JSON object — no markdown fences, no explanation, no text outside the JSON.',
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $prompt];

        $body = [
            'model'       => $resolvedModel,
            'messages'    => $messages,
            'temperature' => (float) ($this->config['temperature'] ?? 0.2),
            'max_tokens'  => 8192,
        ];

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => $this->config['site_url'] ?? config('app.url', 'https://laravel.com'),
                'X-Title'       => self::SITE_TITLE,
            ])
            ->post(self::API_BASE . '/chat/completions', $body);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException("OpenRouter API error ({$response->status()}): {$error}");
        }

        $text = $response->json('choices.0.message.content');

        if ($text === null) {
            throw new RuntimeException('OpenRouter returned an empty response. The model may not be available or the prompt was blocked.');
        }

        // Strip <think>...</think> blocks that chain-of-thought models emit before their answer
        $text = preg_replace('/<think>.*?<\/think>/si', '', $text);

        return trim($text);
    }
}
