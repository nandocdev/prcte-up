<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo Aprobado por VIEX
 *
 * Notifica al profesor responsable que su trabajo ha sido
 * aprobado por VIEX y procederá a certificación.
 */
class WorkApprovedByViexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo aprobado
     * @param User $approver El administrador VIEX que aprueba
     * @param string|null $comments Comentarios de aprobación
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $approver,
        public ?string $comments = null
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
        $evaluationSummary = $this->work->getEvaluationSummary();

        return (new MailMessage())
            ->subject(__('¡Trabajo Aprobado por VIEX! - ') . $this->work->title)
            ->greeting(__('¡Felicitaciones :name!', ['name' => $notifiable->first_name]))
            ->line(__('Su trabajo de extensión ha sido **aprobado** por VIEX después de la evaluación correspondiente.'))
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Evaluaciones completadas:** :count', ['count' => $evaluationSummary['completed_count']]))
            ->line(__('**Puntuación promedio:** :avg%', ['avg' => number_format($evaluationSummary['average_weighted_score'], 2)]))
            ->when(
                $this->comments,
                fn (MailMessage $mail) => $mail->line(__('**Comentarios:** :comments', ['comments' => $this->comments]))
            )
            ->line(__('**Aprobado por:** :approver', ['approver' => $this->approver->full_name]))
            ->action(__('Ver Trabajo'), route('works.show', $this->work))
            ->line(__('Su trabajo ahora procederá al proceso de certificación. ¡Excelente trabajo!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $evaluationSummary = $this->work->getEvaluationSummary();

        return [
            'work_id' => $this->work->id,
            'work_title' => $this->work->title,
            'work_type' => $this->work->workType->name,
            'evaluation_summary' => $evaluationSummary,
            'comments' => $this->comments,
            'approver' => $this->approver->full_name,
            'action_url' => route('works.show', $this->work),
            'message' => __('¡Su trabajo ha sido aprobado por VIEX! :title', ['title' => $this->work->title]),
        ];
    }
}
