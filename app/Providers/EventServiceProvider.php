<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\WorkSubmitted;
use App\Events\WorkPublicationAuthorized;
use App\Events\WorkApprovedByCoordinator;
use App\Events\WorkChangesRequestedByCoordinator;
use App\Events\WorkRejectedByCoordinator;
use App\Events\WorkRejectedByDeanDirector;
use App\Events\WorkReceivedInViex;
use App\Events\EvaluatorAssigned;
use App\Events\EvaluationSubmitted;
use App\Events\WorkApprovedByViex;
use App\Events\WorkRejectedByViex;
use App\Events\WorkChangesRequestedByViex;
use App\Events\WorkApprovedByDeanDirector;
use App\Events\WorkChangesRequestedByDeanDirector;
use App\Listeners\SendWorkSubmittedNotification;
use App\Listeners\SendPublicationAuthorizedNotification;
use App\Listeners\SendWorkApprovedByCoordinatorNotification;
use App\Listeners\SendWorkChangesRequestedByCoordinatorNotification;
use App\Listeners\SendWorkRejectedByCoordinatorNotification;
use App\Listeners\SendWorkRejectedByDeanDirectorNotification;
use App\Listeners\SendWorkReceivedInViexNotification;
use App\Listeners\SendEvaluatorAssignedNotification;
use App\Listeners\SendEvaluationSubmittedNotification;
use App\Listeners\SendWorkApprovedByViexNotification;
use App\Listeners\SendWorkRejectedByViexNotification;
use App\Listeners\SendWorkChangesRequestedByViexNotification;
use App\Listeners\SendWorkApprovedByDeanDirectorNotification;
use App\Listeners\SendWorkChangesRequestedByDeanDirectorNotification;

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

        // CU11: Rechazar Trabajo para Subsanación (Decano/Director)
        WorkRejectedByDeanDirector::class => [
            SendWorkRejectedByDeanDirectorNotification::class,
        ],
        WorkApprovedByDeanDirector::class => [
            SendWorkApprovedByDeanDirectorNotification::class,
        ],
        WorkChangesRequestedByDeanDirector::class => [
            SendWorkChangesRequestedByDeanDirectorNotification::class,
        ],

        // CU9: Evaluación VIEX
        WorkReceivedInViex::class => [
            SendWorkReceivedInViexNotification::class,
        ],
        EvaluatorAssigned::class => [
            SendEvaluatorAssignedNotification::class,
        ],
        EvaluationSubmitted::class => [
            SendEvaluationSubmittedNotification::class,
        ],
        WorkApprovedByViex::class => [
            SendWorkApprovedByViexNotification::class,
        ],
        WorkRejectedByViex::class => [
            SendWorkRejectedByViexNotification::class,
        ],
        WorkChangesRequestedByViex::class => [
            SendWorkChangesRequestedByViexNotification::class,
        ],
        WorkCertifiedByViex::class => [
            SendWorkCertifiedByViexNotification::class,
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
