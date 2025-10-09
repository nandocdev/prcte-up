<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEvaluationCriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'viex_admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
            'is_required' => $this->has('is_required') ? $this->boolean('is_required') : false,
        ]);
    }

    public function rules(): array
    {
        $evaluationCriteria = $this->route('evaluationCriteria');

        return [
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('evaluation_criteria', 'name')->ignore($evaluationCriteria?->id),
            ],
            'description' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'max_score' => ['required', 'integer', 'min:1', 'max:100'],
            'weight' => ['required', 'integer', 'min:0', 'max:100'],
            'order' => ['required', 'integer', 'min:0', 'max:1000'],
            'is_active' => ['required', 'boolean'],
            'is_required' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('El nombre del criterio es obligatorio.'),
            'name.unique' => __('Ya existe un criterio con ese nombre.'),
            'description.required' => __('La descripcion del criterio es obligatoria.'),
            'max_score.required' => __('Debes indicar la puntuacion maxima.'),
            'max_score.min' => __('La puntuacion maxima debe ser al menos 1.'),
            'weight.required' => __('Debes indicar el peso del criterio.'),
            'order.required' => __('Debes indicar el orden de visualizacion.'),
        ];
    }
}
