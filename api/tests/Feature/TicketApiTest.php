<?php

namespace Tests\Feature;

use Tests\TestCase;

class TicketApiTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_tickets(): void
    {
        $response = $this->getJson('/api/tickets');

        $response->assertStatus(401);
    }
}
