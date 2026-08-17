<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

class ApiDocAnalyzer
{
    private const METHOD_WEIGHT = ['GET' => 1, 'POST' => 2, 'PUT' => 3, 'PATCH' => 4, 'DELETE' => 5];

    public function __construct(
        private string $appNamespace,
        private string $appPath
    ) {}

    public function analyze(): array
    {
        $items  = [];
        $errors = [];

        foreach (app('router')->getRoutes() as $route) {
            try {
                $action = $route->getActionName();

                if ($this->isVendorRoute($action)) continue;
                if (!$this->isApiRoute($route))   continue;

                $methods = array_values(array_filter(
                    $route->methods(),
                    fn($m) => !in_array($m, ['HEAD', 'OPTIONS'])
                ));
                if (empty($methods)) continue;

                foreach ($methods as $httpMethod) {
                    $items[] = $this->buildEndpoint($route, $httpMethod, $action);
                }
            } catch (\Throwable $e) {
                $errors[] = ['file' => 'route:' . $route->uri(), 'message' => $e->getMessage()];
            }
        }

        usort($items, fn($a, $b) =>
            $a['group']  <=> $b['group']  ?:
            $a['uri']    <=> $b['uri']    ?:
            (self::METHOD_WEIGHT[$a['method']] ?? 9) <=> (self::METHOD_WEIGHT[$b['method']] ?? 9)
        );

        return ['items' => $items, 'errors' => $errors];
    }

    // ── Route helpers ─────────────────────────────────────────────

    private function isApiRoute($route): bool
    {
        $uri = $route->uri();
        $mw  = $route->gatherMiddleware();
        return str_starts_with($uri, 'api/') || str_starts_with($uri, 'api') || in_array('api', $mw);
    }

    private function isVendorRoute(string $action): bool
    {
        if ($action === 'Closure') return false;
        $class = str_contains($action, '@') ? explode('@', $action)[0] : $action;
        return !str_starts_with($class, $this->appNamespace . '\\');
    }

    private function parseAction(string $action): array
    {
        if (str_contains($action, '@')) {
            [$class, $method] = explode('@', $action, 2);
            return [$class, $method];
        }
        return $action === 'Closure' ? ['Closure', null] : [$action, '__invoke'];
    }

    // ── Endpoint builder ──────────────────────────────────────────

    private function buildEndpoint($route, string $httpMethod, string $action): array
    {
        [$class, $method] = $this->parseAction($action);

        $uri        = '/' . ltrim($route->uri(), '/');
        $middleware = array_values($route->gatherMiddleware());

        [$requestClass, $bodyParams] = ($class && $class !== 'Closure' && $method)
            ? $this->detectFormRequest($class, $method)
            : [null, []];

        return [
            'method'        => $httpMethod,
            'uri'           => $uri,
            'name'          => $route->getName() ?? '',
            'group'         => $this->deriveGroup($uri),
            'controller'    => $class !== 'Closure' ? class_basename($class) : 'Closure',
            'action'        => $method ?? '',
            'middleware'    => $middleware,
            'auth_required' => $this->requiresAuth($middleware),
            'path_params'   => $this->extractPathParams($uri),
            'request_class' => $requestClass,
            'body_params'   => $bodyParams,
            'responses'     => $this->guessResponses($httpMethod),
        ];
    }

    private function deriveGroup(string $uri): string
    {
        $segments = array_values(array_filter(explode('/', ltrim($uri, '/'))));
        array_shift($segments); // drop 'api'

        // Skip version prefix like v1, v2
        if (!empty($segments) && preg_match('/^v\d+$/i', $segments[0])) {
            array_shift($segments);
        }

        return $segments[0] ?? 'misc';
    }

    private function extractPathParams(string $uri): array
    {
        preg_match_all('/\{(\??[\w]+)\}/', $uri, $m);
        return array_map(fn($p) => [
            'name'     => ltrim($p, '?'),
            'required' => !str_starts_with($p, '?'),
            'type'     => str_ends_with(ltrim($p, '?'), '_id') ? 'integer' : 'string',
        ], $m[1] ?? []);
    }

