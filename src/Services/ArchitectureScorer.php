<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Services;

class ArchitectureScorer
{
    private array $checks = [];
    private int   $score  = 0;

    /**
     * Score the architecture report and return the full score data.
     */
    public function score(array $data): array
    {
        $this->checks = [];
        $this->score  = 0;

        $this->checkModelsOrganized($data['models']);
        $this->checkFillableDefined($data['models']);
        $this->checkRelationshipsMapped($data['models']);
        $this->checkControllersFocused($data['controllers']);
        $this->checkServiceLayer($data['dependencies']['nodes']);
        $this->checkRepositoryLayer($data['dependencies']['nodes']);
        $this->checkRoutesNamed($data['route_summary']);
        $this->checkApiVersioned($data['route_summary']);
        $this->checkMiddlewareSecured($data['route_summary']);
        $this->checkErrorFree($data['errors']);

        return [
            'score'  => $this->score,
            'max'    => 100,
            'grade'  => $this->grade(),
            'color'  => $this->color(),
            'checks' => $this->checks,
        ];
    }

    // ── Checks ──────────────────────────────────────────────────────

    private function checkModelsOrganized(array $models): void
    {
        if (empty($models)) {
            $this->fail('models_organized', 'Models Organized', 'No models found');
            return;
        }
        $inModelsDir = count(array_filter($models, fn($m) => str_contains($m['path'] ?? '', 'Models')));
        $pct = ($inModelsDir / count($models)) * 100;

        if ($pct >= 90) {
            $this->pass('models_organized', 'Models Organized', 10);
        } elseif ($pct >= 50) {
            $this->warn('models_organized', 'Models Partially Organized', 5, "{$inModelsDir}/" . count($models) . " in Models/");
        } else {
            $this->fail('models_organized', 'Models Not Organized', 'Move models into app/Models/');
        }
    }

    private function checkFillableDefined(array $models): void
    {
        if (empty($models)) { $this->fail('fillable', 'Fillable Defined', 'No models'); return; }

        $defined = count(array_filter($models, fn($m) => !empty($m['fillable'])));
        $pct = ($defined / count($models)) * 100;

        if ($pct >= 80) {
            $this->pass('fillable', 'Fillable Defined', 10);
        } elseif ($pct >= 50) {
            $this->warn('fillable', 'Fillable Partially Defined', 5, "{$defined}/" . count($models) . " models");
        } else {
            $this->fail('fillable', 'Fillable Not Defined', 'Add $fillable to models for mass assignment protection');
        }
    }

    private function checkRelationshipsMapped(array $models): void
    {
        if (empty($models)) { $this->fail('relationships', 'Relationships Mapped', 'No models'); return; }

        $withRels = count(array_filter($models, fn($m) => !empty($m['relationships'])));
        $pct = ($withRels / count($models)) * 100;

        if ($pct >= 50) {
            $this->pass('relationships', 'Relationships Mapped', 10);
        } elseif ($pct >= 20) {
            $this->warn('relationships', 'Few Relationships Defined', 5, "{$withRels}/" . count($models) . " models");
        } else {
            $this->fail('relationships', 'No Relationships Defined', 'Define Eloquent relationships in your models');
        }
    }

    private function checkControllersFocused(array $controllers): void
    {
        if (empty($controllers)) { $this->pass('controllers_size', 'Controllers Focused', 15); return; }

        $bloated = array_filter($controllers, fn($c) => ($c['method_count'] ?? 0) > 10);
        $fat     = array_filter($controllers, fn($c) => ($c['method_count'] ?? 0) > 15);

        if (empty($bloated)) {
            $this->pass('controllers_size', 'Controllers Focused', 15);
        } elseif (empty($fat)) {
            $names = implode(', ', array_column(array_slice(array_values($bloated), 0, 2), 'name'));
            $this->warn('controllers_size', 'Some Controllers Large', 8, "{$names} >10 methods");
        } else {
            $names = implode(', ', array_column(array_slice(array_values($fat), 0, 2), 'name'));
            $this->fail('controllers_size', 'God Controllers Detected', "{$names} >15 methods — split into smaller classes");
        }
    }

