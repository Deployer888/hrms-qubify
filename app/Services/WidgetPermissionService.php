<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class WidgetPermissionService
{
    protected $cachePrefix = 'widget_permissions_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Check if user can access a specific widget
     */
    public function canAccessWidget(User $user, string $widgetType): bool
    {
        $cacheKey = $this->getCacheKey($user->id, $widgetType);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($user, $widgetType) {
            return $this->checkWidgetPermission($user, $widgetType);
        });
    }

    /**
     * Get all accessible widgets for a user
     */
    public function getAccessibleWidgets(User $user): array
    {
        $cacheKey = $this->getCacheKey($user->id, 'all_widgets');
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($user) {
            return $this->getAllAccessibleWidgets($user);
        });
    }

    /**
     * Clear widget permissions cache for a user
     */
    public function clearUserCache(User $user): void
    {
        $pattern = $this->cachePrefix . $user->id . '_*';
        
        // Clear all cached permissions for this user
        $keys = Cache::getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }

    /**
     * Check widget permission without caching
     */
    protected function checkWidgetPermission(User $user, string $widgetType): bool
    {
        // Define widget permissions mapping
        $widgetPermissions = $this->getWidgetPermissions();
        
        if (!isset($widgetPermissions[$widgetType])) {
            return false;
        }

        $requiredPermissions = $widgetPermissions[$widgetType];
        
        // If no permissions required, allow access
        if (empty($requiredPermissions)) {
            return true;
        }

        // Check if user has any of the required permissions
        foreach ($requiredPermissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        // Check role-based access
        return $this->checkRoleBasedAccess($user, $widgetType);
    }

    /**
     * Get all accessible widgets without caching
     */
    protected function getAllAccessibleWidgets(User $user): array
    {
        $widgetPermissions = $this->getWidgetPermissions();
        $accessibleWidgets = [];

        foreach ($widgetPermissions as $widgetType => $permissions) {
            if ($this->checkWidgetPermission($user, $widgetType)) {
                $accessibleWidgets[] = $widgetType;
            }
        }

        return $accessibleWidgets;
    }

    /**
     * Check role-based access for widgets
     */
    protected function checkRoleBasedAccess(User $user, string $widgetType): bool
    {
        $roleBasedWidgets = $this->getRoleBasedWidgets();
        
        if (!isset($roleBasedWidgets[$widgetType])) {
            return false;
        }

        $allowedRoles = $roleBasedWidgets[$widgetType];
        $userType = $user->type ?? 'employee';

        // Check if user type matches allowed roles
        return in_array($userType, $allowedRoles) || in_array('all', $allowedRoles);
    }

    /**
     * Get widget permissions mapping
     */
    protected function getWidgetPermissions(): array
    {
        return [
            // HR Widgets
            'employee_metrics' => ['manage employees', 'view employees'],
            'attendance_gauge' => ['manage attendance', 'view attendance'],
            'leave_management' => ['manage leaves', 'view leaves'],
            'department_performance' => ['manage departments', 'view departments'],
            'hr_activities' => ['view hr activities'],
            'employee_location' => ['view employee locations'],

            // Company Widgets
            'revenue_analytics' => ['view revenue', 'manage finances'],
            'business_growth' => ['view business metrics'],
            'user_acquisition' => ['view user metrics'],
            'plan_performance' => ['view plan metrics'],
            'company_activities' => ['view company activities'],
            'strategic_kpis' => ['view strategic metrics'],

            // Shared Widgets
            'notifications' => [], // Available to all
            'quick_actions' => [], // Available to all
        ];
    }

    /**
     * Get role-based widget access
     */
    protected function getRoleBasedWidgets(): array
    {
        return [
            // HR Widgets
            'employee_metrics' => ['hr', 'admin', 'super admin'],
            'attendance_gauge' => ['hr', 'admin', 'super admin'],
            'leave_management' => ['hr', 'admin', 'super admin'],
            'department_performance' => ['hr', 'admin', 'super admin'],
            'hr_activities' => ['hr', 'admin', 'super admin'],
            'employee_location' => ['hr', 'admin', 'super admin'],

            // Company Widgets
            'revenue_analytics' => ['company', 'admin', 'super admin'],
            'business_growth' => ['company', 'admin', 'super admin'],
            'user_acquisition' => ['company', 'admin', 'super admin'],
            'plan_performance' => ['company', 'admin', 'super admin'],
            'company_activities' => ['company', 'admin', 'super admin'],
            'strategic_kpis' => ['company', 'admin', 'super admin'],

            // Shared Widgets
            'notifications' => ['all'],
            'quick_actions' => ['all'],
        ];
    }

    /**
     * Get cache key for widget permissions
     */
    protected function getCacheKey(int $userId, string $widgetType): string
    {
        return $this->cachePrefix . $userId . '_' . $widgetType;
    }
}