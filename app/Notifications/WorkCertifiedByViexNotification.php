<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo Certificado por VIEX
 *
 * Notifica al profesor responsable que su trabajo ha sido
 * certificado directamente por VIEX.
 */
class WorkCertifiedByViexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo certificado
     * @param User $certifier El administrador VIEX que certifica
     * @param string|null $comments Comentarios de certificación
     */
    public function __construct(
        public WorkOfExtension $work,
        public User $certifier,
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
        return (new MailMessage())
            ->subject(__('¡Trabajo Certificado por VIEX! - ') . $this->work->title)
            ->greeting(__('¡Felicitaciones :name!', ['name' => $notifiable->first_name]))
            ->line(__('Su trabajo de extensión ha sido **certificado** directamente por VIEX.'))
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Número de Certificación:** :number', ['number' => $this->work->certification->certification_number]))
            ->line(__('**Fecha de Certificación:** :date', ['date' => optional($this->work->certification->issue_date)->format('d/m/Y')]))
            ->line(__('**Válido hasta:** :date', ['date' => optional($this->work->certification->valid_until)->format('d/m/Y')]))
            ->when(
                $this->comments,
                fn (MailMessage $mail) => $mail->line(__('**Comentarios:** :comments', ['comments' => $this->comments]))
            )
            ->line(__('**Certificado por:** :certifier', ['certifier' => $this->certifier->full_name]))
            ->action(__('Descargar Certificación'), route('certificate.download', $this->work->certification))
            ->action(__('Ver Trabajo'), route('works.show', $this->work))
            ->line(__('¡Su trabajo ha sido certificado exitosamente! Puede descargar la certificación oficial desde el enlace anterior.'));
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
            'certification_number' => $this->work->certification->certification_number,
            'issue_date' => optional($this->work->certification->issue_date)->format('d/m/Y'),
            'valid_until' => optional($this->work->certification->valid_until)->format('d/m/Y'),
            'comments' => $this->comments,
            'certifier' => $this->certifier->full_name,
            'download_url' => url()->route('certificate.download', $this->work->certification),
            'work_url' => url()->route('works.show', $this->work),
            'message' => __('¡Su trabajo ha sido certificado por VIEX! :title', ['title' => $this->work->title]),
        ];
    }
}