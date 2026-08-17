<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts;

class DocumentationPrompt
{
    public function build(array $data, string $type = 'architecture'): string
    {
        return match ($type) {
            'models'      => $this->buildModels($data),
            'controllers' => $this->buildControllers($data),
            'routes'      => $this->buildRoutes($data),
            'services'    => $this->buildServices($data),
            'modules'     => $this->buildModules($data),
            default       => $this->buildArchitecture($data),
        };
    }

    private function buildArchitecture(array $data): string
    {
        $project = $data['project']['name'] ?? 'Laravel Application';
        $s       = $data['summary'] ?? [];
        $score   = $data['score'] ?? [];
        $rs      = $data['route_summary'] ?? [];
        $checks  = implode("\n", array_map(
            fn($c) => "- [{$c['status']}] {$c['label']}" . ($c['note'] ? ": {$c['note']}" : ''),
            $score['checks'] ?? []
        ));

        return <<<PROMPT
You are a senior technical writer and Laravel architect.

Write a professional **Architecture.md** document for the following Laravel application.

## Application: {$project}
- Laravel: {$data['laravel_version']} · PHP: {$data['php_version']}
- Architecture Score: {$score['score']}/{$score['max']} — {$score['grade']}
- Models: {$s['models']}, Controllers: {$s['controllers']}, Routes: {$rs['total']}
- Services: {$s['services']}, Repositories: {$s['repositories']}
- Jobs: {$s['jobs']}, Events: {$s['events']}, Observers: {$s['observers']}
- Policies: {$s['policies']}, Modules: {$s['modules']}

## Score Breakdown
{$checks}

## Route Distribution
- Web routes: {$rs['web']}, API routes: {$rs['api']}, Named: {$rs['named_count']}

Write the document with these sections:
1. # Architecture Overview (2-3 paragraphs about the overall design)
2. ## Design Patterns (what patterns are used — MVC, Repository, Service Layer, Event-driven, etc.)
3. ## Component Summary (table with component, count, purpose)
4. ## Architecture Score (score, grade, and what the checks mean)
5. ## Strengths (what the architecture does well)
6. ## Areas for Improvement (specific, actionable)
7. ## Technology Stack

Write professional, specific prose. Do not use generic filler. Reference actual counts and patterns from the data.
PROMPT;
    }

