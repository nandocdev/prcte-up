<?php

namespace App\Listeners;

use App\Events\WorkRejectedByDeanDirector;
use App\Models\User;
use App\Notifications\WorkRejectedByDeanDirectorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener: enviar notificación de rechazo por Decano/Director
 */
class SendWorkRejectedByDeanDirectorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WorkRejectedByDeanDirector $event): void
    {
        $work = $event->work;
        $deanDirector = $event->deanDirector;
        $reason = $event->reason;

        $responsibleUser = $work->responsibleUser;
        $coordinator = $this->findCoordinator($work);

        if ($responsibleUser) {
            $responsibleUser->notify(
                new WorkRejectedByDeanDirectorNotification(
                    work: $work,
                    deanDirector: $deanDirector,
                    reason: $reason,
                    recipientType: 'responsible'
                )
            );
        }

        foreach ($work->participants as $participant) {
            $participantUser = $participant->user;

            if (!$participantUser) {
                continue;
            }

            if ($responsibleUser && $participantUser->getKey() === $responsibleUser->getKey()) {
                continue;
            }

            if ($coordinator && $participantUser->getKey() === $coordinator->getKey()) {
                continue;
            }

            $participantUser->notify(
                new WorkRejectedByDeanDirectorNotification(
                    work: $work,
                    deanDirector: $deanDirector,
                    reason: $reason,
                    recipientType: 'participant'
                )
            );
        }

        if ($coordinator
            && (!$responsibleUser || $coordinator->getKey() !== $responsibleUser->getKey())
        ) {
            $coordinator->notify(
                new WorkRejectedByDeanDirectorNotification(
                    work: $work,
                    deanDirector: $deanDirector,
                    reason: $reason,
                    recipientType: 'coordinator'
                )
            );
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(WorkRejectedByDeanDirector $event, \Throwable $exception): void
    {
        Log::error('Failed to send WorkRejectedByDeanDirector notification', [
            'work_id' => $event->work->getKey(),
            'dean_director_id' => $event->deanDirector->getKey(),
            'exception' => $exception->getMessage(),
        ]);
    }

    /**
     * Buscar coordinador de la unidad asociada al trabajo.
     */
    private function findCoordinator($work): ?User
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'coordinador_extension');
        })
            ->where('main_organizational_unit_id', $work->getAttribute('organizational_unit_id'))
            ->first();
    }
}
