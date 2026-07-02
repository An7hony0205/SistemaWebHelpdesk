<?php

namespace Tests\Feature;

use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use App\Domains\Notifications\Jobs\ProcessNotificationJob;
use App\Domains\Support\Ticket;
use App\Events\TicketCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_created_event_dispatches_notification_job()
    {
        Queue::fake();

        $tenant = Tenant::create(['name' => 'Test', 'domain' => 'test']);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $ticket = new Ticket;
        $ticket->id = 1;
        $ticket->tenant_id = $tenant->id;
        $ticket->user_id = $user->id;
        $ticket->title = 'Test Ticket';
        // We set relations manually to avoid hitting DB for the test if possible,
        // or just let it query since we used RefreshDatabase.
        // But since it's a new instance, we must associate it properly if the subscriber uses relations.
        $ticket->setRelation('user', $user);

        // Disparamos el evento directamente
        Event::dispatch(new TicketCreated($ticket));

        Queue::assertPushed(ProcessNotificationJob::class, function ($job) use ($user) {
            return $job->recipientId === $user->id
                && $job->eventName === 'TicketCreated'
                && $job->channelName === 'email';
        });
    }
}
