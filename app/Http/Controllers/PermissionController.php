<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $groupedPermissions = $this->groupedPermissions();

        return view('permissions.index', [
            'groupedPermissions' => $groupedPermissions,
            'summary' => [
                'groups_count' => count($groupedPermissions),
                'permissions_count' => collect($groupedPermissions)->sum(
                    fn (array $group): int => count($group['permissions'])
                ),
            ],
        ]);
    }

    /**
     * @return array<int, array{key: string, label: string, permissions: Collection<int, Permission>}>
     */
    private function groupedPermissions(): array
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('name')
            ->get();

        return PermissionRegistry::grouped($permissions);
    }
}