    private function requiresAuth(array $middleware): bool
    {
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'auth')) return true;
        }
        return false;
    }

    private function guessResponses(string $method): array
    {
        return match ($method) {
            'GET'         => [200 => 'OK', 401 => 'Unauthorized', 404 => 'Not Found'],
            'POST'        => [201 => 'Created', 401 => 'Unauthorized', 422 => 'Validation Error'],
            'PUT','PATCH' => [200 => 'OK', 401 => 'Unauthorized', 404 => 'Not Found', 422 => 'Validation Error'],
            'DELETE'      => [204 => 'No Content', 401 => 'Unauthorized', 404 => 'Not Found'],
            default       => [200 => 'OK'],
        };
    }

    // ── FormRequest detection ─────────────────────────────────────

    private function detectFormRequest(string $class, string $method): array
    {
        $file = $this->classToFile($class);
        if (!$file) return [null, []];

        $content = @file_get_contents($file);
        if (!$content) return [null, []];

        // Find the method signature
        $pattern = '/public\s+function\s+' . preg_quote($method, '/') . '\s*\(([^)]*)\)/s';
        if (!preg_match($pattern, $content, $m)) return [null, []];

        $signature = $m[1];

        // Look for a *Request type-hint
        if (!preg_match('/\b([A-Z]\w+Request)\s+\$\w+/', $signature, $hit)) return [null, []];

        $shortName = $hit[1];

        // Resolve FQN from use statements
        preg_match('/^use\s+([\w\\\\]+\\\\' . preg_quote($shortName, '/') . ')\s*;/m', $content, $useHit);
        if ($useHit) {
            $fqn = $useHit[1];
        } else {
            preg_match('/^namespace\s+([^;]+);/m', $content, $nsHit);
            $ns  = trim($nsHit[1] ?? '');
            $fqn = $ns ? $ns . '\\' . $shortName : $shortName;
        }

        $requestFile = $this->classToFile($fqn) ?? $this->findFileByClassName($shortName);
        if (!$requestFile) return [$shortName, []];

        $requestContent = @file_get_contents($requestFile);
        $rules = $requestContent ? $this->parseRules($requestContent) : [];

        return [$shortName, $rules];
    }

    private function parseRules(string $content): array
    {
        // Extract the rules() method body
        if (!preg_match('/public\s+function\s+rules\s*\(\s*\)[^{]*\{(.+?)^\s*\}/ms', $content, $m)) {
            return [];
        }
        $body   = $m[1];
        $params = [];

        // String rules:  'field' => 'required|string'
        preg_match_all('/[\'"](\w[\w.]*)[\'"] \s*=>\s* [\'"]([^\'"]+)[\'"]/x', $body, $strHits, PREG_SET_ORDER);
        foreach ($strHits as $hit) {
            $params[$hit[1]] = $this->buildParam($hit[1], $hit[2]);
        }

        // Array rules:  'field' => ['required', 'string', Rule::...]
        preg_match_all('/[\'"](\w[\w.]*)[\'"] \s*=>\s* \[([^\]]+)\]/x', $body, $arrHits, PREG_SET_ORDER);
        foreach ($arrHits as $hit) {
            preg_match_all('/[\'"]([^\'"]+)[\'"]/', $hit[2], $ruleItems);
            $ruleStr = implode('|', $ruleItems[1]);
            if ($ruleStr) $params[$hit[1]] = $this->buildParam($hit[1], $ruleStr);
        }

        return array_values($params);
    }

    private function buildParam(string $field, string $rules): array
    {
        $list = array_filter(array_map('trim', explode('|', $rules)));
        return [
            'field'    => $field,
            'type'     => $this->inferType($list),
            'required' => in_array('required', $list),
            'nullable' => in_array('nullable', $list),
            'rules'    => $rules,
        ];
    }

    private function inferType(array $rules): string
    {
        $map = [
            'integer'  => 'integer',
            'int'      => 'integer',
            'numeric'  => 'number',
            'string'   => 'string',
            'boolean'  => 'boolean',
            'bool'     => 'boolean',
            'array'    => 'array',
            'file'     => 'file',
            'image'    => 'file',
            'email'    => 'email',
            'url'      => 'url',
            'date'     => 'date',
            'uuid'     => 'uuid',
            'json'     => 'json',
            'exists'   => 'integer',
            'in'       => 'enum',
        ];
        foreach ($rules as $rule) {
            $base = strtolower(explode(':', $rule)[0]);
            if (isset($map[$base])) return $map[$base];
        }
        return 'string';
    }

    // ── File resolution ───────────────────────────────────────────

    private function classToFile(string $class): ?string
    {
        try {
            $ref = new \ReflectionClass($class);
            $file = $ref->getFileName();
            return $file ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function findFileByClassName(string $className): ?string
    {
        if (!is_dir($this->appPath)) return null;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->appPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getFilename() === $className . '.php') return $file->getPathname();
        }
        return null;
    }
}
