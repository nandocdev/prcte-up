<?php

declare(strict_types=1);

namespace App\Reports;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class SystemReportManager
{
    /**
     * @var array<string, SystemReport>
     */
    private array $reports = [];

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;

        foreach (config('reports.available', []) as $key => $reportClass) {
            $report = $this->container->make($reportClass);

            if (! $report instanceof SystemReport) {
                throw new InvalidArgumentException(sprintf('Report class %s must extend %s', $reportClass, SystemReport::class));
            }

            $this->reports[$key] = $report;
        }
    }

    /**
     * @return array<string, SystemReport>
     */
    public function all(): array
    {
        return $this->reports;
    }

    public function get(string $key): SystemReport
    {
        if (! array_key_exists($key, $this->reports)) {
            throw new InvalidArgumentException(sprintf('Report %s is not registered', $key));
        }

        return $this->reports[$key];
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->reports);
    }
}
