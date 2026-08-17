<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

class MistralProvider extends OpenAICompatibleProvider
{
    private const API_BASE = 'https://api.mistral.ai/v1';

    protected function apiBase(): string { return self::API_BASE; }
    protected function name(): string    { return 'mistral'; }
}
