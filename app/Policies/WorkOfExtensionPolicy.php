<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Auth\Access\Response;

/**
 * Policy para autorización de WorkOfExtension
 * Implementa las reglas de acceso según roles VIEX
 *
 * Versión simplificada - se refinará después con validaciones específicas
 */
class WorkOfExtensionPolicy {
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool {
        // Todos los usuarios autenticados pueden ver la lista
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Implementa reglas de visibilidad por rol y contexto
     */
    public function view(User $user, WorkOfExtension $workOfExtension): bool {
        // Super admin puede ver cualquier trabajo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Profesor solo puede ver sus propios trabajos
        if ($user->hasRole('profesor')) {
            return (int) $workOfExtension->getAttribute('primary_responsible_user_id') === (int) $user->getKey();
        }

        // Coordinador puede ver trabajos de su unidad en estados relevantes
        if ($user->hasRole('coordinador_extension')) {
            $unitId = (int) $user->getAttribute('main_organizational_unit_id');
            $unitIds = \App\Models\OrganizationalUnit::descendantIds($unitId);

            $coordinatorStatuses = \App\Models\WorkOfExtension::COORDINATOR_STATUS_NAMES;

            $workUnitId = (int) $workOfExtension->getAttribute('organizational_unit_id');
            $workStatusName = $workOfExtension->currentStatus?->getAttribute('name');

            return in_array($workUnitId, $unitIds) &&
                in_array($workStatusName, $coordinatorStatuses);
        }

        // Decano puede ver trabajos de su facultad en estados relevantes
        if ($user->hasRole('decano_director')) {
            $unitId = (int) $user->getAttribute('main_organizational_unit_id');
            $unitIds = \App\Models\OrganizationalUnit::descendantIds($unitId);

            $deanStatuses = \App\Models\WorkOfExtension::DEAN_STATUS_NAMES;

            $workUnitId = (int) $workOfExtension->getAttribute('organizational_unit_id');
            $workStatusName = $workOfExtension->currentStatus?->getAttribute('name');

            return in_array($workUnitId, $unitIds) &&
                in_array($workStatusName, $deanStatuses);
        }

        // VIEX puede ver trabajos en estados VIEX
        if ($user->hasRole('viex_admin')) {
            $viexStatuses = \App\Models\WorkOfExtension::VIEX_STATUS_NAMES;

            $workStatusName = $workOfExtension->currentStatus?->getAttribute('name');

            return in_array($workStatusName, $viexStatuses);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool {
        // Solo profesores pueden crear trabajos
        return $user->hasRole('profesor') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkOfExtension $workOfExtension): bool {
        // Super admin puede editar cualquier trabajo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Solo profesores pueden editar
        if (!$user->hasRole('profesor')) {
            return false;
        }

        // Verificar propiedad del trabajo
        if ((int) $workOfExtension->getAttribute('primary_responsible_user_id') !== (int) $user->getKey()) {
            return false;
        }

        // Verificar que esté en borrador
        if (!$workOfExtension->isInDraft()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkOfExtension $workOfExtension): bool {
        // Super admin puede eliminar cualquier trabajo
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Solo profesores pueden eliminar trabajos
        if (!$user->hasRole('profesor')) {
            return false;
        }

        // Solo el propietario puede eliminar su trabajo
        if ((int) $workOfExtension->getAttribute('primary_responsible_user_id') !== (int) $user->getKey()) {
            return false;
        }

        // Solo se puede eliminar si está en borrador
        if (!$workOfExtension->isInDraft()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkOfExtension $workOfExtension): bool {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkOfExtension $workOfExtension): bool {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can submit work to coordinator.
     */
    public function submit(User $user, WorkOfExtension $workOfExtension): bool {
        // Solo profesores pueden enviar trabajos
        return $user->hasRole('profesor') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can review as coordinator.
     */
    public function reviewAsCoordinator(User $user, WorkOfExtension $workOfExtension): bool {
        return $user->hasRole('coordinador_extension') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can review as dean/director.
     */
    public function reviewAsDean(User $user, WorkOfExtension $workOfExtension): bool {
        return $user->hasRole('decano_director') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can review as VIEX.
     */
    public function reviewAsViex(User $user, WorkOfExtension $workOfExtension): bool {
        return $user->hasRole('viex_admin') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can coordinate extension works.
     */
    public function coordinateExtensionWorks(User $user): bool {
        return $user->hasRole('coordinador_extension') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can approve as coordinator.
     */
    public function approveAsCoordinator(User $user, WorkOfExtension $workOfExtension): bool {
        // Solo coordinadores de extensión o super admin
        if (!$user->hasAnyRole(['coordinador_extension', 'super_admin'])) {
            return false;
        }

        // TODO: Validar que el coordinador sea de la misma unidad organizacional
        // Por ahora permitimos a cualquier coordinador
        return true;
    }

    /**
     * Determine whether the user can request changes as coordinator.
     */
    public function requestChangesAsCoordinator(User $user, WorkOfExtension $workOfExtension): bool {
        // Solo coordinadores de extensión o super admin
        if (!$user->hasAnyRole(['coordinador_extension', 'super_admin'])) {
            return false;
        }

        // TODO: Validar que el coordinador sea de la misma unidad organizacional
        // Por ahora permitimos a cualquier coordinador
        return true;
    }

    /**
     * Determine whether the user can approve as dean/director.
     */
    public function approveAsDean(User $user, WorkOfExtension $workOfExtension): bool {
        // Solo decanos/directores o super admin
        if (!$user->hasAnyRole(['decano_director', 'super_admin'])) {
            return false;
        }

        // TODO: Validar que el decano/director sea de la misma unidad organizacional
        // Por ahora permitimos a cualquier decano/director
        return true;
    }

    /**
     * Determine whether the user can request changes as dean/director.
     */
    public function requestChangesAsDean(User $user, WorkOfExtension $workOfExtension): bool {
        // Solo decanos/directores o super admin
        if (!$user->hasAnyRole(['decano_director', 'super_admin'])) {
            return false;
        }

        // TODO: Validar que el decano/director sea de la misma unidad organizacional
        // Por ahora permitimos a cualquier decano/director
        return true;
    }

    /**
     * Determine whether the user can manage dean/director workflow.
     */
    public function manageDeanDirectorWorkflow(User $user): bool {
        return $user->hasRole('decano_director') || $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can view work as VIEX admin.
     * 
     * CU9 - Fase 7: Autorización para visualización VIEX
     */
    public function viewAsViex(User $user, WorkOfExtension $workOfExtension): bool
    {
        // Super admin siempre puede ver
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Solo administradores VIEX
        if (!$user->hasRole('viex_admin')) {
            return false;
        }

        // Verificar que el trabajo esté en estados VIEX
        $viexStatuses = [
            'Enviado a VIEX',
            'En VIEX - Pendiente Asignación',
            'En VIEX - En Evaluación',
            'En VIEX - Aprobado',
            'Certificado',
        ];

        $currentStatus = $workOfExtension->currentStatus?->name;

        return in_array($currentStatus, $viexStatuses, true);
    }

    /**
     * Determine whether the user can assign evaluators to a work.
     * 
     * CU9 - Fase 7: Autorización para asignación de evaluadores
     */
    public function assignEvaluator(User $user, WorkOfExtension $workOfExtension): bool
    {
        // Super admin siempre puede asignar
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Solo administradores VIEX pueden asignar evaluadores
        if (!$user->hasRole('viex_admin')) {
            return false;
        }

        // Verificar que el trabajo esté en estado que permite asignación
        $allowedStatuses = [
            'En VIEX - Pendiente Asignación',
            'En VIEX - En Evaluación', // Permite asignar evaluadores adicionales
        ];

        $currentStatus = $workOfExtension->currentStatus?->name;

        return in_array($currentStatus, $allowedStatuses, true);
    }

    /**
     * Determine whether the user can approve work as VIEX.
     * 
     * CU9 - Fase 7: Autorización para aprobación por VIEX
     */
    public function approveAsViex(User $user, WorkOfExtension $workOfExtension): bool
    {
        // Super admin siempre puede aprobar
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Solo administradores VIEX pueden aprobar
        if (!$user->hasRole('viex_admin')) {
            return false;
        }

        // Debe estar en estado "En VIEX - En Evaluación"
        $currentStatus = $workOfExtension->currentStatus?->name;

        if ($currentStatus !== 'En VIEX - En Evaluación') {
            return false;
        }

        // Verificar que todas las evaluaciones estén completas
        if (!$workOfExtension->allEvaluationsCompleted()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can reject work as VIEX.
     * 
     * CU9 - Fase 7: Autorización para rechazo por VIEX
     */
    public function rejectAsViex(User $user, WorkOfExtension $workOfExtension): bool
    {
        // Super admin siempre puede rechazar
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Solo administradores VIEX pueden rechazar
        if (!$user->hasRole('viex_admin')) {
            return false;
        }

        // Debe estar en estado "En VIEX - En Evaluación"
        $currentStatus = $workOfExtension->currentStatus?->name;

        return $currentStatus === 'En VIEX - En Evaluación';
    }

    /**
     * Determine whether the user can view work as an evaluator.
     * 
     * CU9 - Fase 7: Autorización para evaluadores
     */
    public function viewAsEvaluator(User $user, WorkOfExtension $workOfExtension): bool
    {
        // Super admin siempre puede ver
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Debe tener rol de evaluador
        if (!$user->hasRole('evaluador')) {
            return false;
        }

        // Verificar que esté asignado como evaluador a este trabajo
        return $workOfExtension->workEvaluators()
            ->where('evaluator_user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can submit evaluation for a work.
     * 
     * CU9 - Fase 7: Autorización para envío de evaluaciones
     */
    public function submitEvaluation(User $user, WorkOfExtension $workOfExtension): bool
    {
        // Super admin siempre puede evaluar
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Debe tener rol de evaluador
        if (!$user->hasRole('evaluador')) {
            return false;
        }

        // Verificar que esté asignado como evaluador
        $assignment = $workOfExtension->workEvaluators()
            ->where('evaluator_user_id', $user->id)
            ->first();

        if (!$assignment) {
            return false;
        }

        // Verificar que haya aceptado la asignación
        if ($assignment->status !== 'accepted' && $assignment->status !== 'in_progress') {
            return false;
        }

        // Verificar que el trabajo esté en evaluación
        $currentStatus = $workOfExtension->currentStatus?->name;

        return $currentStatus === 'En VIEX - En Evaluación';
    }
}
