<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Solicitud de Subsanaciones por Coordinador
 *
 * Se dispara cuando un Coordinador de Extensión solicita correcciones o subsanaciones
 * en un trabajo y lo devuelve al profesor (CU7 - Flujo Alterno CU10).
 *
 * Destinatarios de notificaciones:
 * - Profesor responsable (debe realizar las correcciones solicitadas)
 *
 * @package App\Events
 */
class WorkChangesRequestedByCoordinator
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * El trabajo de extensión que requiere subsanaciones.
     */
    public WorkOfExtension $work;

    /**
     * El coordinador que solicitó las subsanaciones.
     */
    public User $coordinator;

    /**
     * Comentarios del coordinador explicando las subsanaciones requeridas.
     */
    public string $comments;

    /**
     * Crear un nuevo evento.
     *
     * @param WorkOfExtension $work El trabajo que requiere subsanaciones
     * @param User $coordinator El coordinador que solicitó cambios
     * @param string $comments Explicación de las subsanaciones requeridas
     */
    public function __construct(WorkOfExtension $work, User $coordinator, string $comments)
    {
        $this->work = $work;
        $this->coordinator = $coordinator;
        $this->comments = $comments;
    }
}
