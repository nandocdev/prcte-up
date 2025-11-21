<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Events\WorkSubmitted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para crear trabajos de extensión
 * Maneja la lógica completa de creación incluyendo detalles específicos
 */
class CreateWorkService
{
    /**
     * Crear trabajo completo desde datos validados
     *
     * @param array $data Datos validados del formulario
     * @param User $user Usuario que crea el trabajo
     * @return WorkOfExtension
     * @throws \Exception
     */
    public function execute(array $data, User $user): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            // Extraer datos estructurados del request
            $workData = $data['work_data'];
            $specificData = $data['specific_data'];
            $workType = $data['work_type'];

            // Crear trabajo principal
            $work = WorkOfExtension::create([
                'title' => $workData['title'],
                'work_type_id' => $workData['work_type_id'],
                'primary_responsible_user_id' => $user->id,
                'organizational_unit_id' => $workData['organizational_unit_id'],
                'current_status_id' => 1, // Borrador
                'start_date' => $workData['start_date'],
                'end_date' => $workData['end_date'],
                'publication_consent' => $workData['publication_consent'] ?? false,
                'is_draft' => true,
                'description' => $workData['description'],
                'academic_period' => $workData['academic_period'] ?? config('work_types.current_academic_period'),
                'responsible_phone' => $workData['responsible_phone'] ?? null,
            ]);

            // Crear detalles específicos según tipo
            $this->createSpecificDetails($work, $workType, $specificData);

            // Crear entrada inicial en historial de estados
            $this->createInitialStatusHistory($work, $user);

            DB::commit();

            Log::info('Trabajo creado exitosamente', [
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
            Log::error('Error creando trabajo', [
                'user_id' => $user->getKey(),
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Crear detalles específicos según el tipo de trabajo
     *
     * @param WorkOfExtension $work
     * @param string $workType
     * @param array $specificData
     */
    private function createSpecificDetails(WorkOfExtension $work, string $workType, array $specificData): void
    {
        switch ($workType) {
            case '1': // Proyecto
                $work->projectDetail()->create([
                    'project_category' => 'general',
                    'objectives' => $specificData['objectives'] ?? null,
                    'methodology' => $specificData['methodology'] ?? null,
                    'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                    'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                    'geographic_area' => $specificData['geographic_area'] ?? null,
                    'details_json' => json_encode([
                        'objectives' => $specificData['objectives'] ?? null,
                        'methodology' => $specificData['methodology'] ?? null,
                        'direct_beneficiaries' => $specificData['direct_beneficiaries'] ?? null,
                        'indirect_beneficiaries' => $specificData['indirect_beneficiaries'] ?? null,
                        'geographic_area' => $specificData['geographic_area'] ?? null,
                    ]),
                ]);
                break;

            case '2': // Actividad
                $work->activityDetail()->create([
                    'activity_type' => $specificData['activity_type'] ?? null,
                    'modality' => $specificData['modality'] ?? null,
                    'duration_hours' => $specificData['duration_hours'] ?? null,
                    'expected_participants' => $specificData['expected_participants'] ?? null,
                    'participant_profile' => $specificData['participant_profile'] ?? null,
                    'offers_certificate' => $specificData['offers_certificate'] ?? false,
                    'details_json' => json_encode([
                        'activity_type' => $specificData['activity_type'] ?? null,
                        'modality' => $specificData['modality'] ?? null,
                        'duration_hours' => $specificData['duration_hours'] ?? null,
                        'expected_participants' => $specificData['expected_participants'] ?? null,
                        'participant_profile' => $specificData['participant_profile'] ?? null,
                        'offers_certificate' => $specificData['offers_certificate'] ?? false,
                    ]),
                ]);
                break;

            case '3': // Publicación
                $work->publicationDetail()->create([
                    'publication_type' => $specificData['publication_type'] ?? null,
                    'editorial' => $specificData['editorial'] ?? null,
                    'isbn_issn' => $specificData['isbn_issn'] ?? null,
                    'target_audience' => $specificData['target_audience'] ?? null,
                    'language' => $specificData['language'] ?? 'español',
                    'print_run' => $specificData['print_run'] ?? null,
                    'relevance_justification' => $specificData['relevance_justification'] ?? '',
                    'publication_date' => $specificData['publication_date'] ?? now(),
                    'media_type' => $specificData['media_type'] ?? '',
                    'media_nature' => $specificData['media_nature'] ?? null,
                ]);
                break;

            case '4': // Asistencia Técnica
                $work->technicalAssistanceDetail()->create([
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
                    ]),
                ]);
                break;
        }
    }

    /**
     * Crear entrada inicial en historial de estados
     *
     * @param WorkOfExtension $work
     * @param User $user
     */
    private function createInitialStatusHistory(WorkOfExtension $work, User $user): void
    {
        $draftStatus = \App\Models\WorkStatus::where('name', 'Borrador')->first();
        if (!$draftStatus) {
            $draftStatus = \App\Models\WorkStatus::first(); // Fallback
        }

        $work->statusHistory()->create([
            'from_status_id' => null,
            'to_status_id' => $draftStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => 'Trabajo creado en estado borrador',
        ]);
    }
}