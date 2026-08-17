<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Commands;

use Illuminate\Console\Command;
use Viitorcloud\LaravelArchitectureDiscovery\ArchitectureDiscovery;
use Viitorcloud\LaravelArchitectureDiscovery\Services\ReportExporter;

class DiscoverArchitectureCommand extends Command
{
    protected $signature = 'architecture:discover
                            {--format= : Override output format — json, html, markdown, or svg}
                            {--output= : Custom output path (single-format runs only)}';

    protected $description = 'Discover and document Laravel application architecture';

    private const EXTENSIONS = ['json' => 'json', 'html' => 'html', 'markdown' => 'md', 'svg' => 'svg'];
    private const GRADE_COLORS = [
        'Excellent' => 'green', 'Good' => 'cyan', 'Fair' => 'yellow', 'Needs Work' => 'red',
    ];

    public function handle(ArchitectureDiscovery $discovery, ReportExporter $exporter): int
    {
        // Resolve output formats — explicit flag overrides config
        $formatFlag = strtolower(trim($this->option('format') ?? ''));

        if ($formatFlag !== '') {
            if (!array_key_exists($formatFlag, self::EXTENSIONS)) {
                $this->error("Invalid format \"{$formatFlag}\". Supported: " . implode(', ', array_keys(self::EXTENSIONS)));
                return self::FAILURE;
            }
            $formats = [$formatFlag];
        } else {
            $configured = config('architecture-discovery.output', ['json']);
            $formats    = array_filter((array) $configured, fn($f) => array_key_exists($f, self::EXTENSIONS));

            if (empty($formats)) {
                $this->error('No valid output formats configured. Check architecture-discovery.output in config.');
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->line('  <fg=white;options=bold>Scanning Project...</>');
        $this->newLine();

        $report  = $discovery->discover();
        $data    = $report->getReport();
        $summary = $data['summary'];
        $score   = $data['score'];

        $this->line('  <fg=green>✓</> ' . str_pad('Models', 18) . '<fg=white;options=bold>' . $summary['models'] . '</>');
        $this->line('  <fg=green>✓</> ' . str_pad('Controllers', 18) . '<fg=white;options=bold>' . $summary['controllers'] . '</>');
        $this->line('  <fg=green>✓</> ' . str_pad('Routes', 18) . '<fg=white;options=bold>' . $summary['routes'] . '</>');
        $this->line('  <fg=green>✓</> ' . str_pad('Dependencies', 18) . '<fg=white;options=bold>' . count($data['dependencies']['edges']) . '</>');

        $this->newLine();
        $this->line('  <fg=gray>⏱  ' . $data['performance']['execution_time_ms'] . 'ms &nbsp; 💾 ' . $data['performance']['memory_usage_mb'] . 'MB</>');
        $this->newLine();

        // Score line
        $gradeColor = self::GRADE_COLORS[$score['grade'] ?? 'Needs Work'] ?? 'white';
        $this->line(
            '  ' . str_pad('Architecture Score', 18) .
            "<fg=white;options=bold>{$score['score']}</>/100  " .
            "<fg={$gradeColor};options=bold>{$score['grade']}</>"
        );
        $this->newLine();

        // Export each configured format
        $customOutput = $this->option('output');
        foreach ($formats as $format) {
            $ext    = self::EXTENSIONS[$format];
            $output = (count($formats) === 1 && $customOutput)
                ? $customOutput
                : storage_path("architecture/report.{$ext}");

            $exporter->export($report, $format, $output);
            $this->line("  <fg=green>✓</> <fg=gray>Report ({$format}):</> <fg=yellow>{$output}</>");
        }

        $this->newLine();
        $this->line('  <fg=green;options=bold>✓ Completed Successfully.</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
