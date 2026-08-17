# Laravel Architecture Discovery

[![Latest Version on Packagist](https://img.shields.io/packagist/v/viitorcloud/laravel-architecture-discovery.svg?style=flat-square)](https://packagist.org/packages/viitorcloud/laravel-architecture-discovery)
[![Total Downloads](https://img.shields.io/packagist/dt/viitorcloud/laravel-architecture-discovery.svg?style=flat-square)](https://packagist.org/packages/viitorcloud/laravel-architecture-discovery)
[![License](https://img.shields.io/packagist/l/viitorcloud/laravel-architecture-discovery.svg?style=flat-square)](https://packagist.org/packages/viitorcloud/laravel-architecture-discovery)
[![PHP Version](https://img.shields.io/packagist/php-v/viitorcloud/laravel-architecture-discovery.svg?style=flat-square)](https://packagist.org/packages/viitorcloud/laravel-architecture-discovery)

Automatically **discover, visualize, and document** your Laravel application architecture — without writing a single line of configuration.

- Interactive dashboard with dependency graph, ER diagram, and route explorer
- Exportable reports in **HTML**, **JSON**, and **Markdown**
- Scans models, controllers, routes, migrations, jobs, events, services, repositories, observers, policies, modules, and packages
- Optional **AI-powered architecture review** (OpenAI, Anthropic, Gemini, Groq, Mistral, Ollama, OpenRouter)

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.1 |
| Laravel | 10, 11, 12, or 13 |

---

## Installation

```bash
composer require viitorcloud/laravel-architecture-discovery
```

Publish the config file:

```bash
php artisan vendor:publish --tag=architecture-discovery-config
```

---

## Interactive Dashboard

Visit `/architecture` in your browser (only available when `APP_ENV=local` by default):

```
http://your-app.test/architecture
```

The dashboard provides:
- **Overview** — component counts, architecture score, and health checks
- **Models** — relationships, fillable fields, table mappings
- **Controllers** — methods, routes, dependency graph
- **Routes** — full route list with methods, middleware, and controllers
- **Dependency Graph** — interactive SVG graph showing class-level dependencies
- **ER Diagram** — auto-generated entity-relationship diagram from Eloquent models
- **Jobs / Events / Services / Repositories / Observers / Policies** — per-component explorer
- **AI Insights** — AI-powered review of your architecture (requires AI config)

---

## Artisan Command

Generate reports from the command line:

```bash
# Generate all formats (json, html, markdown)
php artisan architecture:discover

# Generate specific formats
php artisan architecture:discover --format=html
php artisan architecture:discover --format=json
php artisan architecture:discover --format=markdown

# Include AI analysis
php artisan architecture:discover --ai
```

Reports are saved to `storage/app/architecture/`.

---

## Configuration

```php
// config/architecture-discovery.php

return [
    'dashboard' => [
        'enabled'    => true,
        'path'       => 'architecture',   // URL path
        'middleware' => ['web'],
    ],

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
    ],

    // AI analysis (optional)
    'ai' => [
        'enabled'  => env('AI_ENABLED', false),
        'provider' => env('AI_PROVIDER', 'gemini'),
    ],
];
```

---

## AI Analysis

Add to your `.env`:

```env
AI_ENABLED=true

# Choose one provider:

# Google Gemini
AI_PROVIDER=gemini
GEMINI_API_KEY=your-key
GEMINI_MODEL=gemini-2.0-flash

# OpenAI
AI_PROVIDER=openai
OPENAI_API_KEY=your-key
OPENAI_MODEL=gpt-4o-mini

# Anthropic Claude
AI_PROVIDER=anthropic
ANTHROPIC_API_KEY=your-key
ANTHROPIC_MODEL=claude-3-5-haiku-20241022

# Groq (fast & free tier)
AI_PROVIDER=groq
GROQ_API_KEY=your-key
GROQ_MODEL=llama-3.3-70b-versatile

# Mistral
AI_PROVIDER=mistral
MISTRAL_API_KEY=your-key
MISTRAL_MODEL=mistral-small-latest

# Ollama (local)
AI_PROVIDER=ollama
OLLAMA_MODEL=llama3
OLLAMA_BASE_URL=http://localhost:11434/v1

# OpenRouter (200+ models, free tier available)
AI_PROVIDER=openrouter
OPENROUTER_API_KEY=your-key
OPENROUTER_MODEL=google/gemini-2.0-flash-exp:free
```

---

## What Gets Scanned

| Component | What is detected |
|---|---|
| **Models** | Table, fillable, hidden, casts, relationships, observers |
| **Controllers** | Methods, route bindings, dependencies |
| **Routes** | URI, method, middleware, name, controller |
| **Migrations** | Table name, columns, indexes |
| **Jobs** | Queue connection, class hierarchy |
| **Events** | Listeners, broadcast channels |
| **Services** | Service classes in `App\Services` |
| **Repositories** | Repository classes in `App\Repositories` |
| **Observers** | Observed models, event hooks |
| **Policies** | Guarded models, defined abilities |
| **Modules** | Laravel module detection (nwidart/laravel-modules compatible) |
| **Packages** | Installed Composer packages with version info |

---

## License

MIT — see [LICENSE](LICENSE) for details.
