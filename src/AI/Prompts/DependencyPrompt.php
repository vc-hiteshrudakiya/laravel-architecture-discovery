<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts;

class DependencyPrompt
{
    public function build(array $data): string
    {
        $deps  = $data['dependencies'] ?? ['nodes' => [], 'edges' => []];
        $nodes = array_slice($deps['nodes'] ?? [], 0, 60);
        $edges = array_slice($deps['edges'] ?? [], 0, 80);

        $json = json_encode(['nodes' => $nodes, 'edges' => $edges], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
You are an expert in software dependency analysis and clean architecture.

Analyze this Laravel application's dependency graph:

```json
{$json}
```

Respond with a JSON object:

```json
{
  "assessment": "2-3 sentence overview of dependency health",
  "circular_risks": [
    {
      "description": "describe the potential circular or problematic chain",
      "nodes_involved": ["NodeA", "NodeB"],
      "severity": "high|medium|low"
    }
  ],
  "layer_violations": [
    {
      "from": "class name",
      "to": "class name",
      "violation": "description of why this direction is wrong",
      "fix": "how to resolve it"
    }
  ],
  "coupling_score": 0-100,
  "recommendations": ["dependency improvement recommendation"]
}
```

Look for:
- Controllers depending directly on models (skipping service layer)
- Circular dependencies
- Layer violations (lower layers depending on higher layers)
- High fan-out nodes (classes with many dependencies)
- Isolated nodes (never used by anything)

Return only the JSON object.
PROMPT;
    }
}
