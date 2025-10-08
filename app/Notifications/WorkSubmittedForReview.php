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
 * CU05: Subsanar trabajo rechazado - reutilizada para reenvíos con contexto diferenciado
 */
class WorkSubmittedForReview extends Notification implements ShouldQueue {
    use Queueable;

    public WorkOfExtension $work;
    public bool $isResubmission;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo enviado
     * @param bool $isResubmission True si es un reenvío después de rechazo
     */
    public function __construct(WorkOfExtension $work, bool $isResubmission = false)
    {
        $this->work = $work;
        $this->isResubmission = $isResubmission;
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
        // Mensaje diferenciado según contexto
        $subject = $this->isResubmission
            ? __('Trabajo de Extensión Corregido y Reenviado para Revisión')
            : __('Nuevo Trabajo de Extensión Enviado para Revisión');

        $introLine = $this->isResubmission
            ? __('Se ha corregido y reenviado un trabajo de extensión para su revisión.')
            : __('Se ha enviado un nuevo trabajo de extensión para su revisión.');

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('Estimado/a Coordinador/a de Extensión'))
            ->line($introLine)
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name ?? 'N/A']))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->name ?? 'N/A']))
            ->line(__('**Unidad Organizacional:** :unit', ['unit' => $this->work->organizationalUnit->name ?? 'N/A']))
            ->line(__('**Fecha de envío:** :date', ['date' => $this->work->getAttribute('submitted_at')?->format('d/m/Y H:i') ?? 'N/A']))
            ->when($this->isResubmission, function (MailMessage $mail) {
                return $mail->line('**Nota:** Este trabajo ha sido corregido según las observaciones realizadas.');
            })
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
        $message = $this->isResubmission
            ? __('Trabajo de extensión corregido y reenviado: :title', ['title' => $this->work->getAttribute('title')])
            : __('Nuevo trabajo de extensión enviado para revisión: :title', ['title' => $this->work->getAttribute('title')]);

        return [
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'work_type' => $this->work->workType->name ?? 'N/A',
            'responsible_name' => $this->work->responsibleUser->name ?? 'N/A',
            'organizational_unit' => $this->work->organizationalUnit->name ?? 'N/A',
            'submitted_at' => $this->work->getAttribute('submitted_at')?->format('Y-m-d H:i:s'),
            'is_resubmission' => $this->isResubmission,
            'action_url' => route('works.show', $this->work),
            'message' => $message
        ];
    }
}
