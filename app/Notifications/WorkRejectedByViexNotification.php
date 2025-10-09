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

    protected WorkOfExtension $work;

    protected User $rejector;

    protected string $reason;

    protected string $recipientType;

    /**
     * @param WorkOfExtension $work Trabajo rechazado
     * @param User $rejector Usuario que rechaza
     * @param string $reason Motivo del rechazo
     * @param string $recipientType Destinatario (responsible, participant, coordinator)
     */
    public function __construct(
        WorkOfExtension $work,
        User $rejector,
        string $reason,
        string $recipientType = 'responsible'
    ) {
        $this->work = $work;
        $this->rejector = $rejector;
        $this->reason = $reason;
        $this->recipientType = $recipientType;
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

        $mail = (new MailMessage())
            ->error()
            ->subject(__('Trabajo No Aprobado por VIEX - :title', ['title' => $this->work->title]))
            ->greeting(__('Estimado/a :name,', ['name' => $notifiable->name]))
            ->line($this->introMessage())
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Evaluaciones completadas:** :count', ['count' => $evaluationSummary['completed_count']]))
            ->line(__('**Puntuación promedio:** :avg%', ['avg' => number_format($evaluationSummary['average_weighted_score'], 2)]))
            ->line(__('**Motivo del rechazo:**'))
            ->line($this->reason)
            ->line(__('**Rechazado por:** :rejector', ['rejector' => $this->rejector->name]))
            ->action(__('Ver Trabajo'), route('works.show', $this->work));

        $mail->line($this->closingMessage());

        return $mail;
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
            'rejector' => $this->rejector->name,
            'recipient_type' => $this->recipientType,
            'action_url' => route('works.show', $this->work),
            'message' => $this->notificationMessage(),
        ];
    }

    /**
     * Mensaje de introducción acorde al destinatario.
     */
    private function introMessage(): string
    {
        if ($this->recipientType === 'participant') {
            return __('Se le informa que el trabajo de extensión en el que participa no fue aprobado por VIEX.');
        }

        if ($this->recipientType === 'coordinator') {
            return __('Se le informa que el trabajo de extensión de su unidad no fue aprobado por VIEX.');
        }

        return __('Lamentamos informarle que su trabajo de extensión no ha sido aprobado por VIEX después de la evaluación.');
    }

    /**
     * Mensaje de cierre por tipo de destinatario.
     */
    private function closingMessage(): string
    {
        if ($this->recipientType === 'coordinator') {
            return __('Por favor coordine con el profesor responsable para atender las observaciones y reenviar el trabajo.');
        }

        return __('Puede revisar las observaciones, realizar los ajustes necesarios y reenviar el trabajo cuando esté listo.');
    }

    /**
     * Texto breve para la notificación almacenada.
     */
    private function notificationMessage(): string
    {
        if ($this->recipientType === 'participant') {
            return __('Trabajo no aprobado por VIEX en el que participa: :title', ['title' => $this->work->title]);
        }

        if ($this->recipientType === 'coordinator') {
            return __('Trabajo no aprobado por VIEX en su unidad: :title', ['title' => $this->work->title]);
        }

        return __('Su trabajo no ha sido aprobado por VIEX: :title', ['title' => $this->work->title]);
    }
}
