<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validación de registro de trabajos de extensión
 *
 * Implementa validaciones según los campos requeridos del formulario oficial VIEX
 */
class RegisterWorkRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        // Autorización delegada a Policy
        return $this->user()->can('create', \App\Models\WorkOfExtension::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            // Datos generales del trabajo
            'title' => 'required|string|max:500|min:10',
            'work_type_id' => 'required|exists:work_type,id',
            'organizational_unit_id' => 'required|exists:organizational_units,id',
            'description' => 'required|string|min:50|max:2000',

            // Fechas del trabajo
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',

            // Información académica
            'academic_period' => 'required|string|max:50',
            'publication_consent' => 'boolean',

            // Participantes adicionales (opcional)
            'participants' => 'nullable|array',
            'participants.*.user_id' => 'exists:users,id',
            'participants.*.role' => 'required_with:participants.*.user_id|string|max:100',

            // Archivos de evidencia (según tipo de trabajo)
            'evidences' => 'nullable|array',
            'evidences.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array {
        return [
            'title.required' => __('El título del trabajo es obligatorio.'),
            'title.min' => __('El título debe tener al menos 10 caracteres.'),
            'title.max' => __('El título no puede exceder 500 caracteres.'),

            'work_type_id.required' => __('Debe seleccionar un tipo de trabajo.'),
            'work_type_id.exists' => __('El tipo de trabajo seleccionado no es válido.'),

            'organizational_unit_id.required' => __('Debe seleccionar una unidad organizacional.'),
            'organizational_unit_id.exists' => __('La unidad organizacional seleccionada no es válida.'),

            'description.required' => __('La descripción del trabajo es obligatoria.'),
            'description.min' => __('La descripción debe tener al menos 50 caracteres.'),
            'description.max' => __('La descripción no puede exceder 2000 caracteres.'),

            'start_date.required' => __('La fecha de inicio es obligatoria.'),
            'start_date.after_or_equal' => __('La fecha de inicio no puede ser anterior a hoy.'),

            'end_date.required' => __('La fecha de finalización es obligatoria.'),
            'end_date.after' => __('La fecha de finalización debe ser posterior a la fecha de inicio.'),

            'academic_period.required' => __('El período académico es obligatorio.'),
            'academic_period.max' => __('El período académico no puede exceder 50 caracteres.'),

            'participants.*.user_id.exists' => __('Uno de los participantes seleccionados no existe.'),
            'participants.*.role.required_with' => __('Debe especificar el rol del participante.'),

            'evidences.*.file' => __('Las evidencias deben ser archivos válidos.'),
            'evidences.*.mimes' => __('Las evidencias deben ser archivos PDF, DOC, DOCX, JPG, JPEG o PNG.'),
            'evidences.*.max' => __('Cada archivo de evidencia no puede exceder 5MB.'),
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array {
        return [
            'title' => __('título'),
            'work_type_id' => __('tipo de trabajo'),
            'organizational_unit_id' => __('unidad organizacional'),
            'description' => __('descripción'),
            'start_date' => __('fecha de inicio'),
            'end_date' => __('fecha de finalización'),
            'academic_period' => __('período académico'),
            'publication_consent' => __('consentimiento de publicación'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void {
        // Convertir publication_consent a booleano
        if ($this->has('publication_consent')) {
            $this->merge([
                'publication_consent' => $this->boolean('publication_consent')
            ]);
        }
    }

    /**
     * Get validated data with additional processing.
     */
    public function getValidatedData(): array {
        $validated = $this->validated();

        // Agregar datos del usuario responsable
        $validated['primary_responsible_user_id'] = $this->user()->getKey();

        // Asignar estado inicial (borrador)
        $validated['current_status_id'] = 1; // Borrador
        $validated['is_draft'] = '1';

        return $validated;
    }
}
