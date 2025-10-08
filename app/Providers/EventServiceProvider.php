<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\WorkSubmitted;
use App\Events\WorkPublicationAuthorized;
use App\Listeners\SendWorkSubmittedNotification;
use App\Listeners\SendPublicationAuthorizedNotification;

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
        WorkSubmitted::class => [
            SendWorkSubmittedNotification::class,
        ],
        WorkPublicationAuthorized::class => [
            SendPublicationAuthorizedNotification::class,
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
