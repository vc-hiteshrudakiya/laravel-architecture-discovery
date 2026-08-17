<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

class DependencyAnalyzer
{
    public function __construct(private string $appPath) {}

    public function analyze(): array
    {
        if (!is_dir($this->appPath)) {
            return ['nodes' => [], 'edges' => [], 'errors' => []];
        }

        $nodes  = [];
        $errors = [];

        // Pass 1 — register every app class with its layer
        foreach ($this->phpFiles() as $file) {
            try {
                $content = file_get_contents($file);
                $name    = $this->className($content);
                if (!$name) continue;

                $layer = $this->detectLayer($name, $file, $content);
                if ($layer === 'other') continue;

                $nodes[$name] = [
                    'name'        => $name,
                    'layer'       => $layer,
                    'file'        => ltrim(str_replace(base_path(), '', $file), DIRECTORY_SEPARATOR),
                    '_ctorDeps'   => $this->extractConstructorDeps($content),
                    '_handleDeps' => $this->extractHandleDeps($content),
                    '_usedModels' => $this->extractUsedClasses($content),
                    '_methodDeps' => $this->extractMethodBodyDeps($content),
                ];
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file, 'message' => $e->getMessage()];
            }
        }

        // Add the virtual Database node if any models were found
        $hasModels = !empty(array_filter($nodes, fn($n) => $n['layer'] === 'model'));
        if ($hasModels) {
            $nodes['Database'] = [
                'name'        => 'Database',
                'layer'       => 'database',
                'file'        => '',
                '_ctorDeps'   => [],
                '_handleDeps' => [],
                '_usedModels' => [],
            ];
        }

        // Pass 2 — resolve edges
        $edgeSet = [];

        foreach ($nodes as $name => $node) {
            if ($name === 'Database') continue;

            // Constructor injection → injects edges (Controller→Service, Service→Repo, Job→Service …)
            foreach ($node['_ctorDeps'] as $dep) {
                $short = class_basename($dep);
                if (isset($nodes[$short]) && $short !== $name) {
                    $this->addEdge($edgeSet, $name, $short, 'injects');
                }
            }

            // Listener handle(SomeEvent $e) → Event → Listener (dispatch direction)
            if ($node['layer'] === 'listener') {
                foreach ($node['_handleDeps'] as $dep) {
                    $short = class_basename($dep);
                    if (isset($nodes[$short]) && $nodes[$short]['layer'] === 'event') {
                        $this->addEdge($edgeSet, $short, $name, 'triggers');
                    }
                }
            }

            // Controller / Repository / Service → Model
            // Also covers controllers that use models directly (Eloquent static calls, imports)
            if (in_array($node['layer'], ['controller', 'repository', 'service'])) {
                foreach ($node['_usedModels'] as $used) {
                    if (isset($nodes[$used]) && $nodes[$used]['layer'] === 'model') {
                        $this->addEdge($edgeSet, $name, $used, 'uses');
                    }
                }
            }

            // Controller / Service → Service / Repository via method-body type hints and calls
            if (in_array($node['layer'], ['controller', 'service', 'job'])) {
                foreach ($node['_methodDeps'] as $dep) {
                    $short = class_basename($dep);
                    if (isset($nodes[$short]) && $short !== $name) {
                        $this->addEdge($edgeSet, $name, $short, 'uses');
                    }
                }
            }

            // Model → Database (all models persist to DB)
            if ($node['layer'] === 'model' && $hasModels) {
                $this->addEdge($edgeSet, $name, 'Database', 'persists');
            }
        }

        // Strip internal fields before returning
        foreach ($nodes as $name => $_) {
            unset(
                $nodes[$name]['_ctorDeps'],
                $nodes[$name]['_handleDeps'],
                $nodes[$name]['_usedModels'],
                $nodes[$name]['_methodDeps'],
            );
        }

