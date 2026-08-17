<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ControllerAnalyzer
{
    public function __construct(private string $path) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $items  = [];
        $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            try {
                $items[] = $this->processFile($file);
            } catch (\Throwable $e) {
                $errors[] = [
                    'file'    => $this->relativePath($file->getRealPath()),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function processFile(SplFileInfo $file): array
    {
        $content   = file_get_contents($file->getRealPath());
        $name      = $file->getFilenameWithoutExtension();
        $namespace = $this->detectNamespace($content);
        $methods   = $this->detectMethods($content);

        return [
            'name'           => $name,
            'path'           => $this->relativePath($file->getRealPath()),
            'namespace'      => $namespace,
            'full_class'     => $namespace ? $namespace . '\\' . $name : $name,
            'methods'        => $methods,
            'method_count'   => count($methods),
            'dependencies'   => $this->detectDependencies($content),
            'middleware'     => $this->detectMiddleware($content),
            'method_details' => $this->detectMethodDetails($content),
            'is_resource'    => $this->isResourceController($methods),
        ];
    }

    private function relativePath(string $absolutePath): string
    {
        return ltrim(str_replace(base_path(), '', $absolutePath), DIRECTORY_SEPARATOR);
    }

    private function detectNamespace(string $content): string
    {
        preg_match('/^namespace\s+([^;]+);/m', $content, $match);
        return isset($match[1]) ? trim($match[1]) : '';
    }

    private function detectMethods(string $content): array
    {
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $matches);

        return array_values(
            array_filter($matches[1], fn($method) => !str_starts_with($method, '__'))
        );
    }

    private function detectDependencies(string $content): array
    {
        if (!preg_match('/public\s+function\s+__construct\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        preg_match_all('/\b([A-Z][\w\\\\]+)\s+\$(\w+)/', $m[1], $hits);
        $deps = [];
        foreach ($hits[1] as $i => $type) {
            $short = class_basename(str_replace('\\', '/', $type));
            if (in_array($short, ['Request', 'Response', 'Application', 'Container'])) continue;
            $deps[] = ['type' => $short, 'var' => $hits[2][$i]];
        }
        return $deps;
    }

    private function detectMiddleware(string $content): array
    {
        $mw = [];

        // PHP 8 attribute: #[Middleware('auth')]
        preg_match_all('/#\[Middleware\([\'"]([^\'"]+)[\'"]\)\]/', $content, $m);
        array_push($mw, ...($m[1] ?? []));

        // $this->middleware('auth') or $this->middleware(['auth', 'verified'])
        preg_match_all('/\$this->middleware\(\s*[\'"]([\w:,\s|]+)[\'"]/m', $content, $m);
        foreach ($m[1] ?? [] as $item) {
            array_push($mw, ...array_map('trim', explode(',', $item)));
        }

        preg_match_all('/\$this->middleware\(\s*\[([^\]]+)\]/s', $content, $m);
        foreach ($m[1] ?? [] as $block) {
            preg_match_all('/[\'"]([^\'"]+)[\'"]/', $block, $inner);
            array_push($mw, ...($inner[1] ?? []));
        }

        return array_values(array_unique(array_filter($mw)));
    }

    private function detectMethodDetails(string $content): array
    {
        // Match each public non-magic method with its full signature
        preg_match_all(
            '/public\s+function\s+(\w+)\s*\(([^)]*)\)/s',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        $details = [];
        foreach ($matches as $match) {
            $name = $match[1];
            if (str_starts_with($name, '__')) continue;

            $params = [];
            $sig    = trim($match[2]);
            if ($sig !== '') {
                foreach (preg_split('/,(?![^<>]*>)/', $sig) as $param) {
                    $param = trim($param);
                    if (preg_match('/\b([A-Z][\w\\\\]+)\s+\$(\w+)/', $param, $p)) {
                        $params[] = ['type' => class_basename(str_replace('\\', '/', $p[1])), 'var' => $p[2]];
                    } elseif (preg_match('/\$(\w+)/', $param, $p)) {
                        $params[] = ['type' => null, 'var' => $p[1]];
                    }
                }
            }

            $details[] = ['method' => $name, 'params' => $params];
        }

        return $details;
    }

    private function isResourceController(array $methods): bool
    {
        $resource = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $found    = array_intersect($methods, $resource);
        return count($found) >= 4;
    }
}
