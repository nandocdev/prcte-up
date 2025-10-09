<?php

declare(strict_types=1);

namespace App\Reports\Reports;

use App\Models\OrganizationalUnit;
use App\Models\WorkEvaluation;
use App\Models\WorkType;
use App\Reports\ReportResult;
use App\Reports\SystemReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EvaluationStatisticsReport extends SystemReport
{
    public function key(): string
    {
        return 'evaluation-statistics';
    }

    public function name(): string
    {
        return __('admin.reports.evaluation_statistics.title');
    }

    public function description(): string
    {
        return __('admin.reports.evaluation_statistics.description');
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
            'final_decision' => [
                'type' => 'select',
                'label' => __('admin.reports.filters.final_decision'),
                'options' => [
                    ['value' => WorkEvaluation::DECISION_APPROVE, 'label' => __('admin.reports.decisions.approve')],
                    ['value' => WorkEvaluation::DECISION_APPROVE_WITH_CONDITIONS, 'label' => __('admin.reports.decisions.approve_with_conditions')],
                    ['value' => WorkEvaluation::DECISION_REJECT, 'label' => __('admin.reports.decisions.reject')],
                    ['value' => WorkEvaluation::DECISION_PENDING, 'label' => __('admin.reports.decisions.pending')],
                ],
            ],
        ];
    }

    public function generate(array $filters): ReportResult
    {
        $filters = $this->resolveFilters($filters);

        $query = WorkEvaluation::query()
            ->select([
                'work_evaluations.final_decision',
                DB::raw('COUNT(work_evaluations.id) as total'),
                DB::raw('AVG(work_evaluations.total_score) as avg_total_score'),
                DB::raw('AVG(work_evaluations.weighted_score) as avg_weighted_score'),
            ])
            ->whereNotNull('work_evaluations.submitted_at')
            ->groupBy('work_evaluations.final_decision')
            ->orderByDesc('total')
            ->join('work_of_extensions', 'work_of_extensions.id', '=', 'work_evaluations.work_of_extension_id');

        if ($filters['date_from']) {
            $query->whereDate('work_evaluations.submitted_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if ($filters['date_to']) {
            $query->whereDate('work_evaluations.submitted_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        if ($filters['work_type_id']) {
            $query->where('work_of_extensions.work_type_id', (int) $filters['work_type_id']);
        }

        if ($filters['organizational_unit_id']) {
            $unitIds = OrganizationalUnit::descendantIds((int) $filters['organizational_unit_id']);
            $query->whereIn('work_of_extensions.organizational_unit_id', $unitIds);
        }

        if ($filters['final_decision']) {
            $query->where('work_evaluations.final_decision', $filters['final_decision']);
        }

        $rows = $query->get()
            ->map(function ($row) {
                return [
                    'decision' => $row->final_decision,
                    'decision_label' => $this->resolveDecisionLabel((string) $row->final_decision),
                    'total' => (int) $row->total,
                    'avg_total_score' => $row->avg_total_score !== null ? round((float) $row->avg_total_score, 2) : null,
                    'avg_weighted_score' => $row->avg_weighted_score !== null ? round((float) $row->avg_weighted_score, 2) : null,
                ];
            })
            ->all();

        $totalEvaluations = array_sum(array_column($rows, 'total'));
        $averageTotalScore = $this->calculateGlobalAverage($rows, 'avg_total_score');
        $averageWeightedScore = $this->calculateGlobalAverage($rows, 'avg_weighted_score');

        return new ReportResult(
            columns: [
                ['key' => 'decision_label', 'label' => __('admin.reports.columns.final_decision')],
                ['key' => 'total', 'label' => __('admin.reports.columns.total_evaluations')],
                ['key' => 'avg_total_score', 'label' => __('admin.reports.columns.average_score')],
                ['key' => 'avg_weighted_score', 'label' => __('admin.reports.columns.average_weighted_score')],
            ],
            rows: $rows,
            summary: [
                'total_evaluations' => $totalEvaluations,
                'average_total_score' => $averageTotalScore,
                'average_weighted_score' => $averageWeightedScore,
            ]
        );
    }

    private function resolveDecisionLabel(string $decision): string
    {
        return match ($decision) {
            WorkEvaluation::DECISION_APPROVE => __('admin.reports.decisions.approve'),
            WorkEvaluation::DECISION_APPROVE_WITH_CONDITIONS => __('admin.reports.decisions.approve_with_conditions'),
            WorkEvaluation::DECISION_REJECT => __('admin.reports.decisions.reject'),
            WorkEvaluation::DECISION_PENDING => __('admin.reports.decisions.pending'),
            default => $decision,
        };
    }

    private function calculateGlobalAverage(array $rows, string $key): ?float
    {
        $values = array_filter(
            array_column($rows, $key),
            static fn ($value) => $value !== null
        );

        if (count($values) === 0) {
            return null;
        }

        return round(array_sum($values) / count($values), 2);
    }
}
