<?php

namespace App\Models;

use App\Support\Permissions\PermissionRegistry;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $fillable = [
        'name',
        'guard_name',
    ];

    public function getResolvedDisplayNameAttribute(): string
    {
        return PermissionRegistry::displayNameFor($this->name);
    }

    public function getResolvedGroupNameAttribute(): string
    {
        return PermissionRegistry::groupNameFor($this->name);
    }
}
