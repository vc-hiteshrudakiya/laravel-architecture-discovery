<?php

namespace Viitorcloud\LaravelArchitectureDiscovery;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\ControllerAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\DependencyAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\EventAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\JobAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\ModelAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\ObserverAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\ApiDocAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\ModuleAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\PackageDetector;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\PolicyAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\RouteAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\Analyzers\ServiceAnalyzer;
use Viitorcloud\LaravelArchitectureDiscovery\AI\AIManager;
use Viitorcloud\LaravelArchitectureDiscovery\Http\Controllers\AIController;
use Viitorcloud\LaravelArchitectureDiscovery\Http\Controllers\DashboardController;
use Viitorcloud\LaravelArchitectureDiscovery\Http\Controllers\ExportController;
use Viitorcloud\LaravelArchitectureDiscovery\Services\ArchitectureScanner;
use Viitorcloud\LaravelArchitectureDiscovery\Services\ReportExporter;

class ArchitectureDiscoveryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/architecture-discovery.php',
            'architecture-discovery'
        );

        $this->app->singleton(ArchitectureScanner::class, function ($app) {
            $config = $app['config']->get('architecture-discovery', []);
            $scan   = $config['scan'] ?? [];
            $paths  = $config['paths'] ?? [];

            $appNamespace = $config['app_namespace'] ?? $this->detectAppNamespace();

            $analyzers = [];

            if ($scan['models'] ?? true) {
                $analyzers['models'] = new ModelAnalyzer(
                    $paths['models'] ?? $this->detectModelsPath()
                );
            }

            if ($scan['controllers'] ?? true) {
                $analyzers['controllers'] = new ControllerAnalyzer(
                    $paths['controllers'] ?? app_path('Http/Controllers')
                );
            }

            if ($scan['routes'] ?? true) {
                $analyzers['routes'] = new RouteAnalyzer($appNamespace);
            }

            if ($scan['dependencies'] ?? true) {
                $analyzers['dependencies'] = new DependencyAnalyzer(app_path());
            }

            if ($scan['jobs'] ?? true) {
                $analyzers['jobs'] = new JobAnalyzer(
                    $paths['jobs'] ?? app_path('Jobs')
                );
            }

            if ($scan['events'] ?? true) {
                $analyzers['events'] = new EventAnalyzer(
                    $paths['events'] ?? app_path('Events')
                );
            }

            if ($scan['services'] ?? true) {
                $analyzers['services'] = new ServiceAnalyzer(
                    $paths['services'] ?? app_path('Services'),
                    'Service'
                );
            }

            if ($scan['repositories'] ?? true) {
                $analyzers['repositories'] = new ServiceAnalyzer(
                    $paths['repositories'] ?? app_path('Repositories'),
                    'Repository'
                );
            }

            if ($scan['observers'] ?? true) {
                $analyzers['observers'] = new ObserverAnalyzer(
                    $paths['observers'] ?? app_path('Observers')
                );
            }

            if ($scan['policies'] ?? true) {
                $analyzers['policies'] = new PolicyAnalyzer(
                    $paths['policies'] ?? app_path('Policies')
                );
            }

            if ($scan['modules'] ?? true) {
                $analyzers['modules'] = new ModuleAnalyzer(
                    $paths['modules'] ?? $this->detectModulesPath()
                );
            }

            if ($scan['packages'] ?? true) {
                $analyzers['packages'] = new PackageDetector(base_path());
            }

            if ($scan['api_docs'] ?? true) {
                $analyzers['api_docs'] = new ApiDocAnalyzer(
                    $appNamespace,
                    app_path()
                );
            }

            return new ArchitectureScanner($analyzers);
        });

        $this->app->singleton(ArchitectureDiscovery::class, function ($app) {
            return new ArchitectureDiscovery($app->make(ArchitectureScanner::class));
        });

        $this->app->singleton(ReportExporter::class, fn() => new ReportExporter());

        $this->app->singleton(AIManager::class, function ($app) {
            return new AIManager(
                $app['config']->get('architecture-discovery.ai', [])
            );
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'architecture-discovery');

        $this->registerDashboardRoute();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\DiscoverArchitectureCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/architecture-discovery.php' => config_path('architecture-discovery.php'),
            ], 'architecture-discovery-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/architecture-discovery'),
            ], 'architecture-discovery-views');
        }
    }

    private function registerDashboardRoute(): void
    {
        $config = config('architecture-discovery.dashboard', []);

        if (!($config['enabled'] ?? true)) {
            return;
        }

        if (!$this->app->environment('local', 'development')) {
            return;
        }

        $path       = $config['path']       ?? 'architecture';
        $middleware = $config['middleware']  ?? ['web'];

        Route::middleware($middleware)
            ->get($path, DashboardController::class)
            ->name('architecture.dashboard');

        Route::middleware($middleware)
            ->get($path . '/export/{format}', ExportController::class)
            ->name('architecture.export')
            ->where('format', 'html|svg');

        Route::middleware($middleware)->group(function () use ($path) {
            Route::post($path . '/ai/analyze',       [AIController::class, 'analyze'])->name('architecture.ai.analyze');
            Route::post($path . '/ai/chat',          [AIController::class, 'chat'])->name('architecture.ai.chat');
            Route::post($path . '/ai/documentation', [AIController::class, 'documentation'])->name('architecture.ai.documentation');
        });
    }

    private function detectModulesPath(): string
    {
        foreach (['Modules', 'modules', 'src/Modules'] as $dir) {
            $path = base_path($dir);
            if (is_dir($path)) return $path;
        }
        return base_path('Modules');
    }

    private function detectModelsPath(): string
    {
        if (is_dir(app_path('Models'))) {
            return app_path('Models');
        }

        return app_path();
    }

    private function detectAppNamespace(): string
    {
        try {
            $composer = json_decode(
                file_get_contents(base_path('composer.json')),
                true
            );

            foreach ($composer['autoload']['psr-4'] ?? [] as $namespace => $path) {
                foreach ((array) $path as $p) {
                    if (rtrim($p, '/') === 'app') {
                        return rtrim($namespace, '\\');
                    }
                }
            }
        } catch (\Throwable $e) {
            // fall through to default
        }

        return 'App';
    }
}
