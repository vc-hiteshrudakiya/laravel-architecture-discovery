<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\AI\DTO;

class AIAnalysisResponse
{
    public function __construct(
        public readonly string $summary,
        public readonly int    $score,
        public readonly array  $problems,
        public readonly array  $suggestions,
        public readonly array  $bestPractices,
        public readonly array  $solidReview,
        public readonly array  $laravelBestPractices,
        public readonly string $rawResponse,
        public readonly string $provider,
        public readonly string $model,
    ) {}

    public function toArray(): array
    {
        return [
            'summary'                => $this->summary,
            'score'                  => $this->score,
            'problems'               => $this->problems,
            'suggestions'            => $this->suggestions,
            'best_practices'         => $this->bestPractices,
            'solid_review'           => $this->solidReview,
            'laravel_best_practices' => $this->laravelBestPractices,
            'provider'               => $this->provider,
            'model'                  => $this->model,
        ];
    }

    public static function fromArray(array $data, string $provider, string $model, string $raw): self
    {
        return new self(
            summary:              $data['summary']                ?? 'No summary available.',
            score:                (int) ($data['score']           ?? 0),
            problems:             $data['problems']               ?? [],
            suggestions:          $data['suggestions']            ?? [],
            bestPractices:        $data['best_practices']         ?? [],
            solidReview:          $data['solid_review']           ?? [],
            laravelBestPractices: $data['laravel_best_practices'] ?? [],
            rawResponse:          $raw,
            provider:             $provider,
            model:                $model,
        );
    }

    public static function error(string $message, string $provider, string $model): self
    {
        return new self(
            summary:              $message,
            score:                0,
            problems:             [['title' => 'Analysis Failed', 'description' => $message, 'severity' => 'error', 'location' => '']],
            suggestions:          [],
            bestPractices:        [],
            solidReview:          [],
            laravelBestPractices: [],
            rawResponse:          $message,
            provider:             $provider,
            model:                $model,
        );
    }
}