    private function checkServiceLayer(array $nodes): void
    {
        $services = array_filter($nodes, fn($n) => ($n['layer'] ?? '') === 'service');
        if (!empty($services)) {
            $this->pass('service_layer', 'Service Layer Present', 15);
        } else {
            $this->fail('service_layer', 'Missing Service Layer', 'Add Service classes to separate business logic from controllers');
        }
    }

    private function checkRepositoryLayer(array $nodes): void
    {
        $repos = array_filter($nodes, fn($n) => ($n['layer'] ?? '') === 'repository');
        if (!empty($repos)) {
            $this->pass('repository_layer', 'Repository Layer Present', 10);
        } else {
            $this->warn('repository_layer', 'No Repository Layer', 0, 'Optional: add Repository classes for data access abstraction');
        }
    }

    private function checkRoutesNamed(array $routeSummary): void
    {
        $total = $routeSummary['total'] ?? 0;
        $named = $routeSummary['named_count'] ?? 0;
        if ($total === 0) { $this->pass('routes_named', 'Routes Named', 10); return; }

        $pct = ($named / $total) * 100;
        if ($pct >= 70) {
            $this->pass('routes_named', 'Routes Well Named', 10);
        } elseif ($pct >= 40) {
            $this->warn('routes_named', 'Some Routes Unnamed', 5, round($pct) . "% have names");
        } else {
            $this->fail('routes_named', 'Routes Not Named', 'Add ->name() to your route definitions');
        }
    }

    private function checkApiVersioned(array $routeSummary): void
    {
        if (!empty($routeSummary['api_versions'])) {
            $versions = implode(', ', array_keys($routeSummary['api_versions']));
            $this->pass('api_versioned', 'API Versioned', 5, $versions);
        } else {
            $this->warn('api_versioned', 'API Not Versioned', 0, 'Consider api/v1/... prefix for future compatibility');
        }
    }

    private function checkMiddlewareSecured(array $routeSummary): void
    {
        $usage = $routeSummary['middleware_usage'] ?? [];
        $secured = ($usage['auth'] ?? 0) + ($usage['auth:sanctum'] ?? 0) + ($usage['admin'] ?? 0);

        if ($secured > 0) {
            $this->pass('middleware_secured', 'Middleware Secured', 5, "{$secured} routes protected");
        } else {
            $this->warn('middleware_secured', 'No Auth Middleware Found', 0, 'Add auth middleware to protected routes');
        }
    }

    private function checkErrorFree(array $errors): void
    {
        $count = count($errors);
        if ($count === 0) {
            $this->pass('error_free', 'Error Free Scan', 10);
        } elseif ($count <= 3) {
            $this->warn('error_free', 'Minor Scan Errors', 5, "{$count} file(s) could not be parsed");
        } else {
            $this->fail('error_free', 'Scan Had Errors', "{$count} files failed — check permissions and syntax");
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function pass(string $key, string $label, int $points, string $note = ''): void
    {
        $this->score   += $points;
        $this->checks[] = ['key' => $key, 'label' => $label, 'status' => 'pass', 'points' => $points, 'note' => $note];
    }

    private function warn(string $key, string $label, int $points, string $note = ''): void
    {
        $this->score   += $points;
        $this->checks[] = ['key' => $key, 'label' => $label, 'status' => 'warn', 'points' => $points, 'note' => $note];
    }

    private function fail(string $key, string $label, string $note = ''): void
    {
        $this->checks[] = ['key' => $key, 'label' => $label, 'status' => 'fail', 'points' => 0, 'note' => $note];
    }

    private function grade(): string
    {
        return match (true) {
            $this->score >= 90 => 'Excellent',
            $this->score >= 75 => 'Good',
            $this->score >= 60 => 'Fair',
            default            => 'Needs Work',
        };
    }

    private function color(): string
    {
        return match (true) {
            $this->score >= 90 => 'emerald',
            $this->score >= 75 => 'blue',
            $this->score >= 60 => 'amber',
            default            => 'red',
        };
    }
}
