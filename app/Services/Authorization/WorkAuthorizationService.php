<?php

namespace App\Services\Authorization;

use App\Models\User;
use App\Models\WorkOfExtension;

/**
 * Servicio para manejar lógica de autorización relacionada con trabajos de extensión
 * Centraliza las reglas de negocio para determinar permisos de acceso
 */
class WorkAuthorizationService
{
    /**
     * Verificar si un coordinador puede revisar un trabajo específico
     */
    public function canCoordinatorReviewWork(User $coordinator, WorkOfExtension $work): bool
    {
        // Super admin puede revisar cualquier trabajo
        if ($coordinator->hasRole('super_admin')) {
            return true;
        }

        // El trabajo debe ser de la unidad del coordinador
        return $work->getAttribute('organizational_unit_id') === $coordinator->getAttribute('main_organizational_unit_id');
    }

    /**
     * Verificar si un decano/director puede revisar un trabajo específico
     */
    public function canDeanDirectorReviewWork(User $deanDirector, WorkOfExtension $work): bool
    {
        // Super admin puede revisar cualquier trabajo
        if ($deanDirector->hasRole('super_admin')) {
            return true;
        }

        // El decano/director debe ser de la misma unidad organizacional
        return $work->getAttribute('organizational_unit_id') === $deanDirector->getAttribute('main_organizational_unit_id');
    }

    /**
     * Verificar si un trabajo puede ser aprobado por coordinador
     */
    public function canCoordinatorApproveWork(WorkOfExtension $work): bool
    {
        $validStatuses = ['En Revisión Coordinador', 'Enviado a Coordinador'];
        return in_array($work->currentStatus->name, $validStatuses);
    }

    /**
     * Verificar si se pueden solicitar cambios a un trabajo (coordinador)
     */
    public function canCoordinatorRequestChanges(WorkOfExtension $work): bool
    {
        $validStatuses = ['En Revisión Coordinador', 'Enviado a Coordinador'];
        return in_array($work->currentStatus->name, $validStatuses);
    }

    /**
     * Verificar si un trabajo puede ser rechazado por coordinador
     */
    public function canCoordinatorRejectWork(WorkOfExtension $work): bool
    {
        $validStatuses = ['En Revisión Coordinador', 'Enviado a Coordinador'];
        return in_array($work->currentStatus->name, $validStatuses);
    }

    /**
     * Verificar si un trabajo puede ser aprobado por decano/director
     */
    public function canDeanDirectorApproveWork(WorkOfExtension $work): bool
    {
        return $work->currentStatus->name === 'Enviado a Decano/Director';
    }

    /**
     * Verificar si se pueden solicitar cambios a un trabajo (decano/director)
     */
    public function canDeanDirectorRequestChanges(WorkOfExtension $work): bool
    {
        return $work->currentStatus->name === 'Enviado a Decano/Director';
    }

    /**
     * Verificar si un trabajo puede ser aprobado por VIEX
     */
    public function canViexApproveWork(WorkOfExtension $work): bool
    {
        return in_array($work->currentStatus->name, [
            'En VIEX - En Evaluación',
            'En VIEX - Aprobado'
        ]);
    }

    /**
     * Verificar si un trabajo puede ser rechazado por VIEX
     */
    public function canViexRejectWork(WorkOfExtension $work): bool
    {
        return in_array($work->currentStatus->name, [
            'En VIEX - En Evaluación',
            'En VIEX - Aprobado'
        ]);
    }

    /**
     * Verificar si se pueden solicitar cambios desde VIEX
     */
    public function canViexRequestChanges(WorkOfExtension $work): bool
    {
        return in_array($work->currentStatus->name, [
            'En VIEX - En Evaluación',
            'En VIEX - Aprobado'
        ]);
    }

    /**
     * Verificar si se puede asignar evaluador a un trabajo
     */
    public function canAssignEvaluator(WorkOfExtension $work): bool
    {
        return $work->currentStatus->name === 'En VIEX - Recibido';
    }

    /**
     * Verificar si se puede iniciar evaluación de un trabajo
     */
    public function canStartEvaluation(WorkOfExtension $work): bool
    {
        return $work->currentStatus->name === 'En VIEX - Recibido' &&
               $work->workEvaluators()->count() > 0;
    }

    /**
     * Verificar si se puede aprobar y certificar directamente
     */
    public function canApproveAndCertify(WorkOfExtension $work): bool
    {
        return in_array($work->currentStatus->name, [
            'Enviado a VIEX',
            'En VIEX - Recibido'
        ]);
    }
}