<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\WorkEvaluator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request: Asignar Evaluador
 *
 * Valida los datos para asignar un evaluador a un trabajo de extensión.
 */
class AssignEvaluatorRequest extends FormRequest
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
        $work = $this->route('work');

        return [
            'evaluator_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($work) {
                    // Verificar que el usuario tiene rol de evaluador
                    $user = User::find($value);
                    if (!$user || !$user->hasRole('evaluador')) {
                        $fail(__('El usuario seleccionado no es un evaluador válido.'));
                    }

                    // Verificar que no esté ya asignado a este trabajo
                    if ($work && $work->workEvaluators()->where('evaluator_user_id', $value)->exists()) {
                        $fail(__('Este evaluador ya está asignado a este trabajo.'));
                    }
                },
            ],
            'role_evaluator' => [
                'required',
                'string',
                Rule::in([WorkEvaluator::ROLE_LEAD, WorkEvaluator::ROLE_EVALUATOR]),
            ],
            'assignment_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'evaluator_id' => __('evaluador'),
            'role_evaluator' => __('rol'),
            'assignment_notes' => __('notas de asignación'),
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
            'evaluator_id.required' => __('Debe seleccionar un evaluador.'),
            'evaluator_id.exists' => __('El evaluador seleccionado no existe.'),
            'role_evaluator.required' => __('Debe especificar el rol del evaluador.'),
            'role_evaluator.in' => __('El rol del evaluador no es válido. Debe ser "Evaluador Principal" o "Evaluador".'),
            'assignment_notes.max' => __('Las notas de asignación no pueden exceder :max caracteres.'),
        ];
    }

    /**
     * Get the validated data with additional processing.
     *
     * @return array
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        // Asegurar que assignment_notes no sea null
        if (!isset($data['assignment_notes'])) {
            $data['assignment_notes'] = null;
        }

        return $data;
    }
}
