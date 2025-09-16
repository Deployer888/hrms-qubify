<?php

namespace App;

use App\Services\RoleDetectionService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getpermissionGroups()
    {
        $permission_groups = DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')
            ->get();
        return $permission_groups;
    }

    public static function getpermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }

    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
        }
        return $hasPermission;
    }

    /**
     * Get the dashboard type for this user
     *
     * @return string
     */
    public function getDashboardType(): string
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->getDashboardType($this);
    }

    /**
     * Get the user's role
     *
     * @return string
     */
    public function getUserRole(): string
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->getUserRole($this);
    }

    /**
     * Get permitted widgets for this user
     *
     * @return array
     */
    public function getPermittedWidgets(): array
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->getPermittedWidgets($this);
    }

    /**
     * Check if user can access a specific widget
     *
     * @param string $widgetType
     * @return bool
     */
    public function canAccessWidget(string $widgetType): bool
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->canAccessWidget($this, $widgetType);
    }

    /**
     * Check if user can access a specific dashboard type
     *
     * @param string $dashboardType
     * @return bool
     */
    public function canAccessDashboard(string $dashboardType): bool
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->isDashboardAccessible($this, $dashboardType);
    }

    /**
     * Check if user has HR privileges
     *
     * @return bool
     */
    public function isHRUser(): bool
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->isHRUser($this);
    }

    /**
     * Check if user has company privileges
     *
     * @return bool
     */
    public function isCompanyUser(): bool
    {
        $roleService = app(RoleDetectionService::class);
        return $roleService->isCompanyUser($this);
    }

    /**
     * Clear role cache for this user
     *
     * @return void
     */
    public function clearRoleCache(): void
    {
        $roleService = app(RoleDetectionService::class);
        $roleService->clearRoleCache($this);
    }
}
