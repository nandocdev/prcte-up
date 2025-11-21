<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OracleLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permitir a todos los usuarios no autenticados hacer login
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'estamento' => 'required|in:P,A,E', // P=Profesor, A=Administrativo, E=Estudiante
            'provincia' => 'required|string|size:1',
            'clase' => 'required|string|size:1',
            'tomo' => 'required|string|max:4',
            'folio' => 'required|string|max:6',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'estamento.required' => 'Debe seleccionar un estamento.',
            'estamento.in' => 'El estamento seleccionado no es válido.',
            'provincia.required' => 'La provincia de la cédula es obligatoria.',
            'provincia.size' => 'La provincia debe tener 1 carácter.',
            'clase.required' => 'La clase de la cédula es obligatoria.',
            'clase.size' => 'La clase debe tener 1 carácter.',
            'tomo.required' => 'El tomo de la cédula es obligatorio.',
            'tomo.max' => 'El tomo no puede tener más de 4 caracteres.',
            'folio.required' => 'El folio de la cédula es obligatorio.',
            'folio.max' => 'El folio no puede tener más de 6 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'estamento' => 'estamento',
            'provincia' => 'provincia',
            'clase' => 'clase',
            'tomo' => 'tomo',
            'folio' => 'folio',
            'password' => 'contraseña',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir a mayúsculas los campos de cédula
        $this->merge([
            'provincia' => strtoupper($this->provincia),
            'clase' => strtoupper($this->clase),
            'tomo' => strtoupper($this->tomo),
            'folio' => strtoupper($this->folio),
        ]);
    }
}