    private function buildModels(array $data): string
    {
        $models = array_map(fn($m) => [
            'name'          => $m['name'],
            'table'         => $m['table'] ?? '',
            'fillable'      => $m['fillable'] ?? [],
            'hidden'        => $m['hidden'] ?? [],
            'relationships' => array_map(
                fn($r) => $r['type'] . ' → ' . class_basename($r['related'] ?? ''),
                $m['relationships'] ?? []
            ),
        ], array_slice($data['models'] ?? [], 0, 50));

        $json = json_encode($models, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
You are a senior technical writer and Laravel expert.

Write a professional **Models.md** document for these Laravel Eloquent models.

```json
{$json}
```

For each model write a section with:
### ModelName
- **Table:** the database table name
- **Purpose:** 1-2 sentences describing what this model represents in the business domain
- **Fields:** list the fillable fields and what they represent
- **Relationships:** list each relationship with a brief explanation of the business meaning
- **Responsibilities:** what business logic this model is responsible for

End with a ## Model Relationship Overview section describing how the models connect in the domain.

Be specific and professional. Infer business purpose from the model and field names.
PROMPT;
    }

    private function buildControllers(array $data): string
    {
        $controllers = array_map(fn($c) => [
            'name'         => $c['name'],
            'method_count' => $c['method_count'] ?? 0,
            'methods'      => array_slice($c['methods'] ?? [], 0, 15),
        ], array_slice($data['controllers'] ?? [], 0, 40));

        $json = json_encode($controllers, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a senior technical writer and Laravel expert.

Write a professional **Controllers.md** document for these Laravel controllers.

```json
{$json}
```

For each controller write:
### ControllerName
- **Responsibility:** what HTTP concerns this controller handles
- **Methods ({count}):** brief description of each method and its purpose
- **Observations:** note any concerns (fat controller, good SRP, resource controller, etc.)

End with a ## Controller Layer Summary section covering:
- Overall controller design quality
- Which controllers might need refactoring
- Suggested service extractions

Be specific. Infer responsibility from method names.
PROMPT;
    }

    private function buildRoutes(array $data): string
    {
        $rs     = $data['route_summary'] ?? [];
        $routes = array_slice($data['routes'] ?? [], 0, 80);
        $byMethod = $rs['by_method'] ?? [];
        $apiVer   = array_keys($rs['api_versions'] ?? []);

        $routeLines = implode("\n", array_map(function ($r) {
            $methods = implode('|', array_filter($r['methods'] ?? [], fn($m) => $m !== 'HEAD'));
            $ctrl    = class_basename($r['controller']['class'] ?? '—');
            $action  = $r['controller']['method'] ?? '—';
            $name    = $r['name'] ?? '';
            return "- [{$methods}] `{$r['uri']}` → {$ctrl}@{$action}" . ($name ? " ({$name})" : '');
        }, $routes));

        return <<<PROMPT
You are a senior technical writer and API documentation specialist.

Write a professional **Routes.md** document for this Laravel application.

## Route Summary
- Total: {$rs['total']}, Web: {$rs['web']}, API: {$rs['api']}
- Named routes: {$rs['named_count']}
- API versions: {$this->implodeOrNone($apiVer)}
- By method: {$this->implodeAssoc($byMethod)}

## Route List
{$routeLines}

Write the document with:
1. # Route Documentation
2. ## Overview (purpose of the routing layer, REST conventions, naming patterns)
3. ## Web Routes (grouped by controller, with purpose description)
4. ## API Routes (grouped by resource/version, with endpoint purpose, auth requirements)
5. ## Route Naming Conventions (what patterns are used)
6. ## Middleware Usage (which middleware are applied and where)

Be professional. Group routes logically. Infer purpose from URIs and controller names.
PROMPT;
    }

    private function buildServices(array $data): string
    {
        $services = array_slice($data['services'] ?? [], 0, 40);
        $repos    = array_slice($data['repositories'] ?? [], 0, 20);
        $jobs     = array_slice($data['jobs'] ?? [], 0, 20);
        $events   = array_slice($data['events'] ?? [], 0, 20);

        $svcList  = implode("\n", array_map(fn($s) => "- {$s['name']}", $services));
        $repoList = implode("\n", array_map(fn($r) => "- {$r['name']}", $repos));
        $jobList  = implode("\n", array_map(fn($j) => "- {$j['name']}", $jobs));
        $evtList  = implode("\n", array_map(fn($e) => "- {$e['name']}", $events));

        return <<<PROMPT
You are a senior technical writer and Laravel expert.

Write a professional **Services.md** document for this Laravel application's service layer.

## Services ({$this->count($services)})
{$svcList}

## Repositories ({$this->count($repos)})
{$repoList}

## Jobs ({$this->count($jobs)})
{$jobList}

## Events ({$this->count($events)})
{$evtList}

Write the document with:
1. # Service Layer Documentation
2. ## Architecture Overview (how the service layer fits into the overall architecture)
3. ## Services (for each: purpose, responsibilities, likely dependencies)
4. ## Repositories (purpose, what data access they encapsulate)
5. ## Background Jobs (what async work they handle, when they're dispatched)
6. ## Events & Listeners (what domain events exist, what they signal)
7. ## Layer Interaction Diagram (text-based flow: Controller → Service → Repository → Model)

Infer purpose from class names. Be professional and specific.
PROMPT;
    }

    private function buildModules(array $data): string
    {
        $modules = $data['modules'] ?? [];

        if (empty($modules)) {
            return <<<PROMPT
Write a short **Modules.md** note explaining that no modules directory was detected in this Laravel application,
and briefly describe when modular architecture (nwidart/laravel-modules) would be beneficial for this project
based on its size (models: {$data['summary']['models']}, controllers: {$data['summary']['controllers']}).
PROMPT;
        }

        $json = json_encode($modules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
You are a senior technical writer and Laravel modular architecture expert.

Write a professional **Modules.md** document for this modular Laravel application.

```json
{$json}
```

Write the document with:
1. # Module Architecture
2. ## Overview (how the application is divided into modules, overall philosophy)
3. ## Module Breakdown (for each module: purpose, components, responsibilities, key flows)
4. ## Inter-Module Dependencies (which modules likely depend on which)
5. ## Module Health Assessment (balanced vs bloated vs thin modules)
6. ## Recommendations (improvements to the module structure)

Infer business domain and purpose from module names and component counts.
PROMPT;
    }

    private function implodeOrNone(array $items): string
    {
        return empty($items) ? 'none' : implode(', ', $items);
    }

    private function implodeAssoc(array $items): string
    {
        $parts = [];
        foreach ($items as $k => $v) $parts[] = strtoupper($k) . ": {$v}";
        return empty($parts) ? 'none' : implode(', ', $parts);
    }

    private function count(array $items): int
    {
        return count($items);
    }
}
