<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\InstitutionalProjectType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstitutionalProjectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'viex_admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $type = $this->route('institutional_project_type');
        $isActive = true;

        if ($type instanceof InstitutionalProjectType) {
            $isActive = (bool) $type->is_active;
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
        $type = $this->route('institutional_project_type');
        $typeId = $type instanceof InstitutionalProjectType ? $type->getKey() : (int) $type;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutional_project_types', 'name')->ignore($typeId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('El nombre del tipo institucional es obligatorio.'),
            'name.unique' => __('Ya existe un tipo institucional con ese nombre.'),
            'is_active.required' => __('Debes indicar si el tipo estará activo o inactivo.'),
        ];
    }
}
