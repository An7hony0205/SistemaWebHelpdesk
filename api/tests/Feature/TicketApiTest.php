<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Domains\Identity\User;
use App\Domains\Identity\Tenant;

class TicketApiTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_tickets(): void
    {
        $response = $this->getJson('/api/tickets');

        $response->assertStatus(401);
    }
}
