<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo Recibido en VIEX
 *
 * Notifica al equipo VIEX que un nuevo trabajo ha sido
 * recibido y requiere asignación de evaluadores.
 */
class WorkReceivedInViexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo recibido
     * @param User $receivedBy El admin que recibió el trabajo
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $receivedBy
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Nuevo Trabajo Recibido en VIEX - ') . $this->work->title)
            ->greeting(__('¡Hola!'))
            ->line(__('Un nuevo trabajo de extensión ha sido recibido en VIEX y requiere asignación de evaluadores.'))
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->full_name]))
            ->line(__('**Unidad Organizativa:** :unit', ['unit' => $this->work->organizationalUnit->name]))
            ->line(__('**Recibido por:** :received_by', ['received_by' => $this->receivedBy->full_name]))
            ->action(__('Ver Trabajo'), route('viex.show', $this->work))
            ->line(__('Por favor, revise el trabajo y asigne los evaluadores correspondientes.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'work_id' => $this->work->id,
            'work_title' => $this->work->title,
            'work_type' => $this->work->workType->name,
            'responsible_user' => $this->work->responsibleUser->full_name,
            'organizational_unit' => $this->work->organizationalUnit->name,
            'received_by' => $this->receivedBy->full_name,
            'action_url' => url()->route('viex.show', $this->work),
            'message' => __('Nuevo trabajo recibido en VIEX: :title', ['title' => $this->work->title]),
        ];
    }
}
