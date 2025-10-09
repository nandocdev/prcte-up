<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SyncEvaluationCriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'viex_admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $criteria = collect($this->input('criteria', []))
            ->map(function ($item) {
                $item = is_array($item) ? $item : [];

                $isActive = array_key_exists('is_active', $item)
                    ? (filter_var($item['is_active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false)
                    : false;

                return [
                    'id' => isset($item['id']) ? (int) $item['id'] : null,
                    'weight' => isset($item['weight']) ? (int) $item['weight'] : null,
                    'order' => isset($item['order']) ? (int) $item['order'] : null,
                    'is_active' => $isActive,
                ];
            })
            ->values()
            ->all();

        $this->merge(['criteria' => $criteria]);
    }

    public function rules(): array
    {
        return [
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*.id' => ['required', 'integer', 'exists:evaluation_criteria,id'],
            'criteria.*.weight' => ['required', 'integer', 'min:0', 'max:100'],
            'criteria.*.order' => ['required', 'integer', 'min:0', 'max:1000'],
            'criteria.*.is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $activeTotal = collect($this->input('criteria', []))
                ->filter(fn ($item) => !empty($item['is_active']))
                ->sum(fn ($item) => (int) $item['weight']);

            if ($activeTotal !== 100) {
                $validator->errors()->add(
                    'criteria',
                    __('La suma de los pesos de criterios activos debe ser 100. Actualmente es :total.', [
                        'total' => $activeTotal,
                    ])
                );
            }
        });
    }
}
