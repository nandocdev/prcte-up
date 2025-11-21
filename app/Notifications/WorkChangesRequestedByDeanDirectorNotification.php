<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Correcciones Solicitadas por Decano/Director
 *
 * Notifica al coordinador que el decano/director ha solicitado correcciones
 * en un trabajo y debe informar al profesor sobre los cambios requeridos.
 *
 * Destinatarios:
 * - Coordinador de extensión (debe informar al profesor)
 *
 * @package App\Notifications
 */
class WorkChangesRequestedByDeanDirectorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El trabajo de extensión que requiere correcciones.
     */
    protected WorkOfExtension $work;

    /**
     * Comentarios del decano/director explicando las correcciones requeridas.
     */
    protected string $comments;

    /**
     * El decano/director que solicitó las correcciones.
     */
    protected User $deanDirector;

    /**
     * Crear una nueva notificación.
     *
     * @param WorkOfExtension $work El trabajo que requiere correcciones
     * @param string $comments Comentarios del decano/director
     * @param User $deanDirector El decano/director que solicitó cambios
     */
    public function __construct(WorkOfExtension $work, string $comments, User $deanDirector)
    {
        $this->work = $work;
        $this->comments = $comments;
        $this->deanDirector = $deanDirector;
    }

    /**
     * Obtener los canales de entrega de la notificación.
     *
     * @param object $notifiable El objeto notificable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Obtener la representación de email de la notificación.
     *
     * @param object $notifiable El objeto notificable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Cargar relaciones necesarias
        $this->work->load(['workType', 'organizationalUnit', 'responsibleUser']);

        return (new MailMessage())
            ->subject(__('Correcciones Requeridas por Decano/Director en Trabajo de Extensión'))
            ->greeting(__('Estimado/a Coordinador/a'))
            ->line(__('El decano/director ha revisado el trabajo de extensión y solicita que se realicen correcciones antes de continuar con el proceso.'))
            ->line(__('**Título del Trabajo:** :title', [
                'title' => $this->work->getAttribute('title'),
            ]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Profesor Responsable:** :professor', [
                'professor' => $this->work->responsibleUser?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Estado Actual:** Rechazado por Decano/Director'))
            ->line('---')
            ->line(__('**Correcciones Solicitadas por el Decano/Director:**'))
            ->line($this->comments)
            ->line('---')
            ->line(__('Por favor, comunique estas observaciones al profesor responsable para que realice las correcciones necesarias.'))
            ->action(__('Ver Detalles del Trabajo'), route('coordinator.show', $this->work))
            ->line(__('Una vez que el profesor realice las correcciones, podrá reenviar el trabajo para revisión.'))
            ->line(__('**Decano/Director:** :dean_director', [
                'dean_director' => $this->deanDirector->getAttribute('name'),
            ]))
            ->salutation(__('Cordialmente,') . "\n" . __('Sistema VIEX - Universidad de Panamá'));
    }

    /**
     * Obtener la representación en array de la notificación (para base de datos).
     *
     * @param object $notifiable El objeto notificable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'work_changes_requested_by_dean_director',
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'dean_director_id' => $this->deanDirector->getKey(),
            'dean_director_name' => $this->deanDirector->getAttribute('name'),
            'comments' => $this->comments,
            'action_url' => url()->route('coordinator.show', $this->work),
            'message' => __('El decano/director solicita correcciones en el trabajo: :title', [
                'title' => $this->work->getAttribute('title'),
            ]),
        ];
    }
}