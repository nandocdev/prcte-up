<?php

declare(strict_types=1);

namespace App\Reports;

abstract class SystemReport
{
    abstract public function key(): string;

    abstract public function name(): string;

    abstract public function description(): string;

    /**
     * @return array<string, array<string, mixed>>
     */
    abstract public function filters(): array;

    abstract public function generate(array $filters): ReportResult;

    public function defaultFilters(): array
    {
        return [
            'date_from' => null,
            'date_to' => null,
            'work_type_id' => null,
            'organizational_unit_id' => null,
        ];
    }

    public function resolveFilters(array $filters): array
    {
        return array_merge($this->defaultFilters(), array_filter(
            $filters,
            static fn ($value) => $value !== null && $value !== ''
        ));
    }
}
