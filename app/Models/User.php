<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_SUSPENDED = 'suspended';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => __('users.status.active'),
            self::STATUS_INACTIVE => __('users.status.inactive'),
            self::STATUS_SUSPENDED => __('users.status.suspended'),
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'secondary',
            self::STATUS_SUSPENDED => 'danger',
            default => 'light',
        };
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

        return $assignedPermissionNames->all() === $systemPermissionNames->all();
    }

    private function assignedPermissionNames(): Collection
    {
        return $this->getAllPermissions()
            ->pluck('name')
            ->unique()
            ->sort()
            ->values();
    }
}
