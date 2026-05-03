<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authorized user can create a user with multiple roles and status', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['users.view', 'users.create', 'organizations.view', 'products.view']);

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('fe fe-home', false)
        ->assertSee('fe fe-file-text', false)
        ->assertSee('fe fe-pie-chart', false)
        ->assertSee('fe fe-users', false)
        ->assertSee('mdi mdi-account-multiple', false)
        ->assertSee('fe fe-settings', false)
        ->assertSeeText(__('common.sidebar.customers'))
        ->assertSeeText(__('common.sidebar.settings'));

    $accountantRole = Role::query()->where('name', 'accountant')->firstOrFail();
    $viewerRole = Role::query()->where('name', 'viewer')->firstOrFail();

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Ahmed Ali',
        'email' => 'ahmed@example.com',
        'password' => 'password123',
        'status' => User::STATUS_INACTIVE,
        'roles' => [$accountantRole->id, $viewerRole->id],
    ]);

    $response->assertRedirect(route('users.index'));

    $user = User::query()->where('email', 'ahmed@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user?->status)->toBe(User::STATUS_INACTIVE)
        ->and($user?->roles->pluck('name')->sort()->values()->all())->toBe(['accountant', 'viewer']);

    $this->actingAs($admin)
        ->get(route('users.show', $user))
        ->assertOk();
});

test('authorized user can update user roles from form string values', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['users.update']);

    $user = User::factory()->create([
        'status' => User::STATUS_ACTIVE,
    ]);

    $accountantRole = Role::query()->where('name', 'accountant')->firstOrFail();
    $viewerRole = Role::query()->where('name', 'viewer')->firstOrFail();
    $user->assignRole($viewerRole);

    $response = $this->actingAs($admin)->patch(route('users.update', $user), [
        'name' => 'Ahmed Updated',
        'email' => 'updated@example.com',
        'password' => '',
        'status' => User::STATUS_SUSPENDED,
        'roles' => [(string) $accountantRole->id, (string) $viewerRole->id],
    ]);

    $response->assertRedirect(route('users.index'));

    $user->refresh();

    expect($user->name)->toBe('Ahmed Updated')
        ->and($user->email)->toBe('updated@example.com')
        ->and($user->status)->toBe(User::STATUS_SUSPENDED)
        ->and($user->roles->pluck('name')->sort()->values()->all())->toBe(['accountant', 'viewer']);
});

test('user details page shows back and quick edit actions', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['users.view', 'users.update']);

    $user = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.show', $user))
        ->assertOk()
        ->assertSeeText(__('users.actions.back'))
        ->assertSeeText(__('users.actions.quick_edit'));
});

test('protected user status cannot be changed', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['users.update']);

    $protectedUser = User::factory()->create([
        'status' => User::STATUS_ACTIVE,
    ]);
    $protectedRole = Role::query()->where('name', 'super-admin')->firstOrFail();
    $protectedUser->assignRole($protectedRole);

    $response = $this->actingAs($admin)->patch(route('users.update', $protectedUser), [
        'name' => $protectedUser->name,
        'email' => $protectedUser->email,
        'password' => '',
        'status' => User::STATUS_SUSPENDED,
        'roles' => [$protectedRole->id],
        'return_to_show' => 1,
    ]);

    $response->assertRedirect(route('users.show', $protectedUser))
        ->assertSessionHas('error', __('users.messages.protected_status_locked'));

    expect($protectedUser->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

test('inactive user cannot sign in', function () {
    $user = User::factory()->create([
        'email' => 'inactive@example.com',
        'password' => 'password',
        'status' => User::STATUS_INACTIVE,
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => __('auth.inactive'),
    ]);

    $this->assertGuest();
});
