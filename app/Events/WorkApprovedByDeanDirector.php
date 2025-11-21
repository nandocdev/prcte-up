<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo de Extensión Aprobado por Decano/Director
 *
 * Se dispara cuando un Decano/Director aprueba un trabajo y lo envía
 * a VIEX para evaluación (CU8 - Flujo Principal).
 *
 * Destinatarios de notificaciones:
 * - Equipo VIEX (debe recibir y evaluar el trabajo)
 * - Profesor responsable (informado del progreso)
 *
 * @package App\Events
 */
class WorkApprovedByDeanDirector
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * El trabajo de extensión aprobado.
     */
    public WorkOfExtension $work;

    /**
     * El decano/director que aprobó el trabajo.
     */
    public User $deanDirector;

    /**
     * Comentarios opcionales del decano/director sobre la aprobación.
     */
    public ?string $comments;

    /**
     * Crear un nuevo evento.
     *
     * @param WorkOfExtension $work El trabajo aprobado
     * @param User $deanDirector El decano/director que aprobó
     * @param string|null $comments Comentarios opcionales
     */
    public function __construct(WorkOfExtension $work, User $deanDirector, ?string $comments)
    {
        $this->work = $work;
        $this->deanDirector = $deanDirector;
        $this->comments = $comments;
    }
}