<?php

declare(strict_types=1);

namespace App\Reports;

class ReportResult
{
    /**
     * @param array<int, array<string, string>> $columns
     * @param array<int, array<string, mixed>> $rows
     * @param array<string, mixed> $summary
     */
    public function __construct(
        public array $columns,
        public array $rows,
        public array $summary = []
    ) {
    }

    public function isEmpty(): bool
    {
        return count($this->rows) === 0;
    }
}
