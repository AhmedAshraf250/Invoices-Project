<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions:id,name')
            ->withCount(['users', 'permissions'])
            ->latest('id')
            ->get();

        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        return view('roles.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'groupedPermissions' => PermissionRegistry::grouped($permissions),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::query()->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'display_name' => [
                'ar' => $validated['display_name_ar'],
                'en' => $validated['display_name_en'],
            ],
        ]);

        $role->syncPermissions($this->selectedPermissions($validated['permissions'] ?? []));

        return to_route('roles.index')
            ->with('success', __('roles.messages.created'));
    }

    public function show(Role $role): View
    {
        $role->load(['permissions', 'users:id,name,email,status']);
        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        // dd($role->permissions);
        return view('roles.show', [
            'role' => $role,
            'permissionGroups' => PermissionRegistry::grouped($role->permissions),
            'groupedPermissions' => PermissionRegistry::grouped($permissions),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
            'display_name' => [
                'ar' => $validated['display_name_ar'],
                'en' => $validated['display_name_en'],
            ],
        ]);

        $role->syncPermissions($this->selectedPermissions($validated['permissions'] ?? []));

        return to_route('roles.index')
            ->with('success', __('roles.messages.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        if ($role->users()->exists()) {
            return to_route('roles.index')
                ->with('error', __('roles.messages.cannot_delete_assigned_role'));
        }

        $role->delete();

        return to_route('roles.index')
            ->with('success', __('roles.messages.deleted'));
    }

    /**
     * @param  array<int, int|string>  $permissionIds
     */
    private function selectedPermissions(array $permissionIds): Collection
    {
        return Permission::query()
            ->whereIn('id', $permissionIds)
            ->get();
    }
}
