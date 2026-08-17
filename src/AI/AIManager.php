<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI;

use Viitorcloud\LaravelArchitectureDiscovery\AI\Contracts\AIProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\DTO\AIAnalysisResponse;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\AnthropicProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\GeminiProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\GroqProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\MistralProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\OllamaProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\OpenAIProvider;
use Viitorcloud\LaravelArchitectureDiscovery\AI\Providers\OpenRouterProvider;
use RuntimeException;

class AIManager
{
    private ?AIProvider $resolvedProvider = null;

    /** Custom providers registered via extend(), keyed by name. */
    private array $customProviders = [];

    public function __construct(private readonly array $config) {}

    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }

    public function analyze(array $architectureData): AIAnalysisResponse
    {
        return $this->provider()->analyze($architectureData);
    }

    public function reviewArchitecture(array $architectureData): AIAnalysisResponse
    {
        return $this->provider()->reviewArchitecture($architectureData);
    }

    public function chat(string $message, array $context = []): string
    {
        return $this->provider()->chat($message, $context);
    }

    public function generateDocumentation(array $architectureData, string $type = 'architecture'): string
    {
        return $this->provider()->generateDocumentation($architectureData, $type);
    }

    public function provider(): AIProvider
    {
        if ($this->resolvedProvider === null) {
            $this->resolvedProvider = $this->makeProvider($this->config['provider'] ?? 'gemini');
        }

        return $this->resolvedProvider;
    }

    /**
     * Register a custom provider by name. If the registered name matches the
     * currently configured provider it is used immediately; otherwise it is
     * stored and will be resolved when provider() is next called with that name.
     */
    public function extend(string $name, AIProvider $provider): void
    {
        $this->customProviders[$name] = $provider;

        // If this name is the active provider, apply it right away.
        if (($this->config['provider'] ?? 'gemini') === $name) {
            $this->resolvedProvider = $provider;
        }
    }

    private function makeProvider(string $name): AIProvider
    {
        // Custom providers registered via extend() take priority.
        if (isset($this->customProviders[$name])) {
            return $this->customProviders[$name];
        }

        return match ($name) {
            'gemini'     => new GeminiProvider($this->config),
            'openai'     => new OpenAIProvider($this->config),
            'anthropic'  => new AnthropicProvider($this->config),
            'mistral'    => new MistralProvider($this->config),
            'groq'       => new GroqProvider($this->config),
            'ollama'     => new OllamaProvider($this->config),
            'openrouter' => new OpenRouterProvider($this->config),
            default      => throw new RuntimeException(
                "AI provider \"{$name}\" is not supported. Available: gemini, openai, anthropic, mistral, groq, ollama, openrouter"
            ),
        };
    }
}
