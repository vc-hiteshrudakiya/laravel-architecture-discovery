<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Analyzers;

class RouteAnalyzer
{
    public function __construct(private string $appNamespace) {}

    public function analyze(): array
    {
        $includeVendor = config('architecture-discovery.include_vendor_routes', false);

        $items  = [];
        $errors = [];

        foreach (app('router')->getRoutes() as $route) {
            try {
                $action = $route->getActionName();

                if (!$includeVendor && $this->isVendorRoute($action)) {
                    continue;
                }

                [$class, $method] = $this->parseAction($action);

                $items[] = [
                    'uri'        => $route->uri(),
                    'methods'    => $route->methods(),
                    'controller' => [
                        'class'  => $class,
                        'method' => $method,
                    ],
                    'name'       => $route->getName(),
                    'middleware' => array_values($route->gatherMiddleware()),
                ];
            } catch (\Throwable $e) {
                $errors[] = [
                    'file'    => 'route: ' . $route->uri(),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function isVendorRoute(string $action): bool
    {
        if ($action === 'Closure') {
            return false;
        }

        $class = str_contains($action, '@') ? explode('@', $action)[0] : $action;

        // Use detected app namespace — works for any project, not just App\
        return !str_starts_with($class, $this->appNamespace . '\\');
    }

    private function parseAction(string $action): array
    {
        if (str_contains($action, '@')) {
            [$class, $method] = explode('@', $action);
            return [$class, $method];
        }

        if ($action === 'Closure') {
            return ['Closure', null];
        }

        return [$action, '__invoke'];
    }
}
