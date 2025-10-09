<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Reports\SystemReportManager;
use App\Models\WorkEvaluation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'report' => $this->route('report') ?? $this->input('report'),
            'format' => $this->input('format', 'view'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('works.generate-report') ?? false;
    }

    public function rules(): array
    {
        $reportKeys = app(SystemReportManager::class)->keys();

        return [
            'report' => ['required', 'string', Rule::in($reportKeys)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'work_type_id' => ['nullable', 'integer', 'exists:work_type,id'],
            'organizational_unit_id' => ['nullable', 'integer', 'exists:organizational_units,id'],
            'final_decision' => ['nullable', 'string', Rule::in([
                WorkEvaluation::DECISION_APPROVE,
                WorkEvaluation::DECISION_APPROVE_WITH_CONDITIONS,
                WorkEvaluation::DECISION_REJECT,
                WorkEvaluation::DECISION_PENDING,
            ])],
            'format' => ['required', 'string', Rule::in(['view', 'csv'])],
        ];
    }

    public function reportKey(): string
    {
        return (string) $this->validated('report');
    }

    public function filters(): array
    {
        return [
            'date_from' => $this->validated('date_from'),
            'date_to' => $this->validated('date_to'),
            'work_type_id' => $this->validated('work_type_id'),
            'organizational_unit_id' => $this->validated('organizational_unit_id'),
            'final_decision' => $this->validated('final_decision'),
        ];
    }

    public function wantsCsv(): bool
    {
        return $this->validated('format') === 'csv';
    }
}
