<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Domains\Identity\Tenant;
use App\Domains\Support\Ticket;
use App\Domains\Identity\User;
use App\Domains\Support\Category;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_scope_isolates_data(): void
    {
        $tenant1 = Tenant::create(['name' => 'Tenant 1']);
        $tenant2 = Tenant::create(['name' => 'Tenant 2']);

        $user1 = User::factory()->create(['tenant_id' => $tenant1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id]);

        $this->actingAs($user1);
        Category::create(['name' => 'Cat 1', 'description' => 'Test']);

        $this->actingAs($user2);
        Category::create(['name' => 'Cat 2', 'description' => 'Test']);

        // Estando como user 2, solo deberia ver la cat 2
        $this->assertEquals(1, Category::count());
        $this->assertEquals('Cat 2', Category::first()->name);
    }
}
