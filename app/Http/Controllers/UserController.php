<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $statusFilter = $request->string('status')->toString();
        $roleFilter = $request->string('role')->toString();

        $users = User::query()
            ->with('roles:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter))
            ->when($roleFilter !== '', fn ($query) => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $roleFilter)))
            ->latest('id')
            ->get();

        $availableRoles = Role::query()->orderBy('name')->get();

        return view('users.index', [
            'users' => $users,
            'availableRoles' => $availableRoles,
            'statusOptions' => User::statusOptions(),
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'role' => $roleFilter,
            ],
            'summary' => [
                'total' => User::query()->count(),
                'active' => User::query()->where('status', User::STATUS_ACTIVE)->count(),
                'inactive' => User::query()->where('status', User::STATUS_INACTIVE)->count(),
                'suspended' => User::query()->where('status', User::STATUS_SUSPENDED)->count(),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => $validated['status'],
        ]);

        $user->syncRoles($this->selectedRoles($validated['roles']));

        return to_route('users.index')
            ->with('success', __('users.messages.created'));
    }

    public function show(User $user): View
    {
        $user->load(['roles.permissions', 'permissions']);

        return view('users.show', [
            'user' => $user,
            'availableRoles' => Role::query()->orderBy('name')->get(),
            'statusOptions' => User::statusOptions(),
            'permissionGroups' => PermissionRegistry::grouped($user->getAllPermissions()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->isProtected() && $validated['status'] !== $user->status) {
            return $this->userUpdateRedirect($request, $user)
                ->with('error', __('users.messages.protected_status_locked'));
        }

        if (
            $user->hasRole('super-admin')
            && User::role('super-admin')->count() === 1
            && ! Role::query()->whereIn('id', $validated['roles'])->where('name', 'super-admin')->exists()
        ) {
            return $this->userUpdateRedirect($request, $user)
                ->with('error', __('users.messages.last_super_admin_protected'));
        }

        if (blank($validated['password'])) {
            unset($validated['password']);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            ...array_key_exists('password', $validated) ? ['password' => $validated['password']] : [],
        ]);

        $user->syncRoles($this->selectedRoles($validated['roles']));

        return $this->userUpdateRedirect($request, $user)
            ->with('success', __('users.messages.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return to_route('users.index')
                ->with('error', __('users.messages.cannot_delete_self'));
        }

        if ($user->hasRole('super-admin') && User::role('super-admin')->count() === 1) {
            return to_route('users.index')
                ->with('error', __('users.messages.last_super_admin_protected'));
        }

        $user->delete();

        return to_route('users.index')
            ->with('success', __('users.messages.deleted'));
    }

    /**
     * @param  array<int, int|string>  $roleIds
     */
    private function selectedRoles(array $roleIds): Collection
    {
        return Role::query()
            ->whereIn('id', $roleIds)
            ->get();
    }

    private function userUpdateRedirect(Request $request, User $user): RedirectResponse
    {
        if ($request->boolean('return_to_show')) {
            return to_route('users.show', $user);
        }

        return to_route('users.index');
    }
}
