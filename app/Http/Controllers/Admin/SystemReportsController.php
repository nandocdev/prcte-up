<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateReportRequest;
use App\Reports\ReportResult;
use App\Reports\SystemReportManager;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemReportsController extends Controller
{
    private SystemReportManager $manager;

    public function __construct(SystemReportManager $manager)
    {
        $this->manager = $manager;
    }

    public function index(): View
    {
        return view('admin.reports.index', [
            'reports' => $this->manager->all(),
        ]);
    }

    public function show(GenerateReportRequest $request, string $report): View
    {
        $reportInstance = $this->manager->get($request->reportKey());

        $activeFilters = $reportInstance->resolveFilters($request->filters());
        $selectedFilters = array_merge($reportInstance->defaultFilters(), $request->filters());
        $shouldGenerate = (bool) $request->query('apply');

        $result = $shouldGenerate ? $reportInstance->generate($activeFilters) : null;

        $downloadParams = $activeFilters;
        $downloadParams['format'] = 'csv';

        return view('admin.reports.show', [
            'report' => $reportInstance,
            'reportKey' => $reportInstance->key(),
            'filters' => $selectedFilters,
            'filterDefinitions' => $reportInstance->filters(),
            'result' => $result,
            'downloadParams' => $downloadParams,
        ]);
    }

    public function download(GenerateReportRequest $request, string $report): StreamedResponse
    {
        $reportInstance = $this->manager->get($request->reportKey());
        $filters = $reportInstance->resolveFilters($request->filters());
        $result = $reportInstance->generate($filters);

        $fileName = sprintf('%s_%s.csv', $reportInstance->key(), now()->format('Ymd_His'));

        return response()->streamDownload(function () use ($result) {
            $handle = fopen('php://output', 'wb');

            if (! $handle) {
                return;
            }

            $this->writeCsv($handle, $result);

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function writeCsv($handle, ReportResult $result): void
    {
        $headers = array_map(static fn (array $column) => $column['label'], $result->columns);
        fputcsv($handle, $headers);

        foreach ($result->rows as $row) {
            $line = [];

            foreach ($result->columns as $column) {
                $line[] = data_get($row, $column['key']);
            }

            fputcsv($handle, $line);
        }
    }
}
