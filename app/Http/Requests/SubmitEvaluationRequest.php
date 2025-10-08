<?php

namespace App\Http\Requests;

use App\Models\EvaluationCriteria;
use App\Models\WorkEvaluation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request: Enviar Evaluación
 *
 * Valida los datos para enviar una evaluación de un trabajo de extensión.
 */
class SubmitEvaluationRequest extends FormRequest
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
        $isSubmitFinal = $this->boolean('submit_final');

        return [
            // Evaluación general
            'general_comments' => [
                $isSubmitFinal ? 'required' : 'nullable',
                'string',
                'max:5000',
            ],
            'strengths' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'weaknesses' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'recommendations' => [
                $isSubmitFinal ? 'required' : 'nullable',
                'string',
                'max:2000',
            ],

            // Decisión final
            'final_decision' => [
                'required',
                'string',
                Rule::in([
                    WorkEvaluation::DECISION_APPROVE,
                    WorkEvaluation::DECISION_APPROVE_WITH_CONDITIONS,
                    WorkEvaluation::DECISION_REJECT,
                    WorkEvaluation::DECISION_PENDING,
                ]),
            ],
            'decision_justification' => [
                'required_unless:final_decision,' . WorkEvaluation::DECISION_PENDING,
                'nullable',
                'string',
                'max:2000',
            ],

            // Criterios de evaluación
            'criteria' => [
                'required',
                'array',
                'min:1',
            ],
            'criteria.*.score' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    // Extraer el ID del criterio del nombre del atributo
                    preg_match('/criteria\.(\d+)\.score/', $attribute, $matches);
                    if (isset($matches[1])) {
                        $criteriaId = $matches[1];
                        $criteria = EvaluationCriteria::find($criteriaId);
                        
                        if ($criteria && $value > $criteria->max_score) {
                            $fail(__('La puntuación no puede ser mayor a :max.', ['max' => $criteria->max_score]));
                        }
                    }
                },
            ],
            'criteria.*.comments' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'criteria.*.evidence' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Flag de envío final
            'submit_final' => [
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
            // Si es envío final, verificar que todos los criterios requeridos tengan puntuación
            if ($this->boolean('submit_final')) {
                $this->validateRequiredCriteria($validator);
            }
        });
    }

    /**
     * Validar que todos los criterios requeridos tengan puntuación.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function validateRequiredCriteria($validator): void
    {
        $requiredCriteria = EvaluationCriteria::required()->active()->pluck('id');
        $submittedCriteria = collect($this->input('criteria', []))->keys();

        $missingCriteria = $requiredCriteria->diff($submittedCriteria);

        if ($missingCriteria->isNotEmpty()) {
            $criteriaNames = EvaluationCriteria::whereIn('id', $missingCriteria)
                ->pluck('name')
                ->join(', ');

            $validator->errors()->add(
                'criteria',
                __('Debe evaluar todos los criterios requeridos: :criteria', ['criteria' => $criteriaNames])
            );
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'general_comments' => __('comentarios generales'),
            'strengths' => __('fortalezas'),
            'weaknesses' => __('debilidades'),
            'recommendations' => __('recomendaciones'),
            'final_decision' => __('decisión final'),
            'decision_justification' => __('justificación de la decisión'),
            'criteria' => __('criterios de evaluación'),
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
            'general_comments.required' => __('Los comentarios generales son obligatorios para enviar la evaluación.'),
            'general_comments.max' => __('Los comentarios generales no pueden exceder :max caracteres.'),
            'recommendations.required' => __('Las recomendaciones son obligatorias para enviar la evaluación.'),
            'final_decision.required' => __('Debe seleccionar una decisión final.'),
            'final_decision.in' => __('La decisión final no es válida.'),
            'decision_justification.required_unless' => __('Debe justificar su decisión.'),
            'criteria.required' => __('Debe evaluar al menos un criterio.'),
            'criteria.*.score.required' => __('La puntuación es obligatoria.'),
            'criteria.*.score.integer' => __('La puntuación debe ser un número entero.'),
            'criteria.*.score.min' => __('La puntuación no puede ser negativa.'),
        ];
    }
}
