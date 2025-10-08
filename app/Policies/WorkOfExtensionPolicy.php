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
            return $workOfExtension->getAttribute('primary_responsible_user_id') === $user->getKey();
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
        if ($workOfExtension->getAttribute('primary_responsible_user_id') !== $user->getKey()) {
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
        if ($workOfExtension->getAttribute('primary_responsible_user_id') !== $user->getKey()) {
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
}
