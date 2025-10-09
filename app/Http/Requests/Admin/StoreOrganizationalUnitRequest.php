<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\OrganizationalUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationalUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super_admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'parent_id' => $this->filled('parent_id') ? (int) $this->input('parent_id') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(OrganizationalUnit::typeOptions()))],
            'parent_id' => ['nullable', 'integer', 'exists:organizational_units,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('El nombre de la unidad es obligatorio.'),
            'type.required' => __('El tipo de unidad es obligatorio.'),
            'type.in' => __('El tipo de unidad seleccionado no es válido.'),
            'parent_id.exists' => __('La unidad padre seleccionada no existe.'),
        ];
    }
}
