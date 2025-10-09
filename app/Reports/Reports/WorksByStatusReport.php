<?php

declare(strict_types=1);

namespace App\Reports\Reports;

use App\Models\OrganizationalUnit;
use App\Models\WorkOfExtension;
use App\Models\WorkType;
use App\Reports\ReportResult;
use App\Reports\SystemReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorksByStatusReport extends SystemReport
{
    public function key(): string
    {
        return 'works-by-status';
    }

    public function name(): string
    {
        return __('admin.reports.works_by_status.title');
    }

    public function description(): string
    {
        return __('admin.reports.works_by_status.description');
    }

    public function filters(): array
    {
        return [
            'date_from' => [
                'type' => 'date',
                'label' => __('admin.reports.filters.date_from'),
            ],
            'date_to' => [
                'type' => 'date',
                'label' => __('admin.reports.filters.date_to'),
            ],
            'work_type_id' => [
                'type' => 'select',
                'label' => __('admin.reports.filters.work_type'),
                'options' => WorkType::getActiveTypes()->map(fn ($type) => [
                    'value' => (string) $type->getKey(),
                    'label' => $type->getAttribute('name'),
                ])->all(),
            ],
            'organizational_unit_id' => [
                'type' => 'select',
                'label' => __('admin.reports.filters.organizational_unit'),
                'options' => OrganizationalUnit::query()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn ($unit) => [
                        'value' => (string) $unit->getKey(),
                        'label' => $unit->getAttribute('name'),
                    ])->all(),
            ],
        ];
    }

    public function generate(array $filters): ReportResult
    {
        $filters = $this->resolveFilters($filters);

        $query = WorkOfExtension::query()
            ->join('work_statuses', 'work_statuses.id', '=', 'work_of_extensions.current_status_id')
            ->select([
                'work_statuses.name as status_name',
                DB::raw('COUNT(work_of_extensions.id) as total'),
            ])
            ->groupBy('work_statuses.name')
            ->orderByDesc('total');

        if ($filters['date_from']) {
            $query->whereDate('work_of_extensions.created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if ($filters['date_to']) {
            $query->whereDate('work_of_extensions.created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        if ($filters['work_type_id']) {
            $query->where('work_of_extensions.work_type_id', (int) $filters['work_type_id']);
        }

        if ($filters['organizational_unit_id']) {
            $query->where('work_of_extensions.organizational_unit_id', (int) $filters['organizational_unit_id']);
        }

        $rows = $query->get()
            ->map(fn ($row) => [
                'status_name' => $row->status_name,
                'total' => (int) $row->total,
            ])
            ->all();

        $total = array_sum(array_column($rows, 'total'));

        return new ReportResult(
            columns: [
                ['key' => 'status_name', 'label' => __('admin.reports.columns.status')],
                ['key' => 'total', 'label' => __('admin.reports.columns.total')],
            ],
            rows: $rows,
            summary: [
                'total_records' => $total,
                'distinct_statuses' => count($rows),
            ]
        );
    }
}
