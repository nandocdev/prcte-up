<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification: Correcciones Solicitadas por VIEX
 *
 * Notifica al profesor que VIEX ha solicitado correcciones en su trabajo
 * y debe realizar los cambios indicados.
 *
 * Destinatarios:
 * - Profesor responsable (debe realizar las correcciones)
 *
 * @package App\Notifications
 */
class WorkChangesRequestedByViexNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El trabajo de extensión que requiere correcciones.
     */
    protected WorkOfExtension $work;

    /**
     * Comentarios de VIEX explicando las correcciones requeridas.
     */
    protected string $comments;

    /**
     * El administrador de VIEX que solicitó las correcciones.
     */
    protected User $viexAdmin;

    /**
     * Crear una nueva notificación.
     *
     * @param WorkOfExtension $work El trabajo que requiere correcciones
     * @param string $comments Comentarios de VIEX
     * @param User $viexAdmin El administrador de VIEX que solicitó cambios
     */
    public function __construct(WorkOfExtension $work, string $comments, User $viexAdmin)
    {
        $this->work = $work;
        $this->comments = $comments;
        $this->viexAdmin = $viexAdmin;
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
        $this->work->load(['workType', 'organizationalUnit']);

        return (new MailMessage())
            ->subject(__('Correcciones Requeridas en su Trabajo de Extensión'))
            ->greeting(__('Estimado/a Profesor/a'))
            ->line(__('VIEX ha revisado su trabajo y solicita que realice algunas correcciones antes de continuar con el proceso de certificación.'))
            ->line(__('**Título del Trabajo:** :title', [
                'title' => $this->work->getAttribute('title'),
            ]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Estado Actual:** Devuelto para Corrección'))
            ->line('---')
            ->line(__('**Correcciones Solicitadas por VIEX:**'))
            ->line($this->comments)
            ->line('---')
            ->line(__('Por favor, realice las correcciones indicadas y vuelva a enviar su trabajo para revisión.'))
            ->action(__('Ir a Mi Trabajo'), route('works.edit', $this->work))
            ->line(__('Una vez realizadas las correcciones, puede reenviar el trabajo desde el sistema.'))
            ->line(__('**Evaluador VIEX:** :viex_admin', [
                'viex_admin' => $this->viexAdmin->getAttribute('name'),
            ]))
            ->salutation(__('Cordialmente,') . "\n" . __('Sistema VIEX - Universidad de Panamá'));
    }

    /**
     * Obtener la representación en array de la notificación (para base de datos).
     *
     * @param object $notifiable El objeto notificable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'work_changes_requested_by_viex',
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'viex_admin_id' => $this->viexAdmin->getKey(),
            'viex_admin_name' => $this->viexAdmin->getAttribute('name'),
            'comments' => $this->comments,
            'action_url' => url()->route('works.edit', $this->work),
            'message' => __('VIEX solicita correcciones en su trabajo: :title', [
                'title' => $this->work->getAttribute('title'),
            ]),
        ];
    }
}