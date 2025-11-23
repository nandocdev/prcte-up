<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo de Extensión Aprobado por Coordinador
 *
 * Se dispara cuando un Coordinador de Extensión aprueba un trabajo y lo envía
 * directamente a VIEX para evaluación final (CU-COORD-004 - Flujo Principal).
 *
 * Destinatarios de notificaciones:
 * - Usuarios VIEX (deben revisar y aprobar/certificar)
 * - Profesor responsable (informado del progreso)
 *
 * @package App\Events
 */
class WorkApprovedByCoordinator
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * El trabajo de extensión aprobado.
     */
    public WorkOfExtension $work;

    /**
     * El coordinador que aprobó el trabajo.
     */
    public User $coordinator;

    /**
     * Comentarios opcionales del coordinador sobre la aprobación.
     */
    public ?string $comments;

    /**
     * Crear un nuevo evento.
     *
     * @param WorkOfExtension $work El trabajo aprobado
     * @param User $coordinator El coordinador que aprobó
     * @param string|null $comments Comentarios opcionales
     */
    public function __construct(WorkOfExtension $work, User $coordinator, ?string $comments)
    {
        $this->work = $work;
        $this->coordinator = $coordinator;
        $this->comments = $comments;
    }
}
