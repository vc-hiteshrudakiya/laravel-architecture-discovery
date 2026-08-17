<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts;

class CodeSmellPrompt
{
    public function build(array $data): string
    {
        $controllers = array_map(fn($c) => [
            'name'         => $c['name'],
            'method_count' => $c['method_count'] ?? 0,
            'methods'      => array_slice($c['methods'] ?? [], 0, 15),
        ], $data['controllers'] ?? []);

        $models = array_map(fn($m) => [
            'name'             => $m['name'],
            'fillable_count'   => count($m['fillable'] ?? []),
            'hidden_count'     => count($m['hidden'] ?? []),
            'relationship_cnt' => count($m['relationships'] ?? []),
        ], $data['models'] ?? []);

        $json = json_encode(['controllers' => $controllers, 'models' => $models], JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a senior PHP engineer specializing in code quality and clean architecture.

Identify code smells in this Laravel application architecture:

```json
{$json}
```

Respond with a JSON array of code smells:

```json
[
  {
    "smell": "smell name (e.g. God Controller, Anemic Model)",
    "location": "class name",
    "evidence": "what specifically indicates this smell",
    "severity": "critical|major|minor",
    "fix": "concise refactoring suggestion"
  }
]
```

Focus on:
- Fat/God controllers (many methods or business logic in HTTP layer)
- Anemic models (no relationships, no business methods)
- Missing abstraction layers
- Potential N+1 query patterns from relationship counts
- Over-engineering (too many tiny classes)

Return only the JSON array.
PROMPT;
    }
}
