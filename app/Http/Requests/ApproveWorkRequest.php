<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request: Aprobar Trabajo por VIEX
 *
 * Valida los datos para la aprobación final de un trabajo por VIEX.
 */
class ApproveWorkRequest extends FormRequest
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
                'nullable',
                'string',
                'max:2000',
            ],
            'recommendations' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'certification_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'attach_documents' => [
                'nullable',
                'boolean',
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
                    __('El trabajo debe estar en estado "En VIEX - En Evaluación" para ser aprobado.')
                );
            }

            // Verificar que todas las evaluaciones estén completadas
            if ($work && !$work->allEvaluationsCompleted()) {
                $validator->errors()->add(
                    'evaluations',
                    __('No todas las evaluaciones han sido completadas. Debe esperar a que todos los evaluadores envíen sus evaluaciones.')
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
            'comments' => __('comentarios'),
            'recommendations' => __('recomendaciones'),
            'certification_date' => __('fecha de certificación'),
            'attach_documents' => __('adjuntar documentos'),
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
            'comments.max' => __('Los comentarios no pueden exceder :max caracteres.'),
            'recommendations.max' => __('Las recomendaciones no pueden exceder :max caracteres.'),
            'certification_date.date' => __('La fecha de certificación debe ser una fecha válida.'),
            'certification_date.after_or_equal' => __('La fecha de certificación no puede ser anterior a hoy.'),
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
        $data['comments'] = $data['comments'] ?? null;
        $data['recommendations'] = $data['recommendations'] ?? null;
        $data['certification_date'] = $data['certification_date'] ?? now()->addDays(7);

        return $data;
    }
}
