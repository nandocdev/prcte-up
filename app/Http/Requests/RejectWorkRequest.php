<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request: Rechazar Trabajo por VIEX
 *
 * Valida los datos para el rechazo de un trabajo por VIEX.
 */
class RejectWorkRequest extends FormRequest
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
            'reason' => [
                'required',
                'string',
                'min:20',
                'max:2000',
            ],
            'recommendations' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'allow_resubmit' => [
                'nullable',
                'boolean',
            ],
            'resubmit_deadline' => [
                'nullable',
                'required_if:allow_resubmit,true',
                'date',
                'after:today',
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

            if ($work && $work->currentStatus->name !== 'En VIEX - En Evaluación') {
                $validator->errors()->add(
                    'work',
                    __('El trabajo debe estar en estado "En VIEX - En Evaluación" para ser rechazado.')
                );
            }

            // Si permite reenvío, verificar deadline
            if ($this->boolean('allow_resubmit') && !$this->has('resubmit_deadline')) {
                $validator->errors()->add(
                    'resubmit_deadline',
                    __('Debe especificar una fecha límite para el reenvío.')
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
            'reason' => __('motivo del rechazo'),
            'recommendations' => __('recomendaciones'),
            'allow_resubmit' => __('permitir reenvío'),
            'resubmit_deadline' => __('fecha límite para reenvío'),
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
            'reason.required' => __('Debe especificar el motivo del rechazo.'),
            'reason.min' => __('El motivo del rechazo debe tener al menos :min caracteres para proporcionar una explicación adecuada.'),
            'reason.max' => __('El motivo del rechazo no puede exceder :max caracteres.'),
            'recommendations.max' => __('Las recomendaciones no pueden exceder :max caracteres.'),
            'resubmit_deadline.required_if' => __('Debe especificar una fecha límite si permite el reenvío.'),
            'resubmit_deadline.date' => __('La fecha límite debe ser una fecha válida.'),
            'resubmit_deadline.after' => __('La fecha límite debe ser posterior a hoy.'),
        ];
    }

    /**
     * Get the validated data with defaults.
     *
     * @return array
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        // Establecer valores por defecto
        $data['recommendations'] = $data['recommendations'] ?? null;
        $data['allow_resubmit'] = $data['allow_resubmit'] ?? true;
        $data['resubmit_deadline'] = $data['resubmit_deadline'] ?? now()->addDays(30);

        return $data;
    }
}
