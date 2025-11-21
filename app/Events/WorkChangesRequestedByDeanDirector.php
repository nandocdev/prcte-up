<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Solicitud de Correcciones por Decano/Director
 *
 * Se dispara cuando un Decano o Director solicita correcciones o subsanaciones
 * en un trabajo y lo devuelve al coordinador (CU10 - Flujo Alterno).
 *
 * Destinatarios de notificaciones:
 * - Coordinador de extensión (debe informar al profesor sobre las correcciones)
 *
 * @package App\Events
 */
class WorkChangesRequestedByDeanDirector
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * El trabajo de extensión que requiere correcciones.
     */
    public WorkOfExtension $work;

    /**
     * El decano/director que solicitó las correcciones.
     */
    public User $deanDirector;

    /**
     * Comentarios del decano/director explicando las correcciones requeridas.
     */
    public string $comments;

    /**
     * Crear un nuevo evento.
     *
     * @param WorkOfExtension $work El trabajo que requiere correcciones
     * @param User $deanDirector El decano/director que solicitó cambios
     * @param string $comments Explicación de las correcciones requeridas
     */
    public function __construct(WorkOfExtension $work, User $deanDirector, string $comments)
    {
        $this->work = $work;
        $this->deanDirector = $deanDirector;
        $this->comments = $comments;
    }
}