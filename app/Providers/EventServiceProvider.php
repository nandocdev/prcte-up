<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\WorkSubmitted;
use App\Events\WorkPublicationAuthorized;
use App\Events\WorkApprovedByCoordinator;
use App\Events\WorkChangesRequestedByCoordinator;
use App\Events\WorkRejectedByCoordinator;
use App\Listeners\SendWorkSubmittedNotification;
use App\Listeners\SendPublicationAuthorizedNotification;
use App\Listeners\SendWorkApprovedByCoordinatorNotification;
use App\Listeners\SendWorkChangesRequestedByCoordinatorNotification;
use App\Listeners\SendWorkRejectedByCoordinatorNotification;

/**
 * Proveedor de servicios de eventos
 * Registra los eventos y listeners del sistema
 */
class EventServiceProvider extends ServiceProvider {
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // CU4: Enviar Trabajo a Revisión
        WorkSubmitted::class => [
            SendWorkSubmittedNotification::class,
        ],

        // CU6: Autorizar Publicación
        WorkPublicationAuthorized::class => [
            SendPublicationAuthorizedNotification::class,
        ],

        // CU7: Revisar y Tramitar Trabajo - Coordinador
        WorkApprovedByCoordinator::class => [
            SendWorkApprovedByCoordinatorNotification::class,
        ],
        WorkChangesRequestedByCoordinator::class => [
            SendWorkChangesRequestedByCoordinatorNotification::class,
        ],
        WorkRejectedByCoordinator::class => [
            SendWorkRejectedByCoordinatorNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool {
        return false;
    }
}
