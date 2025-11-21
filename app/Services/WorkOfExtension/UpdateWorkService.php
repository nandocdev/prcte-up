<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para actualizar trabajos de extensión
 * Maneja la lógica completa de actualización incluyendo detalles específicos
 */
class UpdateWorkService
{
    /**
     * Actualizar trabajo completo desde datos validados
     *
     * @param WorkOfExtension $work Trabajo a actualizar
     * @param array $data Datos validados del formulario
     * @param User $user Usuario que actualiza
     * @return WorkOfExtension
     * @throws \Exception
     */
    public function execute(WorkOfExtension $work, array $data, User $user): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            // Extraer datos estructurados del request
            $workData = $data['work_data'];
            $specificData = $data['specific_data'];
            $workType = (string) ($data['work_type'] ?? $work->getAttribute('work_type_id'));
            $previousType = (string) $work->getAttribute('work_type_id');
            $newWorkTypeId = (string) ($workData['work_type_id'] ?? $previousType);

            // Actualizar trabajo principal
            $work->update([
                'title' => $workData['title'],
                'work_type_id' => $workData['work_type_id'],
                'organizational_unit_id' => $workData['organizational_unit_id'],
                'campus_name' => $workData['campus_name'] ?? $work->campus_name,
                'faculty_name' => $workData['faculty_name'] ?? $work->faculty_name,
                'department_name' => $workData['department_name'] ?? $work->department_name,
                'school_name' => $workData['school_name'] ?? $work->school_name,
                'start_date' => $workData['start_date'],
                'end_date' => $workData['end_date'],
                'publication_consent' => isset($workData['publication_consent']) ? (bool) $workData['publication_consent'] : false,
                'description' => $workData['description'],
                'academic_period' => $workData['academic_period'] ?? config('work_types.current_academic_period'),
                'responsible_phone' => $workData['responsible_phone'] ?? null,
                'responsible_office_phone' => $workData['responsible_office_phone'] ?? null,
                'responsible_personal_phone' => $workData['responsible_personal_phone'] ?? null,
                'responsible_email' => $workData['responsible_email'] ?? $work->responsible_email,
                'sdg_goal_id' => $workData['sdg_goal_id'] ?? $work->sdg_goal_id,
            ]);

            // Limpiar detalles que no correspondan al nuevo tipo
            if ($previousType !== $newWorkTypeId) {
                $this->removeDetailRecordsExcept($work, $newWorkTypeId);
            }

            // Actualizar o crear detalles específicos según tipo
            $this->updateSpecificDetails($work, $workType, $specificData);

            $this->syncParticipants($work, $data['participants'] ?? []);

            // Registrar actualización en historial de estados
            $this->createUpdateHistory($work, $user);

            DB::commit();

            Log::info('Trabajo actualizado exitosamente', [
                'work_id' => $work->getKey(),
                'user_id' => $user->getKey(),
                'work_type' => $workType
            ]);

