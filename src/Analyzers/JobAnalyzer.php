<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class JobAnalyzer
{
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

        // Queue / retry configuration
        $queue = null;
        if (preg_match('/\$queue\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $qm)) {
            $queue = $qm[1];
        }

        $tries = null;
        if (preg_match('/\$tries\s*=\s*(\d+)/', $content, $tm)) {
            $tries = (int) $tm[1];
        }

        $timeout = null;
        if (preg_match('/\$timeout\s*=\s*(\d+)/', $content, $tom)) {
            $timeout = (int) $tom[1];
        }

        $delay = null;
        if (preg_match('/\$delay\s*=\s*(\d+)/', $content, $dm)) {
            $delay = (int) $dm[1];
        }

        // Constructor dependencies (what data the job carries)
        $dependencies = [];
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $ctor)) {
            preg_match_all('/(?:public|protected|private|readonly)?\s*(?:\?)?([A-Z][\w\\\\]+)\s+\$(\w+)/', $ctor[1], $pm, PREG_SET_ORDER);
            foreach ($pm as $p) {
                $dependencies[] = ['type' => class_basename($p[1]), 'name' => $p[2]];
            }
        }

        return [
            'name'          => $name,
            'namespace'     => $namespace,
            'path'          => $file->getRelativePathname(),
            'should_queue'  => str_contains($content, 'ShouldQueue'),
            'unique'        => str_contains($content, 'ShouldBeUnique'),
            'encrypted'     => str_contains($content, 'ShouldBeEncrypted'),
            'queue'         => $queue,
            'tries'         => $tries,
            'timeout'       => $timeout,
            'delay'         => $delay,
            'dependencies'  => $dependencies,
        ];
    }
}
