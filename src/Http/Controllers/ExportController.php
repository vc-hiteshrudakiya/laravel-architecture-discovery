<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Http\Controllers;

use Illuminate\Routing\Controller;
use Viitorcloud\LaravelArchitectureDiscovery\ArchitectureDiscovery;
use Viitorcloud\LaravelArchitectureDiscovery\Services\ReportExporter;

class ExportController extends Controller
{
    public function __invoke(ArchitectureDiscovery $discovery, ReportExporter $exporter, string $format)
    {
        $allowed = ['html', 'svg'];

        if (!in_array($format, $allowed, true)) {
            abort(404);
        }

        $report = $discovery->discover();

        if ($format === 'html') {
            return response($exporter->renderHtml($report), 200, [
                'Content-Type'        => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="architecture.html"',
            ]);
        }

        return response($exporter->renderSvg($report), 200, [
            'Content-Type'        => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="architecture.svg"',
        ]);
    }
}
