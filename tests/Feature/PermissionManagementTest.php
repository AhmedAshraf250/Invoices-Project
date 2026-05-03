<?php

use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authorized user can view permissions page', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->givePermissionTo(['permissions.view']);

    $this->actingAs($admin)
        ->get(route('permissions.index'))
        ->assertOk()
        ->assertViewIs('permissions.index')
        ->assertSeeText(__('permissions.preview.heading'))
        ->assertSeeText(PermissionRegistry::groupLabel('users'))
        ->assertSeeText(PermissionRegistry::displayNameFor('users.view'));
});

test('super admin seeder creates owner with full permissions', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(SuperAdminSeeder::class);

    $owner = User::query()->where('email', 'owner@invoices.test')->first();

    expect($owner)->not->toBeNull()
        ->and($owner?->hasRole('super-admin'))->toBeTrue()
        ->and($owner?->can(PermissionRegistry::allNames()[0]))->toBeTrue();
});
