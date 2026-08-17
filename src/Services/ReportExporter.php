<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Services;

use InvalidArgumentException;

class ReportExporter
{
    public function export(ArchitectureReport $report, string $format, string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        match ($format) {
            'json'     => file_put_contents($path, $report->toJson()),
            'html'     => file_put_contents($path, $this->renderHtml($report)),
            'markdown' => file_put_contents($path, $this->renderMarkdown($report)),
            'svg'      => file_put_contents($path, $this->renderSvg($report)),
            default    => throw new InvalidArgumentException("Unsupported format: {$format}. Supported: json, html, markdown, svg"),
        };
    }

    public function renderHtml(ArchitectureReport $report): string
    {
        return view('architecture-discovery::report', [
            'data' => $report->getReport(),
        ])->render();
    }

    public function renderSvg(ArchitectureReport $report): string
    {
        $data    = $report->getReport();
        $summary = $data['summary'];
        $project = $data['project']['name'];
        $score   = $data['score']['score'] ?? 0;
        $grade   = $data['score']['grade'] ?? '';

        $components = [
            ['Models',        $summary['models'] ?? 0,        '#8b5cf6', '#ede9fe'],
            ['Controllers',   $summary['controllers'] ?? 0,   '#3b82f6', '#dbeafe'],
            ['Routes',        $summary['routes'] ?? 0,        '#10b981', '#d1fae5'],
            ['Jobs',          $summary['jobs'] ?? 0,          '#f59e0b', '#fef9c3'],
            ['Events',        $summary['events'] ?? 0,        '#ec4899', '#fce7f3'],
            ['Services',      $summary['services'] ?? 0,      '#7c3aed', '#f3e8ff'],
            ['Repositories',  $summary['repositories'] ?? 0,  '#0891b2', '#cffafe'],
            ['Observers',     $summary['observers'] ?? 0,     '#f97316', '#ffedd5'],
            ['Policies',      $summary['policies'] ?? 0,      '#64748b', '#f1f5f9'],
            ['Modules',       $summary['modules'] ?? 0,       '#4f46e5', '#e0e7ff'],
            ['Packages',      $summary['packages'] ?? 0,      '#059669', '#d1fae5'],
        ];

        $W  = 1000; $H = 720;
        $cx = 500;  $cy = 355;
        $r  = 235;
        $nr = 50;
        $n  = count($components);

        $svg  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $W . ' ' . $H . '" '
              . 'width="' . $W . '" height="' . $H . '" style="background:#f8fafc;font-family:system-ui,ui-sans-serif,sans-serif">' . "\n";

        // subtle grid
        $svg .= '<defs><pattern id="g" width="40" height="40" patternUnits="userSpaceOnUse">'
              . '<path d="M40 0L0 0 0 40" fill="none" stroke="#e2e8f0" stroke-width="1"/>'
              . '</pattern></defs>' . "\n";
        $svg .= '<rect width="' . $W . '" height="' . $H . '" fill="url(#g)"/>' . "\n";

        // header bar
        $svg .= '<rect x="0" y="0" width="' . $W . '" height="66" fill="#1e293b"/>' . "\n";
        $svg .= '<text x="20" y="28" font-size="18" font-weight="bold" fill="white">'
              . htmlspecialchars($project, ENT_XML1) . ' — Architecture Overview</text>' . "\n";
        $svg .= '<text x="20" y="50" font-size="12" fill="#94a3b8">'
              . 'Laravel ' . htmlspecialchars($data['laravel_version'], ENT_XML1)
              . ' · PHP ' . htmlspecialchars($data['php_version'], ENT_XML1)
              . ' · laravel-architecture-discovery v' . htmlspecialchars($data['package_version'], ENT_XML1)
              . '</text>' . "\n";

        // score badge in header
        $svg .= '<rect x="820" y="10" width="160" height="46" rx="8" fill="#4f46e5"/>' . "\n";
        $svg .= '<text x="900" y="32" text-anchor="middle" font-size="11" fill="#c7d2fe">Architecture Score</text>' . "\n";
        $svg .= '<text x="900" y="52" text-anchor="middle" font-size="16" font-weight="bold" fill="white">'
              . $score . '/100 — ' . htmlspecialchars($grade, ENT_XML1) . '</text>' . "\n";

        // spokes
        foreach ($components as $i => $comp) {
            $angle = (2 * M_PI * $i / $n) - M_PI / 2;
            $nx    = (int) round($cx + $r * cos($angle));
            $ny    = (int) round($cy + $r * sin($angle));
            $svg  .= '<line x1="' . $cx . '" y1="' . $cy . '" x2="' . $nx . '" y2="' . $ny
                   . '" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="4,3"/>' . "\n";
        }

        // center circle
        $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="68" fill="#4f46e5" stroke="#6366f1" stroke-width="3"/>' . "\n";
        $projectShort = mb_strlen($project) > 14 ? mb_substr($project, 0, 13) . '…' : $project;
        $svg .= '<text x="' . $cx . '" y="' . ($cy - 10) . '" text-anchor="middle" font-size="12" font-weight="bold" fill="white">'
              . htmlspecialchars($projectShort, ENT_XML1) . '</text>' . "\n";
        $svg .= '<text x="' . $cx . '" y="' . ($cy + 16) . '" text-anchor="middle" font-size="28" font-weight="bold" fill="white">'
              . $score . '</text>' . "\n";
        $svg .= '<text x="' . $cx . '" y="' . ($cy + 34) . '" text-anchor="middle" font-size="10" fill="#c7d2fe">'
              . htmlspecialchars($grade, ENT_XML1) . '</text>' . "\n";

        // component nodes
        foreach ($components as $i => $comp) {
            [$name, $count, $color, $bg] = $comp;
            $angle = (2 * M_PI * $i / $n) - M_PI / 2;
            $nx    = (int) round($cx + $r * cos($angle));
            $ny    = (int) round($cy + $r * sin($angle));

            $svg .= '<circle cx="' . $nx . '" cy="' . $ny . '" r="' . $nr . '" fill="' . $bg
                  . '" stroke="' . $color . '" stroke-width="2.5"/>' . "\n";
            $svg .= '<text x="' . $nx . '" y="' . ($ny - 6) . '" text-anchor="middle" font-size="22" font-weight="bold" fill="'
                  . $color . '">' . $count . '</text>' . "\n";
            $svg .= '<text x="' . $nx . '" y="' . ($ny + 14) . '" text-anchor="middle" font-size="10" fill="'
                  . $color . '">' . htmlspecialchars($name, ENT_XML1) . '</text>' . "\n";
        }

        // dep-edge count badge
        $edgeCount = count($data['dependencies']['edges'] ?? []);
        $nodeCount = count($data['dependencies']['nodes'] ?? []);
        $svg .= '<rect x="' . ($W - 220) . '" y="' . ($H - 55) . '" width="210" height="40" rx="8" fill="#1e293b" opacity="0.85"/>' . "\n";
        $svg .= '<text x="' . ($W - 115) . '" y="' . ($H - 35) . '" text-anchor="middle" font-size="11" fill="#94a3b8">'
              . $nodeCount . ' dependency nodes · ' . $edgeCount . ' edges</text>' . "\n";
        $svg .= '<text x="' . ($W - 115) . '" y="' . ($H - 20) . '" text-anchor="middle" font-size="10" fill="#64748b">'
              . htmlspecialchars($data['generated_at'], ENT_XML1) . '</text>' . "\n";

        $svg .= '</svg>';

        return $svg;
    }

