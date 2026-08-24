<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileBackNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_panel_pages_include_the_mobile_back_navigation(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo('dashboard.view');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('data-campaign-mobile-back', false)
            ->assertSee('9dot-campaign-navigation-stack', false)
            ->assertSee('Go back to previous page');
    }
}
