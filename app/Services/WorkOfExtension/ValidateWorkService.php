<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;

/**
 * Servicio para manejar validaciones de trabajos de extensión
 * Valida completitud de datos y requisitos para envío
 */
class ValidateWorkService
{
    /**
     * Verificar si el trabajo puede ser enviado
     * Valida todos los campos obligatorios según el tipo de trabajo
     */
    public function canBeSubmitted(WorkOfExtension $work): bool
    {
        // Verificar que esté en borrador
        if (!$work->isInDraft()) {
            return false;
        }

        // Validar campos básicos obligatorios
        if (
            empty($work->title) ||
            empty($work->work_type_id) ||
            empty($work->description) ||
            empty($work->organizational_unit_id) ||
            empty($work->start_date) ||
            empty($work->end_date) ||
            empty($work->academic_period)
        ) {
            return false;
        }

        // Validar detalles específicos según tipo de trabajo
        return $this->validateSpecificDetails($work);
    }

    /**
     * Validar que los detalles específicos del tipo de trabajo estén completos
     */
    protected function validateSpecificDetails(WorkOfExtension $work): bool
    {
        switch ($work->work_type_id) {
            case 1: // Proyecto
                $detail = $work->projectDetail;
                return $detail &&
                    !empty($detail->objectives) &&
                    !empty($detail->methodology);

            case 2: // Actividad
                $detail = $work->activityDetail;
                return $detail &&
                    !empty($detail->activity_type) &&
                    !empty($detail->modality);

            case 3: // Publicación
                $detail = $work->publicationDetail;
                return $detail &&
                    !empty($detail->publication_type);

            case 4: // Asistencia Técnica
                $detail = $work->technicalAssistanceDetail;
                return $detail &&
                    !empty($detail->assistance_type) &&
                    !empty($detail->collaborating_institution);

            default:
                return false;
        }
    }

    /**
     * Obtener lista de campos faltantes para poder enviar el trabajo
     * Útil para mostrar mensajes de error específicos al usuario
     */
    public function getMissingFieldsForSubmission(WorkOfExtension $work): array
    {
        $missing = [];

        // Verificar campos básicos
        if (empty($work->title)) {
            $missing[] = __('Título del trabajo');
        }
        if (empty($work->work_type_id)) {
            $missing[] = __('Tipo de trabajo');
        }
        if (empty($work->description)) {
            $missing[] = __('Descripción');
        }
        if (empty($work->organizational_unit_id)) {
            $missing[] = __('Unidad organizacional');
        }
        if (empty($work->start_date)) {
            $missing[] = __('Fecha de inicio');
        }
        if (empty($work->end_date)) {
            $missing[] = __('Fecha de finalización');
        }
        if (empty($work->academic_period)) {
            $missing[] = __('Período académico');
        }

        // Verificar campos específicos según tipo
        switch ($work->work_type_id) {
            case 1: // Proyecto
                $detail = $work->projectDetail;
                if (!$detail || empty($detail->objectives)) {
                    $missing[] = __('Objetivos del proyecto');
                }
                if (!$detail || empty($detail->methodology)) {
                    $missing[] = __('Metodología del proyecto');
                }
                break;

            case 2: // Actividad
                $detail = $work->activityDetail;
                if (!$detail || empty($detail->activity_type)) {
                    $missing[] = __('Tipo de actividad');
                }
                if (!$detail || empty($detail->modality)) {
                    $missing[] = __('Modalidad de la actividad');
                }
                break;

            case 3: // Publicación
                $detail = $work->publicationDetail;
                if (!$detail || empty($detail->publication_type)) {
                    $missing[] = __('Tipo de publicación');
                }
                break;

            case 4: // Asistencia Técnica
                $detail = $work->technicalAssistanceDetail;
                if (!$detail || empty($detail->assistance_type)) {
                    $missing[] = __('Tipo de asistencia técnica');
                }
                if (!$detail || empty($detail->collaborating_institution)) {
                    $missing[] = __('Institución colaboradora');
                }
                break;
        }

        return $missing;
    }

    /**
     * Validar si el trabajo está en un estado válido para una operación específica
     */
    public function validateStatusForOperation(WorkOfExtension $work, string $operation, array $validStatuses): bool
    {
        $currentStatus = $work->currentStatus?->getAttribute('name') ?? null;

        if (!in_array($currentStatus, $validStatuses)) {
            throw new \InvalidArgumentException(
                "El trabajo no está en el estado correcto para la operación '{$operation}'. Estado actual: {$currentStatus}. Estados válidos: " . implode(', ', $validStatuses)
            );
        }

        return true;
    }

    /**
     * Validar permisos del usuario para una operación específica
     */
    public function validateUserPermission(User $user, string $operation): bool
    {
        // Aquí se pueden agregar validaciones específicas de permisos
        // Por ahora, solo verificamos que el usuario esté autenticado
        if (!$user) {
            throw new \InvalidArgumentException("Usuario no autenticado para la operación '{$operation}'");
        }

        return true;
    }
}