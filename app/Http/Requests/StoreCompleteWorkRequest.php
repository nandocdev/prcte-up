<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompleteWorkRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->hasAnyRole(['profesor', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $rules = [
            // Campos básicos
            'work_type_id' => 'required|exists:work_type,id',
            'organizational_unit_id' => 'required|exists:organizational_units,id',
            'title' => 'required|string|min:10|max:500',
            'description' => 'required|string|min:50|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_period' => 'required|string|max:15',
            'responsible_phone' => 'nullable|string|max:20',
            'publication_consent' => 'boolean',

            // Archivos adjuntos
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB máximo por archivo
        ];

        // Reglas específicas según el tipo de trabajo
        $workType = $this->input('work_type_id');

        switch ($workType) {
            case '1': // Proyecto
                $rules = array_merge($rules, $this->getProjectRules());
                break;

            case '2': // Actividad
                $rules = array_merge($rules, $this->getActivityRules());
                break;

            case '3': // Publicación
                $rules = array_merge($rules, $this->getPublicationRules());
                break;

            case '4': // Asistencia Técnica
                $rules = array_merge($rules, $this->getAssistanceRules());
                break;
        }

        return $rules;
    }

    /**
     * Get validation rules for projects.
     */
    protected function getProjectRules(): array {
        return [
            'objectives' => 'required|string|max:2000',
            'methodology' => 'required|string|max:2000',
            'direct_beneficiaries' => 'nullable|integer|min:0',
            'indirect_beneficiaries' => 'nullable|integer|min:0',
            'geographic_area' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get validation rules for activities.
     */
    protected function getActivityRules(): array {
        return [
            'activity_type' => 'required|in:curso,taller,seminario,conferencia,diplomado,capacitacion,otro',
            'modality' => 'required|in:presencial,virtual,hibrida',
            'duration_hours' => 'nullable|integer|min:1',
            'expected_participants' => 'nullable|integer|min:1',
            'participant_profile' => 'nullable|string|max:255',
            'offers_certificate' => 'boolean',
        ];
    }

    /**
     * Get validation rules for publications.
     */
    protected function getPublicationRules(): array {
        return [
            'publication_type' => 'required|in:articulo,libro,manual,guia,cartilla,folleto,material_audiovisual,otro',
            'editorial' => 'nullable|string|max:255',
            'isbn_issn' => 'nullable|string|max:50',
            'target_audience' => 'nullable|string|max:1000',
            'language' => 'nullable|in:español,ingles,portugues,frances,otro',
            'print_run' => 'nullable|integer|min:1',
            'relevance_justification' => 'required|string|min:20|max:4000',
            'publication_date' => 'required|date',
            'media_type' => 'required|string|max:100',
            'media_nature' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get validation rules for technical assistance.
     */
    protected function getAssistanceRules(): array {
        return [
            'assistance_type' => 'required|in:consultoria,asesoria,diagnostico,evaluacion,auditoria,capacitacion_tecnica,otro',
            'collaborating_institution' => 'required|string|max:255',
            'specialization_area' => 'nullable|string|max:255',
            'expected_products' => 'nullable|string|max:2000',
            'work_modality' => 'nullable|in:presencial,remota,mixta',
            'estimated_hours' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array {
        return [
            'title.min' => 'El título debe tener al menos 10 caracteres.',
            'description.min' => 'La descripción debe tener al menos 50 caracteres.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            'academic_period.required' => 'El período académico es obligatorio.',
            'objectives.required' => 'Los objetivos del proyecto son obligatorios.',
            'methodology.required' => 'La metodología del proyecto es obligatoria.',
            'activity_type.required' => 'El tipo de actividad es obligatorio.',
            'modality.required' => 'La modalidad de la actividad es obligatoria.',
            'publication_type.required' => 'El tipo de publicación es obligatorio.',
            'relevance_justification.required' => 'Debe justificar la relevancia de la publicación.',
            'relevance_justification.min' => 'La justificación de relevancia debe tener al menos 20 caracteres.',
            'assistance_type.required' => 'El tipo de asistencia técnica es obligatorio.',
            'collaborating_institution.required' => 'La institución beneficiaria es obligatoria.',
            'publication_date.required' => 'Debe indicar la fecha de publicación.',
            'media_type.required' => 'Debe seleccionar el tipo de medio.',
        ];
    }

    /**
     * Get the validated data formatted for storage.
     */
    public function getValidatedData(): array {
        $validated = $this->validated();

        // Estructurar datos para el trabajo principal
        $workData = [
            'title' => $validated['title'] ?? null,
            'work_type_id' => $validated['work_type_id'] ?? null,
            'organizational_unit_id' => $validated['organizational_unit_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'academic_period' => $validated['academic_period'] ?? null,
            'responsible_phone' => $validated['responsible_phone'] ?? null,
            'publication_consent' => $validated['publication_consent'] ?? false,
            'primary_responsible_user_id' => $this->user()->getKey(),
            'current_status_id' => 1, // Borrador
            'is_draft' => true,
        ];

        // Estructurar datos específicos según el tipo
        $specificData = [];
        switch ($validated['work_type_id']) {
            case '1': // Proyecto
                $specificData = [
                    'objectives' => $validated['objectives'] ?? null,
                    'methodology' => $validated['methodology'] ?? null,
                    'direct_beneficiaries' => $validated['direct_beneficiaries'] ?? null,
                    'indirect_beneficiaries' => $validated['indirect_beneficiaries'] ?? null,
                    'geographic_area' => $validated['geographic_area'] ?? null,
                ];
                break;

            case '2': // Actividad
                $specificData = [
                    'activity_type' => $validated['activity_type'] ?? null,
                    'modality' => $validated['modality'] ?? null,
                    'duration_hours' => $validated['duration_hours'] ?? null,
                    'expected_participants' => $validated['expected_participants'] ?? null,
                    'participant_profile' => $validated['participant_profile'] ?? null,
                    'offers_certificate' => $validated['offers_certificate'] ?? false,
                ];
                break;

            case '3': // Publicación
                $specificData = [
                    'publication_type' => $validated['publication_type'] ?? null,
                    'editorial' => $validated['editorial'] ?? null,
                    'isbn_issn' => $validated['isbn_issn'] ?? null,
                    'target_audience' => $validated['target_audience'] ?? null,
                    'language' => $validated['language'] ?? 'español',
                    'print_run' => $validated['print_run'] ?? null,
                    'relevance_justification' => $validated['relevance_justification'] ?? null,
                    'publication_date' => $validated['publication_date'] ?? null,
                    'media_type' => $validated['media_type'] ?? null,
                    'media_nature' => $validated['media_nature'] ?? null,
                ];
                break;

            case '4': // Asistencia Técnica
                $specificData = [
                    'assistance_type' => $validated['assistance_type'] ?? null,
                    'collaborating_institution' => $validated['collaborating_institution'] ?? null,
                    'specialization_area' => $validated['specialization_area'] ?? null,
                    'expected_products' => $validated['expected_products'] ?? null,
                    'work_modality' => $validated['work_modality'] ?? 'presencial',
                    'estimated_hours' => $validated['estimated_hours'] ?? null,
                ];
                break;
        }

        return [
            'work_data' => $workData,
            'specific_data' => $specificData,
            'work_type' => $validated['work_type_id'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) {
        \Illuminate\Support\Facades\Log::error('Validation failed in StoreCompleteWorkRequest', [
            'errors' => $validator->errors()->toArray(),
            'input_keys' => array_keys($this->all())
        ]);

        parent::failedValidation($validator);
    }
}
