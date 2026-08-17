<?php

namespace Viitorcloud\LaravelArchitectureDiscovery;

use Viitorcloud\LaravelArchitectureDiscovery\Services\ArchitectureReport;
use Viitorcloud\LaravelArchitectureDiscovery\Services\ArchitectureScanner;

class ArchitectureDiscovery
{
    const VERSION = '0.3.9';

    public function __construct(protected ArchitectureScanner $scanner) {}

    public function discover(): ArchitectureReport
    {
        return $this->scanner->scan();
    }
}
