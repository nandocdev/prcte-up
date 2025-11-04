<?php

namespace App\Notifications;

use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación enviada a VIEX cuando un profesor autoriza la publicación
 * CU06: Autorizar Publicación de Resultados
 */
class WorkPublicationAuthorizedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public WorkOfExtension $work;
    public bool $isAuthorized;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo
     * @param bool $isAuthorized True = autorizado, False = revocado
     */
    public function __construct(WorkOfExtension $work, bool $isAuthorized)
    {
        $this->work = $work;
        $this->isAuthorized = $isAuthorized;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->isAuthorized
            ? __('Autorización de Publicación de Trabajo de Extensión')
            : __('Revocación de Autorización de Publicación de Trabajo');

        $introLine = $this->isAuthorized
            ? __('El profesor ha autorizado la publicación del siguiente trabajo de extensión:')
            : __('El profesor ha revocado la autorización de publicación del siguiente trabajo:');

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting(__('Estimado Equipo VIEX - Proyectos'))
            ->line($introLine)
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo de Trabajo:** :type', ['type' => $this->work->workType->name ?? 'N/A']))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->name ?? 'N/A']))
            ->line(__('**Unidad Organizacional:** :unit', ['unit' => $this->work->organizationalUnit->name ?? 'N/A']))
            ->line(__('**Período Académico:** :period', ['period' => $this->work->getAttribute('academic_period') ?? 'N/A']));

        if ($this->isAuthorized) {
            $mail->line(__('El profesor ha dado su consentimiento para que los resultados de este trabajo puedan ser publicados por la Universidad de Panamá en medios oficiales y portales institucionales.'))
                ->line(__('Esta autorización permite incluir el trabajo en el Informe Final de la Comisión de Evaluación de Proyectos de Extensión y en otros medios de divulgación institucional.'));
        } else {
            $mail->line(__('El profesor ha decidido revocar su autorización previamente otorgada.'))
                ->line(__('Por favor, tomar nota de este cambio en los registros correspondientes.'));
        }

        $mail->action(__('Ver Trabajo en Sistema'), route('works.show', $this->work))
            ->line(__('Esta notificación se envió automáticamente desde el Sistema VIEX.'))
            ->salutation(__('Atentamente, Sistema VIEX - Universidad de Panamá'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'is_authorized' => $this->isAuthorized,
            'responsible_name' => $this->work->responsibleUser->name ?? 'N/A',
            'action_url' => url()->route('works.show', $this->work),
        ];
    }
}
