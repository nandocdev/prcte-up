<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use App\Models\User;
use App\Events\WorkSubmitted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para enviar trabajos de extensión para revisión
 * Maneja validaciones y transiciones de estado
 */
class SubmitWorkService
{
    /**
     * Enviar trabajo para revisión
     *
     * @param WorkOfExtension $work Trabajo a enviar
     * @param User $user Usuario que envía
     * @param bool $isResubmission True si es reenvío después de rechazo
     * @return WorkOfExtension
     * @throws \InvalidArgumentException
     */
    public function execute(WorkOfExtension $work, User $user, bool $isResubmission = false): WorkOfExtension
    {
        // Validar que el trabajo puede ser enviado
        $this->validateWorkCanBeSubmitted($work);

        // Obtener estado de envío
        $submittedStatus = $this->getSubmittedStatus();

        // Ejecutar la transición de estado
        DB::transaction(function () use ($work, $submittedStatus, $user, $isResubmission) {
            // Mensaje diferenciado para historial
            $comment = $isResubmission
                ? 'Trabajo corregido y reenviado para revisión por el coordinador de extensión.'
                : 'Trabajo enviado para revisión por el coordinador de extensión.';

            // Cambiar estado
            $this->changeWorkStatus($work, $submittedStatus, $user, $comment);

            // Marcar como enviado y timestamp
            $work->update([
                'is_draft' => '0',
                'submitted_at' => now(),
            ]);
        });

        // Disparar evento
        WorkSubmitted::dispatch($work, $user, $isResubmission);

        Log::info($isResubmission ? 'Trabajo reenviado' : 'Trabajo enviado para revisión', [
            'work_id' => $work->getKey(),
            'submitted_by' => $user->getKey(),
            'work_title' => $work->getAttribute('title'),
            'is_resubmission' => $isResubmission
        ]);

        return $work->fresh();
    }

