<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()->hasRole('super_admin');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : $this->isMethod('post'),
        ]);
    }

    public function rules(): array {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'professor_code' => ['nullable', 'string', 'max:20', 'unique:users'],
            'organizational_unit_id' => ['required', 'exists:organizational_units,id'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array {
        return [
            'name.required' => __('El nombre es obligatorio.'),
            'email.required' => __('El correo electrónico es obligatorio.'),
            'email.email' => __('El correo electrónico debe ser válido.'),
            'email.unique' => __('Este correo electrónico ya está en uso.'),
            'password.required' => __('La contraseña es obligatoria.'),
            'password.min' => __('La contraseña debe tener al menos 8 caracteres.'),
            'password.confirmed' => __('La confirmación de contraseña no coincide.'),
            'professor_code.unique' => __('Este código de profesor ya está en uso.'),
            'organizational_unit_id.required' => __('La unidad organizacional es obligatoria.'),
            'organizational_unit_id.exists' => __('La unidad organizacional seleccionada no es válida.'),
            'is_active.boolean' => __('El estado del usuario no es válido.'),
        ];
    }
}
