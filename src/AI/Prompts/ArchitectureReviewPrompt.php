<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts;

class ArchitectureReviewPrompt
{
    public function build(array $data): string
    {
        $summary  = $data['summary']      ?? [];
        $score    = $data['score']        ?? [];
        $project  = $data['project']      ?? [];
        $routes   = $data['route_summary'] ?? [];
        $deps     = $data['dependencies'] ?? ['nodes' => [], 'edges' => []];

        $laravelVersion = $data['laravel_version'] ?? 'unknown';
        $phpVersion     = $data['php_version']     ?? 'unknown';

        $modelList = $this->summariseModels($data['models'] ?? []);
        $ctrlList  = $this->summariseControllers($data['controllers'] ?? []);
        $depGraph  = $this->summariseDeps($deps);
        $scoreList = $this->summariseScore($score);

        $json = json_encode([
            'summary'               => $summary,
            'existing_score'        => $score,
            'score_checks'          => $scoreList,
            'models'                => $modelList,
            'controllers'           => $ctrlList,
            'routes'                => $routes,
            'dependency_graph'      => $depGraph,
            'jobs_count'            => $summary['jobs']         ?? 0,
            'events_count'          => $summary['events']       ?? 0,
            'services_count'        => $summary['services']     ?? 0,
            'repositories_count'    => $summary['repositories'] ?? 0,
            'observers_count'       => $summary['observers']    ?? 0,
            'policies_count'        => $summary['policies']     ?? 0,
            'modules_count'         => $summary['modules']      ?? 0,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
You are an expert Laravel architect and senior PHP engineer.

Analyze the following Laravel application architecture and provide a detailed, opinionated review.

## Application
- Project: {$project['name']}
- Laravel: {$laravelVersion}
- PHP: {$phpVersion}

## Architecture Data
```json
{$json}
```

## Instructions
Review the architecture and identify:
1. Structural problems (God classes, fat controllers, missing layers, tight coupling, etc.)
2. SOLID principle violations
3. Laravel best practice violations
4. Missing patterns (Repository missing when Services exist, Observers for event-heavy models, etc.)
5. Actionable refactoring suggestions

## Required Output Format
Respond ONLY with a valid JSON object matching this schema exactly:

```json
{
  "summary": "2-3 sentence executive summary of the architecture",
  "score": 0-100,
  "problems": [
    {
      "title": "short title",
      "description": "detailed description with specific class/file references where possible",
      "severity": "error|warning|info",
      "location": "class name or area of the codebase"
    }
  ],
  "suggestions": [
    {
      "title": "short title",
      "description": "actionable improvement description",
      "priority": "high|medium|low",
      "example": "optional short code or command example"
    }
  ],
  "best_practices": [
    "string — concise best practice that is already followed"
  ],
  "solid_review": {
    "S": { "status": "pass|warn|fail", "note": "Single Responsibility assessment" },
    "O": { "status": "pass|warn|fail", "note": "Open/Closed assessment" },
    "L": { "status": "pass|warn|fail", "note": "Liskov Substitution assessment" },
    "I": { "status": "pass|warn|fail", "note": "Interface Segregation assessment" },
    "D": { "status": "pass|warn|fail", "note": "Dependency Inversion assessment" }
  },
  "laravel_best_practices": [
    {
      "name": "practice name",
      "status": "pass|warn|fail",
      "note": "specific observation about this project"
    }
  ]
}
```

Check these Laravel best practices:
- Fat controllers (>10 public methods is a warning, >15 is a fail)
- Service layer usage
- Repository pattern
- Model mass assignment protection (fillable/guarded)
- Route naming conventions
- API versioning
- Observer usage for model events
- Policy usage for authorization
- Job/Queue usage for heavy tasks
- Event-driven architecture

Do not include any text outside the JSON object.
PROMPT;
    }

    private function summariseModels(array $models): array
    {
        return array_map(fn($m) => [
            'name'          => $m['name'],
            'table'         => $m['table'] ?? '',
            'fillable'      => $m['fillable'] ?? [],
            'relationships' => array_map(fn($r) => $r['type'] . ':' . class_basename($r['related'] ?? ''), $m['relationships'] ?? []),
            'hidden_count'  => count($m['hidden'] ?? []),
        ], array_slice($models, 0, 40));
    }

    private function summariseControllers(array $controllers): array
    {
        return array_map(fn($c) => [
            'name'         => $c['name'],
            'method_count' => $c['method_count'] ?? 0,
            'methods'      => array_slice($c['methods'] ?? [], 0, 10),
        ], array_slice($controllers, 0, 30));
    }

    private function summariseDeps(array $deps): array
    {
        return [
            'node_count' => count($deps['nodes'] ?? []),
            'edge_count' => count($deps['edges'] ?? []),
            'nodes'      => array_slice(array_map(fn($n) => $n['name'] . '(' . ($n['layer'] ?? '') . ')', $deps['nodes'] ?? []), 0, 50),
            'edges'      => array_slice(array_map(fn($e) => $e['from'] . '->' . $e['to'], $deps['edges'] ?? []), 0, 60),
        ];
    }

    private function summariseScore(array $score): array
    {
        return array_map(fn($c) => $c['status'] . ': ' . $c['label'] . ($c['note'] ? ' (' . $c['note'] . ')' : ''), $score['checks'] ?? []);
    }
}