            return $work->fresh([
                'projectDetail',
                'activityDetail',
                'publicationDetail',
                'technicalAssistanceDetail',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error actualizando trabajo', [
                'work_id' => $work->getKey(),
                'user_id' => $user->getKey(),
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar detalles específicos según el tipo de trabajo
     *
     * @param WorkOfExtension $work
     * @param string $workType
     * @param array $specificData
     */
    private function updateSpecificDetails(WorkOfExtension $work, string $workType, array $specificData): void
    {
        switch ($workType) {
            case '1': // Proyecto
                $work->projectDetail()->updateOrCreate(
                    ['work_of_extension_id' => $work->getKey()],
                    [
                        'project_category' => $specificData['project_category'] ?? 'general',
                        'institutional_project_type_id' => $specificData['institutional_project_type_id'] ?? null,
                        'objectives' => $specificData['objectives'] ?? null,
                        'methodology' => $specificData['methodology'] ?? null,
                        'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                        'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                        'geographic_area' => $specificData['geographic_area'] ?? null,
                        'details_json' => json_encode([
                            'general_description' => $specificData['general_description'] ?? null,
                            'justification' => $specificData['justification'] ?? null,
                            'project_scope' => $specificData['project_scope'] ?? null,
                            'resource_plan' => $specificData['resource_plan'] ?? null,
                            'community_plan' => $specificData['communication_plan'] ?? null,
                            'beneficiaries_description' => $specificData['beneficiaries_description'] ?? null,
                            'institution_relationships' => $specificData['institution_relationships'] ?? null,
                            'final_comments' => $specificData['final_comments'] ?? null,
                            'ss_intervention_summary' => $specificData['ss_intervention_summary'] ?? null,
                        ]),
                        'schedule_json' => json_encode(['schedule' => $specificData['project_schedule'] ?? null]),
                        'resources_json' => json_encode(['resources' => $specificData['resource_plan'] ?? null]),
                        'costs_json' => json_encode(['cost_plan' => $specificData['cost_plan'] ?? null]),
                        'ss_intervention_summary' => $specificData['ss_intervention_summary'] ?? null,
                    ]
                );
                break;

            case '2': // Actividad
                $work->activityDetail()->updateOrCreate(
                    ['work_of_extension_id' => $work->getKey()],
                    [
                        'activity_type' => $specificData['activity_type'] ?? null,
                        'modality' => $specificData['modality'] ?? null,
                        'duration_hours' => $specificData['duration_hours'] ?? null,
                        'expected_participants' => $specificData['expected_participants'] ?? null,
                        'participant_profile' => $specificData['participant_profile'] ?? null,
                        'offers_certificate' => isset($specificData['offers_certificate']) ? (bool) $specificData['offers_certificate'] : false,
                        'details_json' => json_encode([
                            'activity_type' => $specificData['activity_type'] ?? null,
                            'modality' => $specificData['modality'] ?? null,
                            'duration_hours' => $specificData['duration_hours'] ?? null,
                            'expected_participants' => $specificData['expected_participants'] ?? null,
                            'participant_profile' => $specificData['participant_profile'] ?? null,
                            'offers_certificate' => isset($specificData['offers_certificate']) ? (bool) $specificData['offers_certificate'] : false,
                            'introduction' => $specificData['introduction'] ?? null,
                            'justification' => $specificData['justification'] ?? null,
                            'objectives' => $specificData['objectives'] ?? null,
                            'methodology' => $specificData['methodology'] ?? null,
                            'resources' => $specificData['resources'] ?? null,
                            'beneficiaries' => $specificData['beneficiaries'] ?? null,
                            'institution_relationships' => $specificData['institution_relationships'] ?? null,
                            'comments' => $specificData['comments'] ?? null,
                        ]),
                    ]
                );
                break;

            case '3': // Publicación
                $work->publicationDetail()->updateOrCreate(
                    ['work_of_extension_id' => $work->getKey()],
                    [
                        'publication_type' => $specificData['publication_type'] ?? null,
                        'summary' => $specificData['summary'] ?? null,
                        'editorial' => $specificData['editorial'] ?? null,
                        'isbn_issn' => $specificData['isbn_issn'] ?? null,
                        'target_audience' => $specificData['target_audience'] ?? null,
                        'language' => $specificData['language'] ?? 'español',
                        'print_run' => $specificData['print_run'] ?? null,
                        'relevance_justification' => $specificData['relevance_justification'] ?? null,
                        'publication_date' => $specificData['publication_date'] ?? null,
                        'media_type' => $specificData['media_type'] ?? null,
                        'media_nature' => $specificData['media_nature'] ?? null,
                    ]
                );
                break;

            case '4': // Asistencia Técnica
                $work->technicalAssistanceDetail()->updateOrCreate(
                    ['work_of_extension_id' => $work->getKey()],
                    [
                        'assistance_type' => $specificData['assistance_type'] ?? null,
                        'collaborating_institution' => $specificData['collaborating_institution'] ?? null,
                        'specialization_area' => $specificData['specialization_area'] ?? null,
                        'expected_products' => $specificData['expected_products'] ?? null,
                        'work_modality' => $specificData['work_modality'] ?? 'presencial',
                        'estimated_hours' => $specificData['estimated_hours'] ?? null,
                        'details_json' => json_encode([
                            'assistance_type' => $specificData['assistance_type'] ?? null,
                            'collaborating_institution' => $specificData['collaborating_institution'] ?? null,
                            'specialization_area' => $specificData['specialization_area'] ?? null,
                            'expected_products' => $specificData['expected_products'] ?? null,
                            'work_modality' => $specificData['work_modality'] ?? 'presencial',
                            'estimated_hours' => $specificData['estimated_hours'] ?? null,
                            'description' => $specificData['description'] ?? null,
                            'objectives' => $specificData['objectives'] ?? null,
                            'methodology' => $specificData['methodology'] ?? null,
                            'evidence' => $specificData['evidence'] ?? null,
                        ]),
                    ]
                );
                break;
        }
    }

    private function syncParticipants(WorkOfExtension $work, array $participants): void
    {
        $work->participants()->delete();

        if (empty($participants)) {
            return;
        }

        foreach ($participants as $participant) {
            $work->participants()->create([
                'user_id' => $participant['user_id'] ?? null,
                'name' => $participant['name'] ?? null,
                'email' => $participant['email'] ?? null,
                'phone' => $participant['phone'] ?? null,
                'institution' => $participant['institution'] ?? null,
                'role' => $participant['role'] ?? 'Participante',
                'is_primary' => $participant['is_primary'] ?? false,
                'is_internal' => $participant['is_internal'] ?? false,
                'external_participant_name' => $participant['name'] ?? null,
            ]);
        }
    }

    /**
     * Eliminar detalles específicos que ya no corresponden al tipo de trabajo actual
     *
     * @param WorkOfExtension $work
     * @param string $currentType
     */
    private function removeDetailRecordsExcept(WorkOfExtension $work, string $currentType): void
    {
        if ($currentType !== '1') {
            $work->projectDetail()->delete();
        }

        if ($currentType !== '2') {
            $work->activityDetail()->delete();
        }

        if ($currentType !== '3') {
            $work->publicationDetail()->delete();
        }

        if ($currentType !== '4') {
            $work->technicalAssistanceDetail()->delete();
        }
    }

    /**
     * Crear entrada en historial para la actualización
     *
     * @param WorkOfExtension $work
     * @param User $user
     */
    private function createUpdateHistory(WorkOfExtension $work, User $user): void
    {
        $work->statusHistory()->create([
            'from_status_id' => $work->getAttribute('current_status_id'),
            'to_status_id' => $work->getAttribute('current_status_id'), // No cambia estado
            'changed_by_user_id' => $user->getKey(),
            'comments' => 'Trabajo actualizado por el usuario (edición completa)',
        ]);
    }
}