    /**
     * Validar que el trabajo puede ser enviado
     *
     * @param WorkOfExtension $work
     * @throws \InvalidArgumentException
     */
    private function validateWorkCanBeSubmitted(WorkOfExtension $work): void
    {
        // Verificar que esté en borrador
        if (!$work->isInDraft()) {
            throw new \InvalidArgumentException(__('El trabajo ya ha sido enviado anteriormente.'));
        }

        // Validar campos básicos obligatorios
        if (!$this->validateBasicFields($work)) {
            $missingFields = $this->getMissingBasicFields($work);
            $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos obligatorios: ') .
                implode(', ', $missingFields);
            throw new \InvalidArgumentException($message);
        }

        // Validar detalles específicos según tipo
        if (!$this->validateSpecificDetails($work)) {
            $missingFields = $this->getMissingSpecificFields($work);
            $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos específicos: ') .
                implode(', ', $missingFields);
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Validar campos básicos obligatorios
     *
     * @param WorkOfExtension $work
     * @return bool
     */
    private function validateBasicFields(WorkOfExtension $work): bool
    {
        return !empty($work->title) &&
               !empty($work->work_type_id) &&
               !empty($work->description) &&
               !empty($work->organizational_unit_id) &&
               !empty($work->start_date) &&
               !empty($work->end_date) &&
               !empty($work->academic_period);
    }

    /**
     * Obtener lista de campos básicos faltantes
     *
     * @param WorkOfExtension $work
     * @return array
     */
    private function getMissingBasicFields(WorkOfExtension $work): array
    {
        $missing = [];

        if (empty($work->title)) $missing[] = __('Título del trabajo');
        if (empty($work->work_type_id)) $missing[] = __('Tipo de trabajo');
        if (empty($work->description)) $missing[] = __('Descripción');
        if (empty($work->organizational_unit_id)) $missing[] = __('Unidad organizacional');
        if (empty($work->start_date)) $missing[] = __('Fecha de inicio');
        if (empty($work->end_date)) $missing[] = __('Fecha de finalización');
        if (empty($work->academic_period)) $missing[] = __('Período académico');

        return $missing;
    }

    /**
     * Validar detalles específicos según tipo de trabajo
     *
     * @param WorkOfExtension $work
     * @return bool
     */
    private function validateSpecificDetails(WorkOfExtension $work): bool
    {
        switch ($work->work_type_id) {
            case 1: // Proyecto
                $detail = $work->projectDetail;
                return $detail && !empty($detail->objectives) && !empty($detail->methodology);

            case 2: // Actividad
                $detail = $work->activityDetail;
                return $detail && !empty($detail->activity_type) && !empty($detail->modality);

            case 3: // Publicación
                $detail = $work->publicationDetail;
                return $detail && !empty($detail->publication_type);

            case 4: // Asistencia Técnica
                $detail = $work->technicalAssistanceDetail;
                return $detail && !empty($detail->assistance_type) && !empty($detail->collaborating_institution);

            default:
                return false;
        }
    }

    /**
     * Obtener lista de campos específicos faltantes
     *
     * @param WorkOfExtension $work
     * @return array
     */
    private function getMissingSpecificFields(WorkOfExtension $work): array
    {
        $missing = [];

        switch ($work->work_type_id) {
            case 1: // Proyecto
                $detail = $work->projectDetail;
                if (!$detail || empty($detail->objectives)) $missing[] = __('Objetivos del proyecto');
                if (!$detail || empty($detail->methodology)) $missing[] = __('Metodología del proyecto');
                break;

            case 2: // Actividad
                $detail = $work->activityDetail;
                if (!$detail || empty($detail->activity_type)) $missing[] = __('Tipo de actividad');
                if (!$detail || empty($detail->modality)) $missing[] = __('Modalidad de la actividad');
                break;

            case 3: // Publicación
                $detail = $work->publicationDetail;
                if (!$detail || empty($detail->publication_type)) $missing[] = __('Tipo de publicación');
                break;

            case 4: // Asistencia Técnica
                $detail = $work->technicalAssistanceDetail;
                if (!$detail || empty($detail->assistance_type)) $missing[] = __('Tipo de asistencia técnica');
                if (!$detail || empty($detail->collaborating_institution)) $missing[] = __('Institución colaboradora');
                break;
        }

        return $missing;
    }

    /**
     * Obtener estado de "Enviado a Coordinador"
     *
     * @return WorkStatus
     * @throws \InvalidArgumentException
     */
    private function getSubmittedStatus(): WorkStatus
    {
        $submittedStatus = WorkStatus::where('name', 'Enviado a Coordinador')->first();

        if (!$submittedStatus) {
            // Intentar con nombres alternativos
            $submittedStatus = WorkStatus::where('name', 'En Coordinador de Extensión')
                ->orWhere('name', 'En Coordinador Extensión')
                ->first();

            if (!$submittedStatus) {
                throw new \InvalidArgumentException(__('No se encontró el estado de envío a coordinador. Contacte al administrador.'));
            }
        }

        return $submittedStatus;
    }

    /**
     * Cambiar estado del trabajo
     *
     * @param WorkOfExtension $work
     * @param WorkStatus $newStatus
     * @param User $user
     * @param string $comments
     */
    private function changeWorkStatus(WorkOfExtension $work, WorkStatus $newStatus, User $user, string $comments): void
    {
        $oldStatusId = $work->getAttribute('current_status_id');

        // Actualizar estado
        $work->update(['current_status_id' => $newStatus->getKey()]);

        // Registrar en historial
        $work->statusHistory()->create([
            'from_status_id' => $oldStatusId,
            'to_status_id' => $newStatus->getKey(),
            'changed_by_user_id' => $user->getKey(),
            'comments' => $comments,
        ]);

        Log::info('Cambio de estado registrado', [
            'work_id' => $work->getKey(),
            'from_status_id' => $oldStatusId,
            'to_status_id' => $newStatus->getKey(),
            'by' => $user->getKey(),
        ]);
    }
}