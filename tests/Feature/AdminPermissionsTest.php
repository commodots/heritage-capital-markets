<?php

namespace Tests\Feature;

use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_cannot_access_a_module_missing_from_their_user_type(): void
    {
        $role = AdminRole::create([
            'name' => 'Research Only',
            'permissions' => ['dashboard', 'research'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/pages')
            ->assertForbidden();
    }

    public function test_super_admin_bypasses_module_permissions(): void
    {
        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get('/admin/pages')
            ->assertOk();
    }

    public function test_only_super_admin_can_manage_administrator_users(): void
    {
        $role = AdminRole::create([
            'name' => 'User Manager',
            'permissions' => ['dashboard', 'user_management'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_super_admin_can_assign_roles_from_the_role_assignments_page(): void
    {
        $role = AdminRole::create([
            'name' => 'Content Manager',
            'permissions' => ['dashboard', 'pages'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => null,
        ]);
        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get('/admin/role-assignments')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/RoleAssignments/Index')
                ->where('users.0.id', $admin->id)
                ->has('roles'));

        $this->actingAs($superAdmin)
            ->put("/admin/role-assignments/{$admin->id}", [
                'admin_role_id' => $role->id,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'admin_role_id' => $role->id,
        ]);
    }

    public function test_non_super_admin_cannot_access_role_assignments(): void
    {
        $role = AdminRole::create([
            'name' => 'User Manager',
            'permissions' => ['dashboard', 'user_management'],
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/role-assignments')
            ->assertForbidden();
    }

    public function test_research_administrator_can_manage_research_and_news_but_not_other_modules(): void
    {
        $role = AdminRole::query()->where('name', 'Research Administrator')->firstOrFail();
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($admin)->get('/admin/research-reports')->assertOk();
        $this->actingAs($admin)->get('/admin/articles')->assertOk();
        $this->actingAs($admin)->get('/admin/homepage/hero-slides')->assertForbidden();
    }

    public function test_super_admin_can_reactivate_a_deactivated_administrator(): void
    {
        $role = AdminRole::create([
            'name' => 'Content',
            'permissions' => ['dashboard', 'pages'],
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);
        $inactiveAdmin = User::factory()->create([
            'is_admin' => false,
            'admin_role_id' => $role->id,
        ]);

        $this->actingAs($superAdmin)
            ->get('/admin/users')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/AdminUsers/Index')
                ->has('users', 2));

        $this->from('/admin/users')
            ->actingAs($superAdmin)
            ->post("/admin/users/{$inactiveAdmin->id}/toggle")
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'id' => $inactiveAdmin->id,
            'is_admin' => true,
        ]);
    }
}
