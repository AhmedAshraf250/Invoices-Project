<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authorized user can create a role and attach permissions', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['roles.view', 'roles.create']);

    $this->actingAs($admin)
        ->get(route('roles.index'))
        ->assertOk()
        ->assertSee('las la-search', false);

    $permissions = Permission::query()
        ->whereIn('name', ['users.view', 'users.create'])
        ->pluck('id')
        ->all();

    $response = $this->actingAs($admin)->post(route('roles.store'), [
        'name' => 'support',
        'display_name_ar' => 'الدعم',
        'display_name_en' => 'Support',
        'permissions' => $permissions,
    ]);

    $response->assertRedirect(route('roles.index'));

    $role = Role::query()->where('name', 'support')->first();

    expect($role)->not->toBeNull()
        ->and($role?->permissions->pluck('name')->sort()->values()->all())->toBe(['users.create', 'users.view'])
        ->and($role?->getTranslation('display_name', 'ar'))->toBe('الدعم')
        ->and($role?->getTranslation('display_name', 'en'))->toBe('Support');

    $this->actingAs($admin)
        ->get(route('roles.show', $role))
        ->assertOk()
        ->assertSeeText(__('roles.actions.back'))
        ->assertSeeText(__('roles.details.quick_actions'))
        ->assertSee('slide-item active', false);
});

test('authorized user can update a role with permission ids from the form', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['roles.view', 'roles.update']);

    $role = Role::query()->create([
        'name' => 'operations',
        'guard_name' => 'web',
        'display_name' => [
            'ar' => 'العمليات',
            'en' => 'Operations',
        ],
    ]);

    $initialPermission = Permission::query()->where('name', 'users.view')->firstOrFail();
    $updatedPermissions = Permission::query()
        ->whereIn('name', ['users.create', 'users.update'])
        ->pluck('id')
        ->map(fn ($id) => (string) $id)
        ->all();

    $role->givePermissionTo($initialPermission);

    $response = $this->actingAs($admin)->patch(route('roles.update', $role), [
        'name' => 'operations-team',
        'display_name_ar' => 'فريق العمليات',
        'display_name_en' => 'Operations Team',
        'permissions' => $updatedPermissions,
    ]);

    $response->assertRedirect(route('roles.index'));

    $role->refresh();

    expect($role->name)->toBe('operations-team')
        ->and($role->getTranslation('display_name', 'ar'))->toBe('فريق العمليات')
        ->and($role->getTranslation('display_name', 'en'))->toBe('Operations Team')
        ->and($role->permissions->pluck('name')->sort()->values()->all())->toBe(['users.create', 'users.update']);
});

test('protected full-permission role cannot be edited or deleted from the UI', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['roles.view', 'roles.update', 'roles.delete']);

    $protectedRole = Role::query()->where('name', 'super-admin')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('roles.show', $protectedRole))
        ->assertOk()
        ->assertDontSee('data-target="#editRoleModal"', false)
        ->assertDontSee('data-target="#deleteRoleModal"', false)
        ->assertDontSee('id="editRoleModal"', false)
        ->assertDontSee('id="deleteRoleModal"', false);

    $this->actingAs($admin)
        ->patch(route('roles.update', $protectedRole), [
            'name' => 'super-admin-updated',
            'display_name_ar' => 'مالك النظام',
            'display_name_en' => 'System Owner',
            'permissions' => $protectedRole->permissions->pluck('id')->all(),
        ])
        ->assertForbidden();
});
