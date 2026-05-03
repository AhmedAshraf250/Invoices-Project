<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as BaseRole;
use Spatie\Translatable\HasTranslations;

class Role extends BaseRole
{
    use HasTranslations;

    public array $translatable = ['display_name'];

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
    ];

    protected function casts(): array
    {
        return [
            'display_name' => 'array',
        ];
    }

    public function resolvedDisplayName(): string
    {
        return $this->getTranslation('display_name', app()->getLocale(), false)
            ?: $this->getTranslation('display_name', config('app.fallback_locale'), false)
            ?: $this->name;
    }

    public function isProtected(): bool
    {
        $assignedPermissionNames = $this->assignedPermissionNames();

        if ($assignedPermissionNames->isEmpty()) {
            return false;
        }

        $systemPermissionNames = Permission::query()
            ->pluck('name')
            ->sort()
            ->values();

        return $assignedPermissionNames->values()->all() === $systemPermissionNames->all();
    }

    private function assignedPermissionNames(): Collection
    {
        $permissions = $this->relationLoaded('permissions')
            ? $this->permissions
            : $this->permissions()->get(['name']);

        return $permissions->pluck('name')
            ->sort()
            ->values();
    }
}
