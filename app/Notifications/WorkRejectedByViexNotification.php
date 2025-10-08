<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo Rechazado por VIEX
 *
 * Notifica al profesor responsable que su trabajo ha sido
 * rechazado por VIEX después de la evaluación.
 */
class WorkRejectedByViexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo rechazado
     * @param User $rejector El administrador VIEX que rechaza
     * @param string $reason Motivo del rechazo
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $rejector,
        public string $reason
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
            ->error()
            ->subject(__('Trabajo No Aprobado por VIEX - ') . $this->work->title)
            ->greeting(__('Estimado/a :name,', ['name' => $notifiable->first_name]))
            ->line(__('Lamentamos informarle que su trabajo de extensión no ha sido aprobado por VIEX después de la evaluación.'))
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Evaluaciones completadas:** :count', ['count' => $evaluationSummary['completed_count']]))
            ->line(__('**Puntuación promedio:** :avg%', ['avg' => number_format($evaluationSummary['average_weighted_score'], 2)]))
            ->line(__('**Motivo del rechazo:**'))
            ->line($this->reason)
            ->line(__('**Rechazado por:** :rejector', ['rejector' => $this->rejector->full_name]))
            ->action(__('Ver Trabajo'), route('works.show', $this->work))
            ->line(__('Puede corregir su trabajo y volver a enviarlo para evaluación si lo considera pertinente.'))
            ->line(__('Para más información sobre los motivos del rechazo, puede revisar las observaciones de los evaluadores.'));
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
            'reason' => $this->reason,
            'rejector' => $this->rejector->full_name,
            'action_url' => route('works.show', $this->work),
            'message' => __('Su trabajo no ha sido aprobado por VIEX: :title', ['title' => $this->work->title]),
        ];
    }
}
