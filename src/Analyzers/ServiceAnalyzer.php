<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ServiceAnalyzer
{
    private const SKIP_METHODS = ['__construct', '__destruct', '__toString', '__invoke'];

    public function __construct(
        private string $path,
        private string $suffix = 'Service',
    ) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $items = $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') continue;
            if (!str_ends_with($file->getFilenameWithoutExtension(), $this->suffix)) continue;
            try {
                $item = $this->processFile($file);
                if ($item) $items[] = $item;
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file->getRelativePathname(), 'message' => $e->getMessage()];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function processFile(SplFileInfo $file): ?array
    {
        $content = file_get_contents($file->getRealPath());

        if (!preg_match('/^namespace\s+([^;]+);/m', $content, $ns)) return null;
        if (!preg_match('/class\s+(\w+)/', $content, $cn)) return null;

        $name      = $cn[1];
        $namespace = trim($ns[1]);

        // Constructor dependencies
        $dependencies = [];
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $ctor)) {
            preg_match_all('/(?:public|protected|private|readonly)?\s*(?:\?)?([A-Z][\w\\\\]+)\s+\$(\w+)/', $ctor[1], $pm, PREG_SET_ORDER);
            foreach ($pm as $p) {
                $dependencies[] = class_basename($p[1]);
            }
        }

        // Public methods (skip magic)
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $mm);
        $methods = array_values(array_filter($mm[1] ?? [], fn($m) => !in_array($m, self::SKIP_METHODS)));

        return [
            'name'         => $name,
            'namespace'    => $namespace,
            'path'         => $file->getRelativePathname(),
            'dependencies' => $dependencies,
            'methods'      => $methods,
            'method_count' => count($methods),
        ];
    }
}
