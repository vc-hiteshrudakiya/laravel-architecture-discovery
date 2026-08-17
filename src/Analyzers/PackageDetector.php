<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

class PackageDetector
{
    private const KNOWN_PACKAGES = [
        // ── Authentication & Auth Scaffolding ─────────────────────
        'laravel/breeze' => [
            'name'        => 'Laravel Breeze',
            'category'    => 'Auth Scaffolding',
            'description' => 'Minimal authentication starter kit with Blade, Livewire, or Inertia.',
            'color'       => 'pink',
            'docs'        => 'https://laravel.com/docs/starter-kits#breeze',
        ],
        'laravel/jetstream' => [
            'name'        => 'Laravel Jetstream',
            'category'    => 'Auth Scaffolding',
            'description' => 'Feature-rich starter kit with teams, 2FA, and API support.',
            'color'       => 'purple',
            'docs'        => 'https://jetstream.laravel.com',
        ],
        'laravel/passport' => [
            'name'        => 'Laravel Passport',
            'category'    => 'API Authentication',
            'description' => 'Full OAuth2 server implementation for API authentication.',
            'color'       => 'red',
            'docs'        => 'https://laravel.com/docs/passport',
        ],
        'laravel/sanctum' => [
            'name'        => 'Laravel Sanctum',
            'category'    => 'API Authentication',
            'description' => 'Lightweight API token and SPA authentication.',
            'color'       => 'blue',
            'docs'        => 'https://laravel.com/docs/sanctum',
        ],
        'tymon/jwt-auth' => [
            'name'        => 'JWT Auth',
            'category'    => 'API Authentication',
            'description' => 'JSON Web Token authentication for Laravel.',
            'color'       => 'orange',
            'docs'        => 'https://jwt-auth.readthedocs.io',
        ],

        // ── UI Frameworks ─────────────────────────────────────────
        'livewire/livewire' => [
            'name'        => 'Livewire',
            'category'    => 'UI Framework',
            'description' => 'Full-stack framework for dynamic interfaces without writing JavaScript.',
            'color'       => 'pink',
            'docs'        => 'https://livewire.laravel.com',
        ],
        'inertiajs/inertia-laravel' => [
            'name'        => 'Inertia.js',
            'category'    => 'UI Framework',
            'description' => 'Build single-page apps using classic server-side routing.',
            'color'       => 'violet',
            'docs'        => 'https://inertiajs.com',
        ],

        // ── Admin Panels ──────────────────────────────────────────
        'filament/filament' => [
            'name'        => 'Filament',
            'category'    => 'Admin Panel',
            'description' => 'Rapid admin panel and CRUD builder for Laravel.',
            'color'       => 'amber',
            'docs'        => 'https://filamentphp.com',
        ],
        'laravel/nova' => [
            'name'        => 'Laravel Nova',
            'category'    => 'Admin Panel',
            'description' => 'Premium administration panel crafted by the Laravel team.',
            'color'       => 'sky',
            'docs'        => 'https://nova.laravel.com',
        ],
        'backpack/crud' => [
            'name'        => 'Backpack for Laravel',
            'category'    => 'Admin Panel',
            'description' => 'Admin panel toolkit with drag-and-drop CRUD builder.',
            'color'       => 'blue',
            'docs'        => 'https://backpackforlaravel.com',
        ],

        // ── Authorization ─────────────────────────────────────────
        'spatie/laravel-permission' => [
            'name'        => 'Spatie Permission',
            'category'    => 'Authorization',
            'description' => 'Associate users with roles and permissions.',
            'color'       => 'emerald',
            'docs'        => 'https://spatie.be/docs/laravel-permission',
        ],
        'silber/bouncer' => [
            'name'        => 'Bouncer',
            'category'    => 'Authorization',
            'description' => 'Eloquent roles and abilities for Laravel.',
            'color'       => 'green',
            'docs'        => 'https://github.com/JosephSilber/bouncer',
        ],

        // ── Monitoring & Debug ────────────────────────────────────
        'laravel/horizon' => [
            'name'        => 'Laravel Horizon',
            'category'    => 'Queue Monitoring',
            'description' => 'Beautiful dashboard for monitoring Redis queues.',
            'color'       => 'teal',
            'docs'        => 'https://laravel.com/docs/horizon',
        ],
        'laravel/telescope' => [
            'name'        => 'Laravel Telescope',
            'category'    => 'Debug',
            'description' => 'Elegant debug assistant — inspect requests, jobs, queries, and more.',
            'color'       => 'slate',
            'docs'        => 'https://laravel.com/docs/telescope',
        ],
        'barryvdh/laravel-debugbar' => [
            'name'        => 'Laravel Debugbar',
            'category'    => 'Debug',
            'description' => 'Integrates PHP Debug Bar for profiling and debugging.',
            'color'       => 'orange',
            'docs'        => 'https://github.com/barryvdh/laravel-debugbar',
        ],

        // ── Media & Storage ───────────────────────────────────────
        'spatie/laravel-medialibrary' => [
            'name'        => 'Spatie Media Library',
            'category'    => 'Media',
            'description' => 'Associate files and images with Eloquent models.',
            'color'       => 'cyan',
            'docs'        => 'https://spatie.be/docs/laravel-medialibrary',
        ],
        'intervention/image' => [
            'name'        => 'Intervention Image',
            'category'    => 'Media',
            'description' => 'PHP image processing and manipulation library.',
            'color'       => 'green',
            'docs'        => 'https://image.intervention.io',
        ],

        // ── Search ────────────────────────────────────────────────
        'laravel/scout' => [
            'name'        => 'Laravel Scout',
            'category'    => 'Search',
            'description' => 'Driver-based full-text search for Eloquent models.',
            'color'       => 'indigo',
            'docs'        => 'https://laravel.com/docs/scout',
        ],
        'algolia/algoliasearch-client-php' => [
            'name'        => 'Algolia',
            'category'    => 'Search',
            'description' => 'Hosted search engine with Laravel Scout integration.',
            'color'       => 'blue',
            'docs'        => 'https://www.algolia.com/doc',
        ],

        // ── Payments ─────────────────────────────────────────────
        'laravel/cashier' => [
            'name'        => 'Laravel Cashier (Stripe)',
            'category'    => 'Payments',
            'description' => 'Expressive interface to Stripe\'s subscription billing.',
            'color'       => 'violet',
            'docs'        => 'https://laravel.com/docs/billing',
        ],
        'laravel/cashier-paddle' => [
            'name'        => 'Laravel Cashier (Paddle)',
            'category'    => 'Payments',
            'description' => 'Expressive interface to Paddle\'s subscription billing.',
            'color'       => 'blue',
            'docs'        => 'https://laravel.com/docs/cashier-paddle',
        ],

        // ── Utilities ─────────────────────────────────────────────
        'spatie/laravel-activitylog' => [
            'name'        => 'Spatie Activity Log',
            'category'    => 'Audit',
            'description' => 'Log activity in your Laravel application.',
            'color'       => 'rose',
            'docs'        => 'https://spatie.be/docs/laravel-activitylog',
        ],
        'spatie/laravel-backup' => [
            'name'        => 'Spatie Backup',
            'category'    => 'Backup',
            'description' => 'Create backups of your application and databases.',
            'color'       => 'green',
            'docs'        => 'https://spatie.be/docs/laravel-backup',
        ],
        'barryvdh/laravel-dompdf' => [
            'name'        => 'Laravel DomPDF',
            'category'    => 'PDF',
            'description' => 'Generate PDF files from Blade views.',
            'color'       => 'red',
            'docs'        => 'https://github.com/barryvdh/laravel-dompdf',
        ],
        'maatwebsite/excel' => [
            'name'        => 'Laravel Excel',
            'category'    => 'Import / Export',
            'description' => 'Import and export Excel and CSV files elegantly.',
            'color'       => 'emerald',
            'docs'        => 'https://laravel-excel.com',
        ],
        'nwidart/laravel-modules' => [
            'name'        => 'Laravel Modules',
            'category'    => 'Architecture',
            'description' => 'Module system for large-scale modular Laravel applications.',
            'color'       => 'indigo',
            'docs'        => 'https://nwidart.com/laravel-modules',
        ],
        'laravel/socialite' => [
            'name'        => 'Laravel Socialite',
            'category'    => 'Auth Scaffolding',
            'description' => 'OAuth authentication with Google, GitHub, Facebook, and more.',
            'color'       => 'sky',
            'docs'        => 'https://laravel.com/docs/socialite',
        ],
    ];

