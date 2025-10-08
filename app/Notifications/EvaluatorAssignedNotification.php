<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkEvaluator;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Evaluador Asignado
 *
 * Notifica a un evaluador que ha sido asignado para
 * evaluar un trabajo de extensión.
 */
class EvaluatorAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo a evaluar
     * @param WorkEvaluator $workEvaluator El registro de asignación
     * @param User $assignedBy El usuario que hizo la asignación
     */
    public function __construct(
        public WorkOfExtension $work,
        public WorkEvaluator $workEvaluator,
        public User $assignedBy
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
        $roleLabel = $this->workEvaluator->role === WorkEvaluator::ROLE_LEAD
            ? __('Evaluador Principal')
            : __('Evaluador');

        return (new MailMessage())
            ->subject(__('Asignación de Evaluación - ') . $this->work->title)
            ->greeting(__('¡Hola :name!', ['name' => $notifiable->first_name]))
            ->line(__('Ha sido asignado como **:role** para evaluar un trabajo de extensión.', ['role' => $roleLabel]))
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->full_name]))
            ->line(__('**Unidad Organizativa:** :unit', ['unit' => $this->work->organizationalUnit->name]))
            ->when(
                $this->workEvaluator->assignment_notes,
                fn (MailMessage $mail) => $mail->line(__('**Notas:** :notes', ['notes' => $this->workEvaluator->assignment_notes]))
            )
            ->line(__('**Asignado por:** :assigned_by', ['assigned_by' => $this->assignedBy->full_name]))
            ->action(__('Ver Asignación'), route('evaluator.show', $this->work))
            ->line(__('Por favor, acepte o decline esta asignación a la brevedad posible.'));
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
            'role' => $this->workEvaluator->role,
            'role_label' => $this->workEvaluator->role === WorkEvaluator::ROLE_LEAD
                ? __('Evaluador Principal')
                : __('Evaluador'),
            'assignment_notes' => $this->workEvaluator->assignment_notes,
            'assigned_by' => $this->assignedBy->full_name,
            'action_url' => route('evaluator.show', $this->work),
            'message' => __('Ha sido asignado para evaluar: :title', ['title' => $this->work->title]),
        ];
    }
}
