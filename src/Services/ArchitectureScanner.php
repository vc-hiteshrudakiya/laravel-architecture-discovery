<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Services;

class ArchitectureScanner
{
    public function __construct(protected array $analyzers) {}

    public function scan(): ArchitectureReport
    {
        $startTime   = microtime(true);
        $startMemory = memory_get_usage(true);

        $report = new ArchitectureReport(
            config('app.name', 'Laravel Application'),
            base_path(),
        );

        // Core analyzers
        foreach (['models', 'controllers', 'routes'] as $type) {
            if (!isset($this->analyzers[$type])) continue;

            $result = $this->analyzers[$type]->analyze();

            $addItem = match ($type) {
                'models'      => fn($item) => $report->addModel($item),
                'controllers' => fn($item) => $report->addController($item),
                'routes'      => fn($item) => $report->addRoute($item),
            };

            foreach ($result['items']  as $item)  { $addItem($item); }
            foreach ($result['errors'] as $error) { $report->addError($error); }
        }

        // Extended analyzers
        $extendedMap = [
            'jobs'         => fn($item) => $report->addJob($item),
            'events'       => fn($item) => $report->addEvent($item),
            'services'     => fn($item) => $report->addService($item),
            'repositories' => fn($item) => $report->addRepository($item),
            'observers'    => fn($item) => $report->addObserver($item),
            'policies'     => fn($item) => $report->addPolicy($item),
            'modules'      => fn($item) => $report->addModule($item),
            'packages'     => fn($item) => $report->addPackage($item),
            'api_docs'     => fn($item) => $report->addApiDoc($item),
        ];

        foreach ($extendedMap as $type => $addFn) {
            if (!isset($this->analyzers[$type])) continue;
            $result = $this->analyzers[$type]->analyze();
            foreach ($result['items']  as $item)  { $addFn($item); }
            foreach ($result['errors'] as $error) { $report->addError($error); }
        }

        // Dependency graph
        if (isset($this->analyzers['dependencies'])) {
            $result = $this->analyzers['dependencies']->analyze();
            $report->setDependencies($result);
            foreach ($result['errors'] as $error) { $report->addError($error); }
        }

        $report->setPerformance(
            (microtime(true) - $startTime) * 1000,
            (memory_get_usage(true) - $startMemory) / 1024 / 1024,
        );

        $report->setScore((new ArchitectureScorer())->score($report->getReport()));

        return $report;
    }
}