        return [
            'nodes'  => array_values($nodes),
            'edges'  => array_values($edgeSet),
            'errors' => $errors,
        ];
    }

    private function addEdge(array &$set, string $from, string $to, string $type): void
    {
        $key = $from . '|' . $to . '|' . $type;
        if (!isset($set[$key])) {
            $set[$key] = ['from' => $from, 'to' => $to, 'type' => $type];
        }
    }

    private function phpFiles(): \Generator
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->appPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() === 'php') {
                yield $file->getPathname();
            }
        }
    }

    private function className(string $content): ?string
    {
        return preg_match('/\bclass\s+(\w+)/', $content, $m) ? $m[1] : null;
    }

    private function detectLayer(string $name, string $file, string $content): string
    {
        $sep = DIRECTORY_SEPARATOR;

        // Services & repositories — check first (most specific)
        if (str_ends_with($name, 'Service'))    return 'service';
        if (str_ends_with($name, 'Repository')) return 'repository';

        // Listeners, jobs, events
        if (str_ends_with($name, 'Listener'))                                         return 'listener';
        if (str_ends_with($name, 'Job') || str_contains($file, "{$sep}Jobs{$sep}"))   return 'job';
        if (str_ends_with($name, 'Event') || str_contains($file, "{$sep}Events{$sep}")) return 'event';

        // Eloquent models
        if (str_contains($file, "{$sep}Models{$sep}") ||
            preg_match('/extends\s+(?:Eloquent\\\\)?Model\b/', $content)) {
            return 'model';
        }

        // Standard Laravel controllers
        if (str_ends_with($name, 'Controller')) return 'controller';

        // Filament pages, resources, widgets, actions — treat as controller layer
        if (str_contains($file, "{$sep}Filament{$sep}") ||
            str_contains($file, "{$sep}Livewire{$sep}") ||
            str_ends_with($name, 'Page')      ||
            str_ends_with($name, 'Resource')  ||
            str_ends_with($name, 'Widget')    ||
            str_ends_with($name, 'Component') ||
            preg_match('/extends\s+(?:Page|Resource|Widget|Component|ListRecords|CreateRecord|EditRecord|ViewRecord|ManageRecords)\b/', $content)) {
            return 'controller';
        }

        return 'other';
    }

    private function extractConstructorDeps(string $content): array
    {
        if (!preg_match('/public\s+function\s+__construct\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        preg_match_all('/\b([A-Z][\w\\\\]+)\s+\$/', $m[1], $hits);
        return array_unique($hits[1] ?? []);
    }

    private function extractHandleDeps(string $content): array
    {
        if (!preg_match('/public\s+function\s+handle\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        preg_match_all('/\b([A-Z][\w\\\\]+)\s+\$/', $m[1], $hits);
        return array_unique($hits[1] ?? []);
    }

    private function extractUsedClasses(string $content): array
    {
        $found = [];

        // use App\Models\User; → User
        preg_match_all('/^use\s+(?:[A-Za-z0-9\\\\]+\\\\)?([A-Z][A-Za-z0-9]+)(?:\s+as\s+\w+)?\s*;/m', $content, $m);
        array_push($found, ...($m[1] ?? []));

        // Static Eloquent calls: User::find(), User::all(), User::where(), etc.
        preg_match_all(
            '/\b([A-Z][A-Za-z0-9]+)::(?:find|findOrFail|all|where|with|create|firstOrCreate|updateOrCreate|first|firstOrFail|get|select|orderBy|paginate|count|sum|exists|doesntExist|latest|oldest)\b/',
            $content, $m
        );
        array_push($found, ...($m[1] ?? []));

        // new ModelName(
        preg_match_all('/\bnew\s+([A-Z][A-Za-z0-9]+)\s*\(/', $content, $m);
        array_push($found, ...($m[1] ?? []));

        return array_unique(array_filter($found, fn($c) => !in_array($c, ['DB', 'Schema', 'Carbon', 'Str', 'Arr', 'Log', 'Cache', 'Config', 'Request', 'Response', 'Auth', 'Gate', 'Hash', 'Storage', 'Mail', 'Event', 'Queue', 'Redirect', 'Route', 'Session', 'Validator'])));
    }

    private function extractMethodBodyDeps(string $content): array
    {
        $found = [];

        // Type hints in non-constructor methods: public function store(UserService $svc, Request $r)
        preg_match_all(
            '/public\s+function\s+(?!__construct)\w+\s*\([^)]*\b([A-Z][A-Za-z0-9]+)\s+\$/',
            $content, $m
        );
        array_push($found, ...($m[1] ?? []));

        // $this->someProperty->method() — resolves to class name if property matches class naming
        // ClassName::staticCall() already handled in extractUsedClasses
        // Direct resolve/make calls: app(SomeService::class), resolve(SomeService::class)
        preg_match_all('/(?:app|resolve|make)\(\s*([A-Z][A-Za-z0-9]+)::class/', $content, $m);
        array_push($found, ...($m[1] ?? []));

        return array_unique(array_filter($found, fn($c) => !in_array($c, ['Request', 'Response', 'Model', 'Controller', 'FormRequest', 'Resource', 'Notification', 'Mailable', 'ShouldQueue'])));
    }
}
