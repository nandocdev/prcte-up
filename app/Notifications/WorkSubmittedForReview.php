<?php

namespace App\Notifications;

use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación enviada al coordinador cuando un trabajo es sometido a revisión
 * CU04: Enviar trabajo a coordinador - componente de notificación
 */
class WorkSubmittedForReview extends Notification implements ShouldQueue {
    use Queueable;

    public WorkOfExtension $work;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkOfExtension $work) {
        $this->work = $work;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)
            ->subject(__('Nuevo Trabajo de Extensión Enviado para Revisión'))
            ->greeting(__('Estimado/a Coordinador/a de Extensión'))
            ->line(__('Se ha enviado un nuevo trabajo de extensión para su revisión.'))
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name ?? 'N/A']))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->name ?? 'N/A']))
            ->line(__('**Unidad Organizacional:** :unit', ['unit' => $this->work->organizationalUnit->name ?? 'N/A']))
            ->line(__('**Fecha de envío:** :date', ['date' => $this->work->getAttribute('submitted_at')?->format('d/m/Y H:i') ?? 'N/A']))
            ->action(__('Revisar Trabajo'), route('works.show', $this->work))
            ->line(__('Por favor, revise el trabajo y proceda con la evaluación correspondiente.'))
            ->salutation(__('Atentamente, Sistema VIEX - Universidad de Panamá'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array {
        return [
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'work_type' => $this->work->workType->name ?? 'N/A',
            'responsible_name' => $this->work->responsibleUser->name ?? 'N/A',
            'organizational_unit' => $this->work->organizationalUnit->name ?? 'N/A',
            'submitted_at' => $this->work->getAttribute('submitted_at')?->format('Y-m-d H:i:s'),
            'action_url' => route('works.show', $this->work),
            'message' => __('Nuevo trabajo de extensión enviado para revisión: :title', ['title' => $this->work->getAttribute('title')])
        ];
    }
}
