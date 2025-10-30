<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstitutionalProjectTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'viex_admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:inst_project_types,name'],
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
