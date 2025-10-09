<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\OrganizationalUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationalUnitRequest extends FormRequest
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
            'parent_id' => [
                'nullable',
                'integer',
                'exists:organizational_units,id',
                function (string $attribute, $value, $fail): void {
                    /** @var OrganizationalUnit $unit */
                    $unit = $this->route('organizational_unit');

                    if (!$unit instanceof OrganizationalUnit) {
                        return;
                    }

                    if ($value === $unit->getKey()) {
                        $fail(__('Una unidad no puede ser su propio padre.'));

                        return;
                    }

                    if (in_array((int) $value, OrganizationalUnit::descendantIds($unit->getKey()), true)) {
                        $fail(__('No puedes asignar una unidad descendiente como padre.'));
                    }
                },
            ],
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
