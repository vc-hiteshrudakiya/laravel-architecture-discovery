<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\Prompts;

class ModulePrompt
{
    public function build(array $data): string
    {
        $modules = $data['modules'] ?? [];
        $json    = json_encode($modules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
You are a Laravel modular architecture expert.

Analyze the following modules detected in this Laravel application:

```json
{$json}
```

Respond with a JSON object:

```json
{
  "assessment": "2-3 sentence overview of the modular structure",
  "module_health": [
    {
      "module": "module name",
      "status": "healthy|bloated|thin|unbalanced",
      "note": "specific observation",
      "suggestions": ["actionable suggestion"]
    }
  ],
  "coupling_risks": ["description of inter-module coupling risks"],
  "recommendations": ["architectural recommendations for the module system"]
}
```

Look for:
- Modules with too many or too few components (imbalance)
- Missing layers within modules (e.g. controllers but no services)
- Potential shared-kernel candidates
- Modules that should be split or merged

Return only the JSON object.
PROMPT;
    }
}
