<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()->hasRole('super_admin');
    }

    public function rules(): array {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array {
        return [
            'name.required' => __('El nombre del rol es obligatorio.'),
            'name.unique' => __('Ya existe un rol con este nombre.'),
        ];
    }
}
