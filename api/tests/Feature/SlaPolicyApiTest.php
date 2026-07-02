<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domains\Identity\User;
use App\Domains\Identity\Tenant;
use App\Domains\Administration\Priority;

class SlaPolicyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_sla_policy()
    {
        $tenant = Tenant::create(['name' => 'Test Tenant', 'domain' => 'test']);
        
        // Admin
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        $user->assignRole('Admin');

        $priority = Priority::forceCreate(['name' => 'High', 'level' => 1]);

        $response = $this->actingAs($user)->postJson('/api/sla-policies', [
            'name' => 'Default SLA',
            'is_active' => true,
            'targets' => [
                [
                    'priority_id' => $priority->id,
                    'first_response_time_minutes' => 60,
                    'resolution_time_minutes' => 240,
                ]
            ]
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('name', 'Default SLA')
                 ->assertJsonCount(1, 'targets');
                 
        $this->assertDatabaseHas('sla_policies', [
            'name' => 'Default SLA',
            'tenant_id' => $tenant->id,
        ]);
        
        $this->assertDatabaseHas('sla_targets', [
            'first_response_time_minutes' => 60,
            'resolution_time_minutes' => 240,
        ]);
    }
}
