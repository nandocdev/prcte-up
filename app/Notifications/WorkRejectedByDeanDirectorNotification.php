<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo rechazado por Decano/Director
 */
class WorkRejectedByDeanDirectorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected WorkOfExtension $work;

    protected User $deanDirector;

    protected string $reason;

    protected string $recipientType;

    /**
     * @param WorkOfExtension $work Trabajo rechazado
     * @param User $deanDirector Usuario que rechaza
     * @param string $reason Comentarios del rechazo
     * @param string $recipientType Tipo de destinatario (responsible, participant, coordinator)
     */
    public function __construct(
        WorkOfExtension $work,
        User $deanDirector,
        string $reason,
        string $recipientType = 'responsible'
    ) {
        $this->work = $work;
        $this->deanDirector = $deanDirector;
        $this->reason = $reason;
        $this->recipientType = $recipientType;
    }

    /**
     * {@inheritdoc}
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * {@inheritdoc}
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->error()
            ->subject(__('Trabajo rechazado por Decano/Director - :title', ['title' => $this->work->title]))
            ->greeting(__('Estimado/a :name,', ['name' => $notifiable->name]))
            ->line($this->introMessage())
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Motivo del rechazo:**'))
            ->line($this->reason)
            ->line(__('**Rechazado por:** :name', ['name' => $this->deanDirector->name]))
            ->action(__('Ver Trabajo'), route('works.show', $this->work));

        if ($this->recipientType === 'coordinator') {
            $mail->line(__('Puede coordinar con el profesor responsable para subsanar las observaciones y reenviar el trabajo.'));
        } else {
            $mail->line(__('Puede realizar los ajustes necesarios y reenviar el trabajo a través de la plataforma.'));
        }

        return $mail;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array(
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->title,
            'work_type' => $this->work->workType->name,
            'reason' => $this->reason,
            'dean_director' => $this->deanDirector->name,
            'recipient_type' => $this->recipientType,
            'action_url' => url()->route('works.show', $this->work),
            'message' => $this->notificationMessage()
        );
    }

    /**
     * Obtener mensaje de introducción dependiendo del destinatario.
     */
    private function introMessage(): string
    {
        if ($this->recipientType === 'participant') {
            return __('Se le informa que el trabajo de extensión en el que participa ha sido rechazado por el Decano/Director.');
        }

        if ($this->recipientType === 'coordinator') {
            return __('Se le informa que el trabajo de extensión de su unidad ha sido rechazado por el Decano/Director.');
        }

        return __('Lamentamos informarle que su trabajo de extensión ha sido rechazado por el Decano/Director.');
    }

    /**
     * Obtener mensaje corto para la notificación en base de datos.
     */
    private function notificationMessage(): string
    {
        if ($this->recipientType === 'participant') {
            return __('Trabajo rechazado por Decano/Director: :title', ['title' => $this->work->title]);
        }

        if ($this->recipientType === 'coordinator') {
            return __('Trabajo de su unidad rechazado por Decano/Director: :title', ['title' => $this->work->title]);
        }

        return __('Su trabajo fue rechazado por Decano/Director: :title', ['title' => $this->work->title]);
    }
}
