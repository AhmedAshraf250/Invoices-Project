<?php

namespace App\Support\Permissions;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PermissionRegistry
{
    public static function definitions(): array
    {
        return [
            ['name' => 'invoices.view', 'group_name' => 'invoices'],
            ['name' => 'invoices.list', 'group_name' => 'invoices'],
            ['name' => 'invoices.paid', 'group_name' => 'invoices'],
            ['name' => 'invoices.partial', 'group_name' => 'invoices'],
            ['name' => 'invoices.unpaid', 'group_name' => 'invoices'],
            ['name' => 'invoices.archived', 'group_name' => 'invoices'],
            ['name' => 'invoices.create', 'group_name' => 'invoices'],
            ['name' => 'invoices.archive', 'group_name' => 'invoices'],
            ['name' => 'invoices.delete', 'group_name' => 'invoices'],
            ['name' => 'invoices.export', 'group_name' => 'invoices'],
            ['name' => 'invoices.change-status', 'group_name' => 'invoices'],
            ['name' => 'invoices.update', 'group_name' => 'invoices'],
            ['name' => 'invoices.restore', 'group_name' => 'invoices'],
            ['name' => 'invoice-attachments.create', 'group_name' => 'invoices'],
            ['name' => 'invoice-attachments.delete', 'group_name' => 'invoices'],
            ['name' => 'reports.view', 'group_name' => 'reports'],
            ['name' => 'reports.invoices', 'group_name' => 'reports'],
            ['name' => 'reports.customers', 'group_name' => 'reports'],
            ['name' => 'users.view', 'group_name' => 'users'],
            ['name' => 'users.list', 'group_name' => 'users'],
            ['name' => 'users.create', 'group_name' => 'users'],
            ['name' => 'users.update', 'group_name' => 'users'],
            ['name' => 'users.delete', 'group_name' => 'users'],
            ['name' => 'roles.view', 'group_name' => 'roles'],
            ['name' => 'roles.create', 'group_name' => 'roles'],
            ['name' => 'roles.update', 'group_name' => 'roles'],
            ['name' => 'roles.delete', 'group_name' => 'roles'],
            ['name' => 'permissions.view', 'group_name' => 'permissions'],
            ['name' => 'settings.view', 'group_name' => 'settings'],
            ['name' => 'products.view', 'group_name' => 'products'],
            ['name' => 'products.create', 'group_name' => 'products'],
            ['name' => 'products.update', 'group_name' => 'products'],
            ['name' => 'products.delete', 'group_name' => 'products'],
            ['name' => 'organizations.view', 'group_name' => 'organizations'],
            ['name' => 'organizations.create', 'group_name' => 'organizations'],
            ['name' => 'organizations.update', 'group_name' => 'organizations'],
            ['name' => 'organizations.delete', 'group_name' => 'organizations'],
        ];
    }

    public static function allNames(): array
    {
        return array_column(self::definitions(), 'name');
    }

    public static function defaultRoles(): array
    {
        return [
            [
                'name' => 'super-admin',
                'display_name' => ['ar' => 'المالك', 'en' => 'Owner'],
                'permissions' => self::allNames(),
            ],
            [
                'name' => 'admin',
                'display_name' => ['ar' => 'مدير النظام', 'en' => 'System Admin'],
                'permissions' => [
                    'invoices.view',
                    'invoices.list',
                    'invoices.paid',
                    'invoices.partial',
                    'invoices.unpaid',
                    'invoices.archived',
                    'invoices.create',
                    'invoices.archive',
                    'invoices.delete',
                    'invoices.export',
                    'invoices.change-status',
                    'invoices.update',
                    'invoices.restore',
                    'invoice-attachments.create',
                    'invoice-attachments.delete',
                    'reports.view',
                    'reports.invoices',
                    'reports.customers',
                    'users.view',
                    'users.list',
                    'users.create',
                    'users.update',
                    'users.delete',
                    'roles.view',
                    'roles.create',
                    'roles.update',
                    'roles.delete',
                    'permissions.view',
                    'settings.view',
                    'products.view',
                    'products.create',
                    'products.update',
                    'products.delete',
                    'organizations.view',
                    'organizations.create',
                    'organizations.update',
                    'organizations.delete',
                ],
            ],
            [
                'name' => 'accountant',
                'display_name' => ['ar' => 'محاسب', 'en' => 'Accountant'],
                'permissions' => [
                    'invoices.view',
                    'invoices.list',
                    'invoices.paid',
                    'invoices.partial',
                    'invoices.unpaid',
                    'invoices.archived',
                    'invoices.create',
                    'invoices.export',
                    'invoices.change-status',
                    'invoices.update',
                    'invoices.restore',
                    'invoice-attachments.create',
                    'invoice-attachments.delete',
                    'reports.view',
                    'reports.invoices',
                    'reports.customers',
                    'products.view',
                    'organizations.view',
                ],
            ],
            [
                'name' => 'sales',
                'display_name' => ['ar' => 'مبيعات', 'en' => 'Sales'],
                'permissions' => [
                    'invoices.view',
                    'invoices.list',
                    'invoices.paid',
                    'invoices.partial',
                    'invoices.unpaid',
                    'invoices.create',
                    'invoices.update',
                    'invoice-attachments.create',
                    'products.view',
                    'organizations.view',
                ],
            ],
            [
                'name' => 'viewer',
                'display_name' => ['ar' => 'مستعرض', 'en' => 'Viewer'],
                'permissions' => [
                    'invoices.view',
                    'invoices.list',
                    'invoices.paid',
                    'invoices.partial',
                    'invoices.unpaid',
                    'invoices.archived',
                    'reports.view',
                    'reports.invoices',
                    'reports.customers',
                    'products.view',
                    'organizations.view',
                ],
            ],
        ];
    }

    public static function displayNameFor(string $permissionName): string
    {
        $translationKey = 'permissions.items.'.self::translationKeyFor($permissionName);
        $translation = __($translationKey);

        if ($translation !== $translationKey) {
            return $translation;
        }

        return Str::headline(str_replace(['.', '-'], ' ', $permissionName));
    }

    public static function groupNameFor(string $permissionName): string
    {
        return self::definitionMap()[$permissionName]['group_name'] ?? 'other';
    }

    public static function groupLabel(string $groupName): string
    {
        return __('permissions.groups.'.$groupName);
    }

    public static function grouped(Collection|array $permissions): array
    {
        return collect($permissions)
            ->groupBy(fn (Permission $permission) => $permission->resolved_group_name)
            ->map(function (Collection $groupedPermissions, string $groupName): array {
                return [
                    'label' => self::groupLabel($groupName),
                    'permissions' => $groupedPermissions->sortBy(fn (Permission $permission) => $permission->resolved_display_name)->values(),
                ];
            })
            ->sortBy('label')
            ->all();
    }

    public static function groupOptions(): array
    {
        return collect(self::definitions())
            ->pluck('group_name')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    protected static function definitionMap(): array
    {
        $map = collect(self::definitions())
            ->mapWithKeys(fn (array $definition) => [
                $definition['name'] => [
                    'group_name' => $definition['group_name'],
                ],
            ])
            ->all();

        return $map;
    }

    protected static function translationKeyFor(string $permissionName): string
    {
        return str_replace(['.', '-'], '_', $permissionName);
    }
}