    private function renderMarkdown(ArchitectureReport $report): string
    {
        $data = $report->getReport();
        $out  = [];

        // ── Header ────────────────────────────────────────────────
        $out[] = "# Architecture Report — {$data['project']['name']}";
        $out[] = '';
        $out[] = "> Generated: {$data['generated_at']}  ";
        $out[] = "> Laravel {$data['laravel_version']} · PHP {$data['php_version']} · laravel-architecture-discovery v{$data['package_version']}";
        $out[] = '';
        $out[] = '---';
        $out[] = '';

        // ── Architecture Score ────────────────────────────────────
        if (!empty($data['score'])) {
            $s = $data['score'];
            $out[] = '## Architecture Score';
            $out[] = '';
            $out[] = "**{$s['score']} / {$s['max']}** — {$s['grade']}";
            $out[] = '';
            foreach ($s['checks'] as $check) {
                $icon = match ($check['status']) { 'pass' => '✔', 'warn' => '⚠', default => '✘' };
                $note = $check['note'] ? " — *{$check['note']}*" : '';
                $out[] = "{$icon} {$check['label']}{$note}";
            }
            $out[] = '';
            $out[] = '---';
            $out[] = '';
        }

        // ── Summary ───────────────────────────────────────────────
        $out[] = '## Summary';
        $out[] = '';
        $out[] = '| Component | Count |';
        $out[] = '|-----------|------:|';
        $out[] = "| Models | {$data['summary']['models']} |";
        $out[] = "| Controllers | {$data['summary']['controllers']} |";
        $out[] = "| Routes | {$data['summary']['routes']} |";
        $out[] = "| Dependency edges | " . count($data['dependencies']['edges']) . " |";

        // Route analysis
        $rs = $data['route_summary'];
        if (!empty($rs['api_versions'])) {
            $out[] = "| API Versions | " . implode(', ', array_keys($rs['api_versions'])) . " |";
        }
        $out[] = "| Named Routes | {$rs['named_count']} / {$rs['total']} |";
        $out[] = '';

        // ── Dependency Graph (Mermaid — renders on GitHub) ────────
        $deps = $data['dependencies'];
        if (!empty($deps['edges'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Dependency Graph';
            $out[] = '';
            $out[] = '```mermaid';
            $out[] = 'flowchart TD';

            $layerStyle = [
                'controller' => ':::controller',
                'service'    => ':::service',
                'repository' => ':::repository',
                'model'      => ':::model',
                'job'        => ':::job',
                'event'      => ':::event',
                'listener'   => ':::listener',
                'database'   => ':::database',
            ];
            $edgeLabel = ['injects' => '', 'uses' => 'uses', 'triggers' => 'triggers', 'persists' => 'persists'];

            // Group by layer for subgraphs
            $layerOrder  = ['controller', 'job', 'event', 'listener', 'service', 'repository', 'model'];
            $layerLabels = ['controller' => 'Controllers', 'job' => 'Jobs', 'event' => 'Events',
                'listener' => 'Listeners', 'service' => 'Services', 'repository' => 'Repositories', 'model' => 'Models'];
            $byLayer = [];
            foreach ($deps['nodes'] as $node) {
                $l = $node['layer'] ?? 'model';
                if ($l !== 'database') $byLayer[$l][] = $node['name'];
            }
            foreach ($layerOrder as $layer) {
                if (empty($byLayer[$layer])) continue;
                $out[] = "    subgraph {$layerLabels[$layer]}";
                foreach ($byLayer[$layer] as $nm) { $out[] = "        {$nm}"; }
                $out[] = '    end';
            }

            // Database cylinder node
            $hasDb = !empty(array_filter($deps['nodes'], fn($n) => ($n['layer'] ?? '') === 'database'));
            if ($hasDb) $out[] = '    Database[("Database")]';

            foreach ($deps['edges'] as $edge) {
                $lbl   = $edgeLabel[$edge['type'] ?? ''] ?? '';
                $arrow = $lbl ? "-->|\"{$lbl}\"|" : '-->';
                $fromStyle = $layerStyle[$deps['nodes'][array_search($edge['from'], array_column($deps['nodes'], 'name'))]['layer'] ?? ''] ?? '';
                $toStyle   = $layerStyle[$deps['nodes'][array_search($edge['to'],   array_column($deps['nodes'], 'name'))]['layer'] ?? ''] ?? '';
                $out[] = "    {$edge['from']}{$fromStyle} {$arrow} {$edge['to']}{$toStyle}";
            }

            $out[] = '';
            $out[] = '    classDef controller fill:#dbeafe,stroke:#3b82f6,color:#1e3a8a';
            $out[] = '    classDef service    fill:#d1fae5,stroke:#10b981,color:#064e3b';
            $out[] = '    classDef repository fill:#fef3c7,stroke:#f59e0b,color:#78350f';
            $out[] = '    classDef model      fill:#ede9fe,stroke:#8b5cf6,color:#4c1d95';
            $out[] = '    classDef job        fill:#fef9c3,stroke:#ca8a04,color:#713f12';
            $out[] = '    classDef event      fill:#fdf4ff,stroke:#a855f7,color:#581c87';
            $out[] = '    classDef listener   fill:#fce7f3,stroke:#ec4899,color:#831843';
            $out[] = '    classDef database   fill:#f1f5f9,stroke:#64748b,color:#1e293b';
            $out[] = '```';
            $out[] = '';
        }

        // ── Models ────────────────────────────────────────────────
        $out[] = '---';
        $out[] = '';
        $out[] = '## Models';
        $out[] = '';
        foreach ($data['models'] as $model) {
            $out[] = "### {$model['name']}";
            $out[] = '';
            $out[] = "**Namespace:** `{$model['namespace']}`  ";
            $out[] = "**Table:** `{$model['table']}`  ";
            if (!empty($model['fillable'])) {
                $out[] = '**Fillable:** `' . implode('`, `', $model['fillable']) . '`';
            }
            if (!empty($model['hidden'])) {
                $out[] = '**Hidden:** `' . implode('`, `', $model['hidden']) . '`';
            }
            $out[] = '';
            if (!empty($model['relationships'])) {
                $out[] = '| Method | Type | Related |';
                $out[] = '|--------|------|---------|';
                foreach ($model['relationships'] as $rel) {
                    $related = class_basename($rel['related'] ?? '—');
                    $out[] = "| `{$rel['method']}` | `{$rel['type']}` | `{$related}` |";
                }
                $out[] = '';
            }
            $out[] = '---';
            $out[] = '';
        }

        // ── Controllers ───────────────────────────────────────────
        $out[] = '## Controllers';
        $out[] = '';
        foreach ($data['controllers'] as $ctrl) {
            $out[] = "### {$ctrl['name']}";
            $out[] = '';
            $out[] = "**Namespace:** `{$ctrl['namespace']}`  ";
            $out[] = "**Methods ({$ctrl['method_count']}):** " .
                     implode(', ', array_map(fn($m) => "`{$m}`", $ctrl['methods'] ?? []));
            $out[] = '';
            $out[] = '---';
            $out[] = '';
        }

        // ── Routes ────────────────────────────────────────────────
        $out[] = '## Routes';
        $out[] = '';
        $out[] = '| Method | URI | Controller | Action | Name | Middleware |';
        $out[] = '|--------|-----|------------|--------|------|------------|';
        foreach ($data['routes'] as $route) {
            $methods = implode(',', array_filter($route['methods'] ?? [], fn($m) => $m !== 'HEAD'));
            $ctrl    = class_basename($route['controller']['class'] ?? '—');
            $action  = $route['controller']['method'] ?? '—';
            $name    = $route['name'] ?? '—';
            $mw      = implode(', ', $route['middleware'] ?? []);
            $out[] = "| {$methods} | `{$route['uri']}` | {$ctrl} | {$action} | {$name} | {$mw} |";
        }
        $out[] = '';

        return implode("\n", $out);
    }
}
