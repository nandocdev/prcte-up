<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\WorkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'viex_admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $workType = $this->route('work_type');
        $isActive = true;

        if ($workType instanceof WorkType) {
            $isActive = (bool) $workType->is_active;
        }

        if ($this->has('is_active')) {
            $isActive = $this->boolean('is_active');
        }

        $this->merge([
            'is_active' => $isActive,
        ]);
    }

    public function rules(): array
    {
        $workType = $this->route('work_type');
        $workTypeId = $workType instanceof WorkType ? $workType->getKey() : (int) $workType;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('work_type', 'name')->ignore($workTypeId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('El nombre del tipo de trabajo es obligatorio.'),
            'name.unique' => __('Ya existe un tipo de trabajo con ese nombre.'),
            'is_active.required' => __('Debes indicar si el tipo estará activo o inactivo.'),
        ];
    }
}
