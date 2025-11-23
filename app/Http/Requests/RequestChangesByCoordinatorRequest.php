<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request: Solicitar Correcciones por Coordinador
 *
 * Valida los datos para solicitar correcciones a un trabajo por coordinador.
 */
class RequestChangesByCoordinatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // La autorización se maneja en el Policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comments' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $work = $this->route('work');

            // Verificar que el trabajo esté en el estado correcto
            if ($work && !$work->currentStatus) {
                $validator->errors()->add('work', __('El trabajo no tiene un estado definido.'));
                return;
            }

            $validStatuses = ['Enviado a Coordinador', 'En Revisión Coordinador'];
            if ($work && !in_array($work->currentStatus->name, $validStatuses)) {
                $validator->errors()->add(
                    'work',
                    __('El trabajo debe estar en estado "Enviado a Coordinador" o "En Revisión Coordinador" para solicitar correcciones.')
                );
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'comments' => __('comentarios de corrección'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'comments.required' => __('Debe especificar las correcciones solicitadas.'),
            'comments.min' => __('Los comentarios de corrección deben tener al menos :min caracteres para proporcionar una explicación adecuada.'),
            'comments.max' => __('Los comentarios de corrección no pueden exceder :max caracteres.'),
        ];
    }
}