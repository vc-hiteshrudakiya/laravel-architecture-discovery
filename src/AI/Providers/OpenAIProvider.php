<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Providers;

class OpenAIProvider extends OpenAICompatibleProvider
{
    private const API_BASE = 'https://api.openai.com/v1';

    protected function apiBase(): string { return self::API_BASE; }
    protected function name(): string    { return 'openai'; }
}
