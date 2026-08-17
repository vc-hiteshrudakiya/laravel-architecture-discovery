<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

class ModuleAnalyzer
{
    public function __construct(private string $modulesPath) {}

    public function analyze(): array
    {
        if (!is_dir($this->modulesPath)) {
            return ['items' => [], 'errors' => []];
        }

        $items  = [];
        $errors = [];

        foreach (new \DirectoryIterator($this->modulesPath) as $entry) {
            if (!$entry->isDir() || $entry->isDot()) continue;

            try {
                $items[] = $this->analyzeModule($entry->getPathname(), $entry->getFilename());
            } catch (\Throwable $e) {
                $errors[] = ['file' => $entry->getPathname(), 'message' => $e->getMessage()];
            }
        }

        usort($items, fn($a, $b) => strcmp($a['name'], $b['name']));

        return ['items' => $items, 'errors' => $errors];
    }

    private function analyzeModule(string $path, string $name): array
    {
        return [
            'name'        => $name,
            'path'        => ltrim(str_replace(base_path(), '', $path), DIRECTORY_SEPARATOR),
            'controllers' => $this->countPhpFiles($path . '/Http/Controllers'),
            'models'      => $this->countPhpFiles($path . '/Models')
                           ?: $this->countPhpFiles($path . '/Model'),
            'routes'      => $this->countRoutes($path . '/routes')
                           ?: $this->countRoutes($path . '/Routes'),
            'jobs'        => $this->countPhpFiles($path . '/Jobs'),
            'events'      => $this->countPhpFiles($path . '/Events'),
            'services'    => $this->countPhpFiles($path . '/Services'),
        ];
    }

    private function countPhpFiles(string $dir): int
    {
        if (!is_dir($dir)) return 0;

        $count = 0;
        $it    = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() === 'php') $count++;
        }
        return $count;
    }

    private function countRoutes(string $routesDir): int
    {
        if (!is_dir($routesDir)) return 0;

        $count = 0;
        foreach (new \DirectoryIterator($routesDir) as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;
            $content = file_get_contents($file->getPathname());
            preg_match_all(
                '/Route\s*::\s*(?:get|post|put|patch|delete|options|any|resource|apiResource|match)\s*\(/i',
                $content,
                $m
            );
            $count += count($m[0]);
        }
        return $count;
    }
}
