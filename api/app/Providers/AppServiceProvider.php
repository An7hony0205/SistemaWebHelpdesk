<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\TicketRepositoryInterface::class,
            \App\Repositories\TicketRepository::class
        );
        $this->app->bind(
            \App\Domains\Preferences\Repositories\UserPreferenceRepositoryInterface::class,
            \App\Domains\Preferences\Repositories\EloquentUserPreferenceRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar Eventos del Dominio SLA
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\TicketCreated::class,
            \App\Domains\Sla\Listeners\TicketCreatedListener::class,
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\TicketCommentAdded::class,
            \App\Domains\Sla\Listeners\TicketCommentAddedListener::class,
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\TicketStatusUpdated::class,
            \App\Domains\Sla\Listeners\TicketStatusChangedListener::class,
        );

        // Registrar Event Subscriber de Notificaciones
        \Illuminate\Support\Facades\Event::subscribe(
            \App\Domains\Notifications\Listeners\NotificationEventSubscriber::class
        );

        // Registrar Event Subscriber de Automatizaciones
        \Illuminate\Support\Facades\Event::subscribe(
            \App\Domains\Automations\Engine\AutomationEventSubscriber::class
        );

        // Registrar Event Subscriber de Integraciones
        \Illuminate\Support\Facades\Event::subscribe(
            \App\Domains\Integrations\Engine\IntegrationEventSubscriber::class
        );

        // Registrar Event Subscriber de Inteligencia Artificial
        \Illuminate\Support\Facades\Event::subscribe(
            \App\Domains\Intelligence\Engine\IntelligenceEventSubscriber::class
        );

        // Registrar KPI Providers en DashboardService
        $this->app->singleton(\App\Domains\Dashboard\Services\DashboardService::class, function ($app) {
            $service = new \App\Domains\Dashboard\Services\DashboardService();
            $service->registerProvider(new \App\Domains\Dashboard\Providers\TicketsCountKpi());
            $service->registerProvider(new \App\Domains\Dashboard\Providers\SlaComplianceKpi());
            $service->registerProvider(new \App\Domains\Dashboard\Providers\TicketsByStatusKpi());
            return $service;
        });
    }
}
