<?php

namespace App\Domains\Notifications\Listeners;

use Illuminate\Events\Dispatcher;
use App\Events\TicketCreated;
use App\Events\TicketAssigned;
use App\Events\TicketResolved;
use App\Domains\Notifications\Jobs\ProcessNotificationJob;
use App\Domains\Notifications\Models\NotificationPreference;
use App\Domains\Identity\User;
use Illuminate\Support\Facades\Log;

class NotificationEventSubscriber
{
    public function handleTicketCreated(TicketCreated $event): void
    {
        $payload = [
            'ticket_id' => $event->ticket->id,
            'ticket_title' => $event->ticket->title,
            'user_name' => $event->ticket->user->name ?? 'Usuario',
        ];

        // Notify admins or specific groups? Let's notify the ticket owner for simplicity in MVP
        // OR we notify anyone with 'TicketCreated' preference.
        $this->dispatchNotifications($event->ticket->tenant_id, 'TicketCreated', $payload, [$event->ticket->user_id]);
    }

    public function handleTicketAssigned(TicketAssigned $event): void
    {
        $payload = [
            'ticket_id' => $event->ticket->id,
            'ticket_title' => $event->ticket->title,
            'assigned_to' => $event->ticket->assignedUser->name ?? 'N/A',
        ];

        $this->dispatchNotifications($event->ticket->tenant_id, 'TicketAssigned', $payload, [$event->ticket->assigned_to]);
    }

    public function handleTicketResolved(TicketResolved $event): void
    {
        $payload = [
            'ticket_id' => $event->ticket->id,
            'ticket_title' => $event->ticket->title,
        ];

        $this->dispatchNotifications($event->ticket->tenant_id, 'TicketResolved', $payload, [$event->ticket->user_id]);
    }

    /**
     * Dispatch notification jobs based on preferences
     */
    private function dispatchNotifications(int $tenantId, string $eventName, array $payload, array $potentialRecipients)
    {
        foreach ($potentialRecipients as $userId) {
            if (!$userId) continue;

            // Ver preferencias del usuario
            $pref = NotificationPreference::where('configurable_type', User::class)
                ->where('configurable_id', $userId)
                ->where('event_name', $eventName)
                ->first();

            // Si no tiene preferencia explícita, podríamos asumir un default (ej. email) 
            // Para el MVP asumiremos default = email
            $channels = $pref ? $pref->channels : ['email'];

            foreach ($channels as $channelName) {
                ProcessNotificationJob::dispatch(
                    $tenantId,
                    $eventName,
                    $payload,
                    $userId,
                    $channelName
                );
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            TicketCreated::class => 'handleTicketCreated',
            TicketAssigned::class => 'handleTicketAssigned',
            TicketResolved::class => 'handleTicketResolved',
        ];
    }
}
