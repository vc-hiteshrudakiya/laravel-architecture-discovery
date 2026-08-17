<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Interactive Dashboard
    |--------------------------------------------------------------------------
    | Accessible at /{path} — only registered when APP_ENV=local by default.
    */
    'dashboard' => [
        'enabled'    => true,
        'path'       => 'architecture',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | What to scan
    |--------------------------------------------------------------------------
    */
    'scan' => [
        'models'       => true,
        'controllers'  => true,
        'routes'       => true,
        'migrations'   => true,
        'dependencies' => true,
        'jobs'         => true,
        'events'       => true,
        'services'     => true,
        'repositories' => true,
        'observers'    => true,
        'policies'     => true,
        'modules'      => true,
        'packages'     => true,
        'api_docs'     => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom scan paths
    |--------------------------------------------------------------------------
    | Set to null to auto-detect.
    */
    'paths' => [
        'models'       => null,
        'controllers'  => null,
        'routes'       => null,
        'jobs'         => null,
        'events'       => null,
        'services'     => null,
        'repositories' => null,
        'observers'    => null,
        'policies'     => null,
        'modules'      => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | App namespace
    |--------------------------------------------------------------------------
    */
    'app_namespace' => null,

    /*
    |--------------------------------------------------------------------------
    | Include vendor / framework routes
    |--------------------------------------------------------------------------
    */
    'include_vendor_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Default output formats (artisan command)
    |--------------------------------------------------------------------------
    */
    'output' => ['json', 'html', 'markdown'],

    /*
    |--------------------------------------------------------------------------
    | AI Analysis
    |--------------------------------------------------------------------------
    | Enable AI-powered architecture insights via your chosen provider.
    |
    | Supported providers (set AI_PROVIDER in .env):
    |
    |   gemini      — Google Gemini         GEMINI_API_KEY      / GEMINI_MODEL
    |   openai      — OpenAI GPT            OPENAI_API_KEY      / OPENAI_MODEL
    |   anthropic   — Anthropic Claude      ANTHROPIC_API_KEY   / ANTHROPIC_MODEL
    |   mistral     — Mistral AI            MISTRAL_API_KEY     / MISTRAL_MODEL
    |   groq        — Groq (fast inference) GROQ_API_KEY        / GROQ_MODEL
    |   ollama      — Ollama (local)        OLLAMA_MODEL        / OLLAMA_BASE_URL (optional)
    |   openrouter  — OpenRouter gateway    OPENROUTER_API_KEY  / OPENROUTER_MODEL
    |
    | Recommended models per provider:
    |   gemini      → gemini-2.5-flash
    |   openai      → gpt-4o
    |   anthropic   → claude-sonnet-4-6
    |   mistral     → mistral-large-latest
    |   groq        → llama-3.3-70b-versatile
    |   ollama      → llama3.2  (or any model you have pulled)
    |   openrouter  → google/gemini-2.5-flash  (free — see openrouter.ai/models)
    |
    | OpenRouter provides access to 200+ models via a single API key.
    | Ideal for users who do not have a direct Gemini or OpenAI key.
    | Free models include: google/gemini-2.5-flash, meta-llama/llama-3.3-70b, etc.
    */
    'ai' => [
        'enabled'     => (bool) env('AI_ENABLED', false),
        'provider'    => env('AI_PROVIDER', 'gemini'),
        'temperature' => (float) env('AI_TEMPERATURE', 0.2),

        // Each provider reads its own key + model from env.
        // Only the active provider's values are used at runtime.
        'api_key' => match (env('AI_PROVIDER', 'gemini')) {
            'openai'     => env('OPENAI_API_KEY'),
            'anthropic'  => env('ANTHROPIC_API_KEY'),
            'mistral'    => env('MISTRAL_API_KEY'),
            'groq'       => env('GROQ_API_KEY'),
            'ollama'     => null,            // no key needed for local Ollama
            'openrouter' => env('OPENROUTER_API_KEY'),
            default      => env('GEMINI_API_KEY'),
        },

        'model' => match (env('AI_PROVIDER', 'gemini')) {
            'openai'     => env('OPENAI_MODEL'),
            'anthropic'  => env('ANTHROPIC_MODEL'),
            'mistral'    => env('MISTRAL_MODEL'),
            'groq'       => env('GROQ_MODEL'),
            'ollama'     => env('OLLAMA_MODEL'),
            'openrouter' => env('OPENROUTER_MODEL'),
            default      => env('GEMINI_MODEL'),
        },

        // Ollama only — base URL of the running Ollama server
        'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434/v1'),
    ],

];
