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
 * Notification: Subsanaciones Solicitadas por Coordinador
 *
 * Notifica al profesor que el coordinador ha solicitado correcciones o
 * subsanaciones en su trabajo y debe realizar los cambios indicados.
 *
 * Destinatarios:
 * - Profesor responsable (debe realizar las subsanaciones)
 *
 * @package App\Notifications
 */
class WorkChangesRequestedByCoordinatorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El trabajo de extensión que requiere subsanaciones.
     */
    protected WorkOfExtension $work;

    /**
     * Comentarios del coordinador explicando las subsanaciones requeridas.
     */
    protected string $comments;

    /**
     * El coordinador que solicitó las subsanaciones.
     */
    protected User $coordinator;

    /**
     * Crear una nueva notificación.
     *
     * @param WorkOfExtension $work El trabajo que requiere subsanaciones
     * @param string $comments Comentarios del coordinador
     * @param User $coordinator El coordinador que solicitó cambios
     */
    public function __construct(WorkOfExtension $work, string $comments, User $coordinator)
    {
        $this->work = $work;
        $this->comments = $comments;
        $this->coordinator = $coordinator;
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
        $this->work->load(['workType', 'organizationalUnit']);

        return (new MailMessage())
            ->subject(__('Subsanaciones Requeridas en su Trabajo de Extensión'))
            ->greeting(__('Estimado/a Profesor/a'))
            ->line(__('El coordinador de extensión ha revisado su trabajo y solicita que realice algunas correcciones antes de continuar con el proceso de revisión.'))
            ->line(__('**Título del Trabajo:** :title', [
                'title' => $this->work->getAttribute('title'),
            ]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Estado Actual:** Devuelto para Corrección'))
            ->line('---')
            ->line(__('**Subsanaciones Solicitadas por el Coordinador:**'))
            ->line($this->comments)
            ->line('---')
            ->line(__('Por favor, realice las correcciones indicadas y vuelva a enviar su trabajo para revisión.'))
            ->action(__('Ir a Mi Trabajo'), route('works.edit', $this->work))
            ->line(__('Una vez realizadas las correcciones, puede reenviar el trabajo desde el sistema.'))
            ->line(__('**Coordinador:** :coordinator', [
                'coordinator' => $this->coordinator->getAttribute('name'),
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
            'type' => 'work_changes_requested_by_coordinator',
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'coordinator_id' => $this->coordinator->getKey(),
            'coordinator_name' => $this->coordinator->getAttribute('name'),
            'comments' => $this->comments,
            'action_url' => route('works.edit', $this->work),
            'message' => __('El coordinador solicita subsanaciones en su trabajo: :title', [
                'title' => $this->work->getAttribute('title'),
            ]),
        ];
    }
}
