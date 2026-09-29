<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_only_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('kasir.dashboard'))->assertForbidden();
    }

    public function test_cashier_can_only_access_cashier_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)->get(route('kasir.dashboard'))->assertOk();
        $this->actingAs($cashier)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_role_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
}