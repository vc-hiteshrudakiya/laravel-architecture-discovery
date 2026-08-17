<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ObserverAnalyzer
{
    private const LIFECYCLE_EVENTS = [
        'retrieved', 'creating', 'created', 'updating', 'updated',
        'saving', 'saved', 'deleting', 'deleted', 'restoring',
        'restored', 'forceDeleting', 'forceDeleted',
    ];

    public function __construct(private string $path) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $items = $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') continue;
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

        // Infer model name from observer class name (UserObserver → User)
        $model = str_ends_with($name, 'Observer') ? substr($name, 0, -8) : null;

        // Detect handled lifecycle events
        $events = [];
        foreach (self::LIFECYCLE_EVENTS as $event) {
            if (preg_match('/public\s+function\s+' . preg_quote($event, '/') . '\s*\(/', $content)) {
                $events[] = $event;
            }
        }

        return [
            'name'      => $name,
            'namespace' => $namespace,
            'path'      => $file->getRelativePathname(),
            'model'     => $model,
            'events'    => $events,
        ];
    }
}
