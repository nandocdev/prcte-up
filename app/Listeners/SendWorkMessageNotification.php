<?php

namespace App\Listeners;

use App\Events\WorkMessageSent;
use App\Notifications\NewWorkMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWorkMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WorkMessageSent $event): void
    {
        // Enviar notificación al destinatario
        $event->recipient->notify(new NewWorkMessage($event->message, $event->work, $event->sender));
    }
}
