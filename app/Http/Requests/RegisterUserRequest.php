<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterUserRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cedula' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'professor_code' => ['nullable', 'string', 'max:20', 'unique:' . User::class],
            'main_organizational_unit_id' => ['nullable', 'exists:organizational_units,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'name.required' => __('El nombre completo es obligatorio.'),
            'email.required' => __('El correo electrónico es obligatorio.'),
            'email.unique' => __('Este correo electrónico ya está registrado.'),
            'password.required' => __('La contraseña es obligatoria.'),
            'password.confirmed' => __('La confirmación de contraseña no coincide.'),
            'cedula.required' => __('La cédula de identidad es obligatoria.'),
            'cedula.unique' => __('Esta cédula ya está registrada en el sistema.'),
            'professor_code.unique' => __('Este código de profesor ya está asignado.'),
            'main_organizational_unit_id.exists' => __('La unidad organizacional seleccionada no existe.'),
        ];
    }
}
