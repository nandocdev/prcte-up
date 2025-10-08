<?php

namespace App\Notifications;

use App\Models\WorkEvaluation;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Evaluación Enviada
 *
 * Notifica al equipo VIEX que un evaluador ha completado
 * y enviado su evaluación de un trabajo.
 */
class EvaluationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo evaluado
     * @param WorkEvaluation $evaluation La evaluación enviada
     */
    public function __construct(
        public WorkOfExtension $work,
        public WorkEvaluation $evaluation
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
        $evaluator = $this->evaluation->evaluator;
        $decisionLabel = match ($this->evaluation->final_decision) {
            WorkEvaluation::DECISION_APPROVE => __('Aprobar'),
            WorkEvaluation::DECISION_APPROVE_WITH_CONDITIONS => __('Aprobar con Condiciones'),
            WorkEvaluation::DECISION_REJECT => __('Rechazar'),
            default => __('Pendiente'),
        };

        return (new MailMessage())
            ->subject(__('Evaluación Enviada - ') . $this->work->title)
            ->greeting(__('¡Hola!'))
            ->line(__('Una evaluación ha sido completada y enviada para un trabajo de extensión.'))
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Evaluador:** :evaluator', ['evaluator' => $evaluator->full_name]))
            ->line(__('**Puntuación Total:** :score / :max', [
                'score' => number_format($this->evaluation->total_score, 2),
                'max' => number_format($this->evaluation->max_possible_score, 2),
            ]))
            ->line(__('**Puntuación Ponderada:** :weighted%', [
                'weighted' => number_format($this->evaluation->weighted_score, 2),
            ]))
            ->line(__('**Decisión:** :decision', ['decision' => $decisionLabel]))
            ->action(__('Ver Evaluaciones'), route('viex.reviewEvaluations', $this->work))
            ->line(__('Por favor, revise la evaluación y determine si se requieren acciones adicionales.'));
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
            'evaluation_id' => $this->evaluation->id,
            'evaluator_name' => $this->evaluation->evaluator->full_name,
            'total_score' => $this->evaluation->total_score,
            'weighted_score' => $this->evaluation->weighted_score,
            'final_decision' => $this->evaluation->final_decision,
            'action_url' => route('viex.reviewEvaluations', $this->work),
            'message' => __('Nueva evaluación enviada para: :title', ['title' => $this->work->title]),
        ];
    }
}
