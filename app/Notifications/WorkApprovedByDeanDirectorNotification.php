<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Trabajo de Extensión Aprobado por Decano/Director
 *
 * Notifica a los destinatarios que un trabajo ha sido aprobado por el
 * decano/director y enviado a VIEX para evaluación.
 *
 * Destinatarios:
 * - Equipo VIEX (debe recibir y evaluar el trabajo)
 * - Profesor (informado del progreso)
 *
 * @package App\Notifications
 */
class WorkApprovedByDeanDirectorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El trabajo de extensión aprobado.
     */
    protected WorkOfExtension $work;

    /**
     * Comentarios opcionales del decano/director.
     */
    protected ?string $comments;

    /**
     * Tipo de destinatario: 'viex' o 'professor'.
     */
    protected string $recipientType;

    /**
     * Crear una nueva notificación.
     *
     * @param WorkOfExtension $work El trabajo aprobado
     * @param string|null $comments Comentarios del decano/director
     * @param string $recipientType Tipo de destinatario ('viex' o 'professor')
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

        if ($this->recipientType === 'viex') {
            return $this->buildViexEmail($notifiable);
        }

        return $this->buildProfessorEmail($notifiable);
    }

    /**
     * Construir el email para el Equipo VIEX.
     *
     * @param object $notifiable El objeto notificable
     * @return MailMessage
     */
    private function buildViexEmail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject(__('Nuevo Trabajo de Extensión Aprobado - Requiere Evaluación VIEX'))
            ->greeting(__('Estimado/a Equipo VIEX'))
            ->line(__('Un trabajo de extensión ha sido aprobado por el Decano/Director y requiere su evaluación.'))
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
            $mail->line(__('**Comentarios del Decano/Director:**'))
                ->line($this->comments);
        }

        $mail->line(__('El trabajo está ahora pendiente de evaluación por parte de VIEX.'))
            ->action(__('Evaluar Trabajo en Sistema'), route('viex.show', $this->work))
            ->line(__('Por favor, proceda con la evaluación del trabajo a la brevedad posible.'))
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
            ->subject(__('Su Trabajo de Extensión ha sido Aprobado por el Decano/Director'))
            ->greeting(__('Estimado/a Profesor/a'))
            ->line(__('Le informamos que su trabajo de extensión ha sido aprobado por el Decano/Director y enviado a VIEX para evaluación.'))
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Estado Actual:** Enviado a VIEX para evaluación'));

        if ($this->comments) {
            $mail->line(__('**Comentarios del Decano/Director:**'))
                ->line($this->comments);
        }

        $mail->line(__('Su trabajo avanza en el proceso de evaluación. Será notificado cuando VIEX complete su evaluación.'))
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
        $message = $this->recipientType === 'viex'
            ? __('Nuevo trabajo aprobado por decano/director, requiere evaluación: :title', [
                'title' => $this->work->getAttribute('title'),
            ])
            : __('Su trabajo ha sido aprobado por el decano/director: :title', [
                'title' => $this->work->getAttribute('title'),
            ]);

        return [
            'type' => 'work_approved_by_dean_director',
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'dean_director_comments' => $this->comments,
            'recipient_type' => $this->recipientType,
            'action_url' => $this->recipientType === 'viex'
                ? url()->route('viex.show', $this->work)
                : url()->route('works.show', $this->work),
            'message' => $message,
        ];
    }
}