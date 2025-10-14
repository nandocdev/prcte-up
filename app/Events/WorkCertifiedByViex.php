<?php

namespace App\Events;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event: Trabajo Certificado por VIEX
 *
 * Se dispara cuando VIEX certifica directamente un trabajo
 * sin necesidad de evaluadores adicionales.
 */
class WorkCertifiedByViex
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo certificado
     * @param User $certifier El administrador VIEX que certifica
     * @param string|null $comments Comentarios de certificación
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $certifier,
        public ?string $comments = null
    ) {
    }
}