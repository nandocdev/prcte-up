<?php

namespace App\Notifications;

use App\Models\WorkMessage;
use App\Models\WorkOfExtension;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewWorkMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public WorkMessage $message;
    public WorkOfExtension $work;
    public User $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkMessage $message, WorkOfExtension $work, User $sender)
    {
        $this->message = $message;
        $this->work = $work;
        $this->sender = $sender;
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
        return (new MailMessage)
            ->subject('Nuevo mensaje en el trabajo: ' . $this->work->title)
            ->greeting('Hola ' . $notifiable->name)
            ->line($this->sender->name . ' te ha enviado un mensaje relacionado con el trabajo "' . $this->work->title . '".')
            ->line('Mensaje: ' . Str::limit($this->message->message, 100))
            ->action('Ver Mensaje', route('works.messages.show', $this->work))
            ->line('Por favor, revisa el chat para continuar la conversación.')
            ->salutation('Saludos, Equipo VIEX');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'work_id' => $this->work->id,
            'work_title' => $this->work->title,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message_preview' => Str::limit($this->message->message, 50),
            'type' => 'work_message',
        ];
    }
}
