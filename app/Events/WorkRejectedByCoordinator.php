<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo de Extensión Rechazado por Coordinador
 *
 * Se dispara cuando un Coordinador de Extensión rechaza definitivamente un trabajo
 * debido a problemas significativos (CU7 - Flujo Alterno CU11).
 *
 * Destinatarios de notificaciones:
 * - Profesor responsable (debe realizar correcciones mayores antes de reenviar)
 *
 * @package App\Events
 */
class WorkRejectedByCoordinator
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * El trabajo de extensión rechazado.
     */
    public WorkOfExtension $work;

    /**
     * El coordinador que rechazó el trabajo.
     */
    public User $coordinator;

    /**
     * Razón detallada del rechazo.
     */
    public string $reason;

    /**
     * Crear un nuevo evento.
     *
     * @param WorkOfExtension $work El trabajo rechazado
     * @param User $coordinator El coordinador que rechazó
     * @param string $reason Explicación del rechazo
     */
    public function __construct(WorkOfExtension $work, User $coordinator, string $reason)
    {
        $this->work = $work;
        $this->coordinator = $coordinator;
        $this->reason = $reason;
    }
}
