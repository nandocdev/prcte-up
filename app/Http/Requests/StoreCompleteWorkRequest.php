<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompleteWorkRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array {
        $rules = [
            // Campos básicos
            'work_type_id' => 'required|exists:work_type,id',
            'organizational_unit_id' => 'required|exists:organizational_units,id',
            'campus_name' => 'required|string|max:150',
            'faculty_name' => 'required|string|max:150',
            'department_name' => 'required|string|max:150',
            'school_name' => 'nullable|string|max:150',
            'title' => 'required|string|min:10|max:500',
            'description' => 'required|string|min:50|max:2000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'academic_period' => 'nullable|string|max:15',
            'responsible_phone' => 'nullable|string|max:20',
            'responsible_office_phone' => 'required|string|max:25',
            'responsible_personal_phone' => 'required|string|max:25',
            'responsible_email' => 'required|email|max:150',
            'publication_consent' => 'boolean',
            'sdg_goal_id' => 'required|exists:sdg_goals,id',

            // Archivos adjuntos
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB máximo por archivo

            // Participantes
            'participants' => 'nullable|array|max:10',
            'participants.*.name' => 'required_with:participants|string|max:255',
            'participants.*.email' => 'nullable|email|max:150',
            'participants.*.phone' => 'nullable|string|max:30',
            'participants.*.institution' => 'nullable|string|max:255',
            'participants.*.role' => 'nullable|string|max:50',
            'participants.*.is_primary' => 'boolean',
            'participants.*.user_id' => 'nullable|exists:users,id',
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
            'project_category' => 'required|in:general,institucional,unidad_academica,servicio_social',
            'institutional_project_type_id' => 'required_if:project_category,institucional|nullable|exists:inst_project_types,id',
            'project_general_description' => 'required|string|min:30',
            'project_justification' => 'required|string|min:30',
            'objectives' => 'required|string|max:2000',
            'methodology' => 'required|string|max:2000',
            'project_scope' => 'required|string|min:20',
            'project_resource_plan' => 'required|string|min:20',
            'project_schedule' => 'required|string|min:20',
            'project_cost_plan' => 'required|string|min:20',
            'project_beneficiaries' => 'required|string|min:20',
            'project_communication_plan' => 'nullable|string|max:2000',
            'project_institution_relationships' => 'nullable|string|max:2000',
            'project_final_comments' => 'nullable|string|max:2000',
            'ss_intervention_summary' => 'required_if:project_category,servicio_social|nullable|string|min:20',
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
            'activity_introduction' => 'required|string|min:20',
            'activity_justification' => 'required|string|min:20',
            'activity_objectives' => 'required|string|min:20',
            'activity_methodology' => 'required|string|min:20',
            'activity_resources' => 'required|string|min:20',
            'activity_beneficiaries' => 'required|string|min:20',
            'activity_institution_relationships' => 'nullable|string|max:2000',
            'activity_comments' => 'nullable|string|max:2000',
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
            'publication_summary' => 'required|string|min:30|max:4000',
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
            'assistance_description' => 'required|string|min:20',
            'assistance_objectives' => 'required|string|min:20',
            'assistance_methodology' => 'required|string|min:20',
            'assistance_evidence' => 'required|string|min:20',
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

        if (empty($validated['academic_period'])) {
            $validated['academic_period'] = $this->calculateAcademicPeriod($validated);
        }

        // Estructurar datos para el trabajo principal
        $workData = [
            'title' => $validated['title'] ?? null,
            'work_type_id' => $validated['work_type_id'] ?? null,
            'organizational_unit_id' => $validated['organizational_unit_id'] ?? null,
            'campus_name' => $validated['campus_name'] ?? null,
            'faculty_name' => $validated['faculty_name'] ?? null,
            'department_name' => $validated['department_name'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'academic_period' => $validated['academic_period'] ?? null,
            'responsible_phone' => $validated['responsible_phone'] ?? null,
            'responsible_office_phone' => $validated['responsible_office_phone'] ?? null,
            'responsible_personal_phone' => $validated['responsible_personal_phone'] ?? null,
            'responsible_email' => $validated['responsible_email'] ?? null,
            'publication_consent' => $validated['publication_consent'] ?? false,
            'sdg_goal_id' => $validated['sdg_goal_id'] ?? null,
            'primary_responsible_user_id' => $this->user()->getKey(),
            'current_status_id' => 1, // Borrador
            'is_draft' => true,
        ];

        // Estructurar datos específicos según el tipo
        $specificData = [];
        switch ($validated['work_type_id']) {
            case '1': // Proyecto
                $specificData = [
                    'project_category' => $validated['project_category'] ?? 'general',
                    'institutional_project_type_id' => $validated['institutional_project_type_id'] ?? null,
                    'general_description' => $validated['project_general_description'] ?? null,
                    'justification' => $validated['project_justification'] ?? null,
                    'objectives' => $validated['objectives'] ?? null,
                    'methodology' => $validated['methodology'] ?? null,
                    'project_scope' => $validated['project_scope'] ?? null,
                    'resource_plan' => $validated['project_resource_plan'] ?? null,
                    'project_schedule' => $validated['project_schedule'] ?? null,
                    'cost_plan' => $validated['project_cost_plan'] ?? null,
                    'beneficiaries_description' => $validated['project_beneficiaries'] ?? null,
                    'communication_plan' => $validated['project_communication_plan'] ?? null,
                    'institution_relationships' => $validated['project_institution_relationships'] ?? null,
                    'final_comments' => $validated['project_final_comments'] ?? null,
                    'direct_beneficiaries' => $validated['direct_beneficiaries'] ?? null,
                    'indirect_beneficiaries' => $validated['indirect_beneficiaries'] ?? null,
                    'geographic_area' => $validated['geographic_area'] ?? null,
                    'ss_intervention_summary' => $validated['ss_intervention_summary'] ?? null,
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
                    'introduction' => $validated['activity_introduction'] ?? null,
                    'justification' => $validated['activity_justification'] ?? null,
                    'objectives' => $validated['activity_objectives'] ?? null,
                    'methodology' => $validated['activity_methodology'] ?? null,
                    'resources' => $validated['activity_resources'] ?? null,
                    'beneficiaries' => $validated['activity_beneficiaries'] ?? null,
                    'institution_relationships' => $validated['activity_institution_relationships'] ?? null,
                    'comments' => $validated['activity_comments'] ?? null,
                ];
                break;

            case '3': // Publicación
                $specificData = [
                    'publication_type' => $validated['publication_type'] ?? null,
                    'summary' => $validated['publication_summary'] ?? null,
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
                    'description' => $validated['assistance_description'] ?? null,
                    'objectives' => $validated['assistance_objectives'] ?? null,
                    'methodology' => $validated['assistance_methodology'] ?? null,
                    'evidence' => $validated['assistance_evidence'] ?? null,
                ];
                break;
        }

        return [
            'work_data' => $workData,
            'specific_data' => $specificData,
            'work_type' => $validated['work_type_id'],
            'participants' => $this->transformParticipants($validated['participants'] ?? []),
        ];
    }

    protected function calculateAcademicPeriod(array $data): ?string
    {
        if (empty($data['start_date']) || empty($data['end_date'])) {
            return null;
        }

        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        if ($start->gt($end)) {
            return null;
        }

        $semester = $start->month <= 6 ? 'I' : 'II';

        return sprintf('%s-%s', $start->year, $semester);
    }

    protected function transformParticipants(array $participants): array
    {
        return collect($participants)
            ->filter(fn($participant) => !empty($participant['name'] ?? null))
            ->map(fn($participant) => [
                'name' => $participant['name'] ?? null,
                'email' => $participant['email'] ?? null,
                'phone' => $participant['phone'] ?? null,
                'institution' => $participant['institution'] ?? null,
                'role' => $participant['role'] ?? 'Participante',
                'is_primary' => (bool) ($participant['is_primary'] ?? false),
                'user_id' => $participant['user_id'] ?? null,
                'is_internal' => !empty($participant['user_id']),
            ])
            ->values()
            ->toArray();
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