    public function __construct(private string $basePath) {}

    public function analyze(): array
    {
        $composerJson = $this->readJson($this->basePath . '/composer.json');
        if (!$composerJson) {
            return ['items' => [], 'errors' => []];
        }

        $composerLock  = $this->readJson($this->basePath . '/composer.lock');
        $lockVersions  = $this->buildLockVersionMap($composerLock);

        $installed = array_merge(
            array_keys($composerJson['require']      ?? []),
            array_keys($composerJson['require-dev']  ?? []),
        );
        $installed = array_map('strtolower', $installed);

        $items = [];
        foreach (self::KNOWN_PACKAGES as $packageKey => $meta) {
            if (!in_array($packageKey, $installed, true)) continue;

            $version = $lockVersions[$packageKey] ?? $this->constraintFromJson($composerJson, $packageKey);

            $items[] = [
                'key'         => $packageKey,
                'name'        => $meta['name'],
                'category'    => $meta['category'],
                'description' => $meta['description'],
                'color'       => $meta['color'],
                'docs'        => $meta['docs'],
                'version'     => $version,
                'dev'         => $this->isDevOnly($composerJson, $packageKey),
            ];
        }

        // Sort by category then name
        usort($items, fn($a, $b) => $a['category'] <=> $b['category'] ?: $a['name'] <=> $b['name']);

        return ['items' => $items, 'errors' => []];
    }

    private function readJson(string $path): ?array
    {
        if (!file_exists($path)) return null;
        $decoded = json_decode(file_get_contents($path), true);
        return is_array($decoded) ? $decoded : null;
    }

    private function buildLockVersionMap(?array $lock): array
    {
        if (!$lock) return [];

        $map = [];
        foreach (array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []) as $pkg) {
            $map[strtolower($pkg['name'])] = ltrim($pkg['version'] ?? '', 'v');
        }
        return $map;
    }

    private function constraintFromJson(array $json, string $key): ?string
    {
        return $json['require'][$key]
            ?? $json['require-dev'][$key]
            ?? null;
    }

    private function isDevOnly(array $json, string $key): bool
    {
        return isset($json['require-dev'][$key]) && !isset($json['require'][$key]);
    }
}
