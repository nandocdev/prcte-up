<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\WorkStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['super_admin', 'viex_admin']) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $status = $this->route('work_status');
        $isActive = true;

        if ($status instanceof WorkStatus) {
            $isActive = (bool) $status->is_active;
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
        $status = $this->route('work_status');
        $statusId = $status instanceof WorkStatus ? $status->getKey() : (int) $status;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('work_statuses', 'name')->ignore($statusId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('El nombre del estado es obligatorio.'),
            'name.unique' => __('Ya existe un estado con ese nombre.'),
            'is_active.required' => __('Debes indicar si el estado estará activo o inactivo.'),
        ];
    }
}
