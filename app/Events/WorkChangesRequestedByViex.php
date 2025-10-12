<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Solicitud de Correcciones por VIEX
 *
 * Se dispara cuando VIEX solicita correcciones en un trabajo y lo devuelve al profesor.
 *
 * Destinatarios de notificaciones:
 * - Profesor responsable (debe realizar las correcciones solicitadas)
 *
 * @package App\Events
 */
class WorkChangesRequestedByViex
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * El trabajo de extensión que requiere correcciones.
     */
    public WorkOfExtension $work;

    /**
     * El administrador de VIEX que solicitó las correcciones.
     */
    public User $viexAdmin;

    /**
     * Comentarios de VIEX explicando las correcciones requeridas.
     */
    public string $comments;

    /**
     * Crear un nuevo evento.
     *
     * @param WorkOfExtension $work El trabajo que requiere correcciones
     * @param User $viexAdmin El administrador de VIEX que solicitó cambios
     * @param string $comments Explicación de las correcciones requeridas
     */
    public function __construct(WorkOfExtension $work, User $viexAdmin, string $comments)
    {
        $this->work = $work;
        $this->viexAdmin = $viexAdmin;
        $this->comments = $comments;
    }
}