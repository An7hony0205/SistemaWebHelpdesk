<?php

namespace App\Providers;

use App\Domains\Automations\Engine\AutomationEventSubscriber;
use App\Domains\Dashboard\Providers\SlaComplianceKpi;
use App\Domains\Dashboard\Providers\TicketsByStatusKpi;
use App\Domains\Dashboard\Providers\TicketsCountKpi;
use App\Domains\Dashboard\Services\DashboardService;
use App\Domains\Integrations\Engine\IntegrationEventSubscriber;
use App\Domains\Intelligence\Engine\IntelligenceEventSubscriber;
use App\Domains\Notifications\Listeners\NotificationEventSubscriber;
use App\Domains\Preferences\Repositories\EloquentUserPreferenceRepository;
use App\Domains\Preferences\Repositories\UserPreferenceRepositoryInterface;
use App\Domains\Sla\Listeners\TicketCommentAddedListener;
use App\Domains\Sla\Listeners\TicketCreatedListener;
use App\Domains\Sla\Listeners\TicketStatusChangedListener;
use App\Events\TicketCommentAdded;
use App\Events\TicketCreated;
use App\Events\TicketStatusUpdated;
use App\Repositories\TicketRepository;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TicketRepositoryInterface::class,
            TicketRepository::class
        );
        $this->app->bind(
            UserPreferenceRepositoryInterface::class,
            EloquentUserPreferenceRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar Eventos del Dominio SLA
        Event::listen(
            TicketCreated::class,
            TicketCreatedListener::class,
        );
        Event::listen(
            TicketCommentAdded::class,
            TicketCommentAddedListener::class,
        );
        Event::listen(
            TicketStatusUpdated::class,
            TicketStatusChangedListener::class,
        );

        // Registrar Event Subscriber de Notificaciones
        Event::subscribe(
            NotificationEventSubscriber::class
        );

        // Registrar Event Subscriber de Automatizaciones
        Event::subscribe(
            AutomationEventSubscriber::class
        );

        // Registrar Event Subscriber de Integraciones
        Event::subscribe(
            IntegrationEventSubscriber::class
        );

        // Registrar Event Subscriber de Inteligencia Artificial
        Event::subscribe(
            IntelligenceEventSubscriber::class
        );

        // Registrar KPI Providers en DashboardService
        $this->app->singleton(DashboardService::class, function ($app) {
            $service = new DashboardService;
            $service->registerProvider(new TicketsCountKpi);
            $service->registerProvider(new SlaComplianceKpi);
            $service->registerProvider(new TicketsByStatusKpi);

            return $service;
        });
    }
}
