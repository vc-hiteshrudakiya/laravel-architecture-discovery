<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Http\Controllers;

use Illuminate\Routing\Controller;
use Viitorcloud\LaravelArchitectureDiscovery\ArchitectureDiscovery;

class DashboardController extends Controller
{
    public function __invoke(ArchitectureDiscovery $discovery)
    {
        $report = $discovery->discover();
        $data   = $report->getReport();

        return view('architecture-discovery::dashboard', compact('data'));
    }
}
