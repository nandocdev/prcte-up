<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo de Extensión Aprobado por Coordinador
 *
 * Notifica a los destinatarios que un trabajo ha sido aprobado por el
 * coordinador de extensión y enviado al siguiente nivel (Decano/Director).
 *
 * Destinatarios:
 * - Decano/Director (debe revisar y aprobar)
 * - Profesor (informado del progreso)
 *
 * @package App\Notifications
 */
class WorkApprovedByCoordinatorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El trabajo de extensión aprobado.
     */
    protected WorkOfExtension $work;

    /**
     * Comentarios opcionales del coordinador.
     */
    protected ?string $comments;

    /**
     * Tipo de destinatario: 'dean' o 'professor'.
     */
    protected string $recipientType;

    /**
     * Crear una nueva notificación.
     *
     * @param WorkOfExtension $work El trabajo aprobado
     * @param string|null $comments Comentarios del coordinador
     * @param string $recipientType Tipo de destinatario ('dean' o 'professor')
     */
    public function __construct(WorkOfExtension $work, ?string $comments, string $recipientType)
    {
        $this->work = $work;
        $this->comments = $comments;
        $this->recipientType = $recipientType;
    }

    /**
     * Obtener los canales de entrega de la notificación.
     *
     * @param object $notifiable El objeto notificable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Obtener la representación de email de la notificación.
     *
     * @param object $notifiable El objeto notificable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Cargar relaciones necesarias
        $this->work->load(['workType', 'responsibleUser', 'organizationalUnit']);

        if ($this->recipientType === 'dean') {
            return $this->buildDeanEmail($notifiable);
        }

        return $this->buildProfessorEmail($notifiable);
    }

    /**
     * Construir el email para el Decano/Director.
     *
     * @param object $notifiable El objeto notificable
     * @return MailMessage
     */
    private function buildDeanEmail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject(__('Trabajo de Extensión Aprobado por Coordinador - Requiere su Revisión'))
            ->greeting(__('Estimado/a Decano/Director'))
            ->line(__('Un trabajo de extensión ha sido aprobado por el coordinador y requiere su revisión y aprobación.'))
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Profesor Responsable:** :professor', [
                'professor' => $this->work->responsibleUser?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Unidad Organizacional:** :unit', [
                'unit' => $this->work->organizationalUnit?->getAttribute('name') ?? 'N/A',
            ]));

        if ($this->comments) {
            $mail->line(__('**Comentarios del Coordinador:**'))
                ->line($this->comments);
        }

        $mail->line(__('El trabajo está ahora pendiente de su revisión y aprobación para ser enviado a VIEX.'))
            ->action(__('Revisar Trabajo en Sistema'), route('dean.show', $this->work))
            ->line(__('Por favor, revise el trabajo a la brevedad posible.'))
            ->salutation(__('Cordialmente,') . "\n" . __('Sistema VIEX - Universidad de Panamá'));

        return $mail;
    }

    /**
     * Construir el email para el Profesor.
     *
     * @param object $notifiable El objeto notificable
     * @return MailMessage
     */
    private function buildProfessorEmail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject(__('Su Trabajo de Extensión ha sido Aprobado por el Coordinador'))
            ->greeting(__('Estimado/a Profesor/a'))
            ->line(__('Le informamos que su trabajo de extensión ha sido aprobado por el coordinador y enviado al Decano/Director para revisión.'))
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Estado Actual:** Enviado a Decano/Director'));

        if ($this->comments) {
            $mail->line(__('**Comentarios del Coordinador:**'))
                ->line($this->comments);
        }

        $mail->line(__('Su trabajo avanza en el proceso de revisión. Será notificado cuando el Decano/Director realice su evaluación.'))
            ->action(__('Ver Detalles del Trabajo'), route('works.show', $this->work))
            ->salutation(__('Cordialmente,') . "\n" . __('Sistema VIEX - Universidad de Panamá'));

        return $mail;
    }

    /**
     * Obtener la representación en array de la notificación (para base de datos).
     *
     * @param object $notifiable El objeto notificable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->recipientType === 'dean'
            ? __('Trabajo aprobado por coordinador, requiere su revisión: :title', [
                'title' => $this->work->getAttribute('title'),
            ])
            : __('Su trabajo ha sido aprobado por el coordinador: :title', [
                'title' => $this->work->getAttribute('title'),
            ]);

        return [
            'type' => 'work_approved_by_coordinator',
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'coordinator_comments' => $this->comments,
            'recipient_type' => $this->recipientType,
            'action_url' => $this->recipientType === 'dean'
                ? route('dean.show', $this->work)
                : route('works.show', $this->work),
            'message' => $message,
        ];
    }
}
