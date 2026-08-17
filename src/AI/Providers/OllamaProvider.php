<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaProvider extends OpenAICompatibleProvider
{
    protected function apiBase(): string
    {
        return rtrim($this->config['base_url'] ?? 'http://localhost:11434/v1', '/');
    }

    protected function name(): string { return 'ollama'; }

    protected function request(string $prompt, string $model, bool $jsonMode): string
    {
        $model = $model ?: ($this->config['model'] ?? '');

        if (empty($model)) {
            throw new RuntimeException('Ollama model is not configured. Set OLLAMA_MODEL in your .env file.');
        }

        $url  = $this->apiBase() . '/chat/completions';
        $body = [
            'model'       => $model,
            'messages'    => [['role' => 'user', 'content' => $prompt]],
            'temperature' => (float) ($this->config['temperature'] ?? 0.2),
            'stream'      => false,
        ];

        // Ollama uses 'format' not 'response_format'
        if ($jsonMode) {
            $body['format'] = 'json';
        }

        $headers = ['Content-Type' => 'application/json'];

        // API key is optional for Ollama (needed only when behind a proxy)
        $apiKey = $this->config['api_key'] ?? '';
        if (!empty($apiKey)) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        $response = Http::timeout(120)
            ->withHeaders($headers)
            ->post($url, $body);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException("Ollama API error ({$response->status()}): {$error}");
        }

        $text = $response->json('choices.0.message.content');

        if ($text === null) {
            throw new RuntimeException('Ollama returned an empty response. Is the model pulled and running?');
        }

        return $text;
    }
}
