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
 * Notification: Trabajo de Extensión Rechazado por Coordinador
 *
 * Notifica al profesor que su trabajo ha sido rechazado por el coordinador
 * debido a problemas significativos que requieren corrección antes de reenvío.
 *
 * Destinatarios:
 * - Profesor responsable (debe realizar correcciones mayores)
 *
 * @package App\Notifications
 */
class WorkRejectedByCoordinatorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * El trabajo de extensión rechazado.
     */
    protected WorkOfExtension $work;

    /**
     * Razón detallada del rechazo.
     */
    protected string $reason;

    /**
     * El coordinador que rechazó el trabajo.
     */
    protected User $coordinator;

    /**
     * Crear una nueva notificación.
     *
     * @param WorkOfExtension $work El trabajo rechazado
     * @param string $reason Razón del rechazo
     * @param User $coordinator El coordinador que rechazó
     */
    public function __construct(WorkOfExtension $work, string $reason, User $coordinator)
    {
        $this->work = $work;
        $this->reason = $reason;
        $this->coordinator = $coordinator;
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
            ->subject(__('Su Trabajo de Extensión ha sido Rechazado por el Coordinador'))
            ->greeting(__('Estimado/a Profesor/a'))
            ->line(__('Lamentamos informarle que su trabajo de extensión ha sido rechazado por el coordinador de extensión.'))
            ->line(__('**Título del Trabajo:** :title', [
                'title' => $this->work->getAttribute('title'),
            ]))
            ->line(__('**Tipo de Trabajo:** :type', [
                'type' => $this->work->workType?->getAttribute('name') ?? 'N/A',
            ]))
            ->line(__('**Estado Actual:** Rechazado por Coordinador'))
            ->line('---')
            ->line(__('**Motivo del Rechazo:**'))
            ->line($this->reason)
            ->line('---')
            ->line(__('**¿Qué puede hacer ahora?**'))
            ->line(__('1. Revise detenidamente las observaciones del coordinador'))
            ->line(__('2. Realice las correcciones necesarias en su trabajo'))
            ->line(__('3. Una vez corregido, puede volver a enviar el trabajo para una nueva revisión'))
            ->action(__('Ver Mi Trabajo'), route('works.show', $this->work))
            ->line(__('Si tiene dudas sobre las observaciones, puede contactar con el coordinador de extensión de su unidad.'))
            ->line(__('**Coordinador:** :coordinator', [
                'coordinator' => $this->coordinator->getAttribute('name'),
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
            'type' => 'work_rejected_by_coordinator',
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'coordinator_id' => $this->coordinator->getKey(),
            'coordinator_name' => $this->coordinator->getAttribute('name'),
            'rejection_reason' => $this->reason,
            'action_url' => route('works.show', $this->work),
            'message' => __('Su trabajo ha sido rechazado por el coordinador: :title', [
                'title' => $this->work->getAttribute('title'),
            ]),
        ];
    }
}
