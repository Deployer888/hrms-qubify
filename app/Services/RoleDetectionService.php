<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoleDetectionService
{
    const ROLE_HR = 'hr';
    const ROLE_COMPANY = 'company';
    const ROLE_EMPLOYEE = 'employee';
    const ROLE_SUPER_ADMIN = 'super admin';
    
    const DASHBOARD_HR = 'hr_dashboard';
    const DASHBOARD_COMPANY = 'company_dashboard';
    
    const CACHE_PREFIX = 'user_role_';
    const CACHE_TTL = 3600; // 1 hour
    
    /**
     * Get the user's role based on type field and permissions
     *
     * @param User $user
     * @return string
     */
    public function getUserRole(User $user): string
    {
        $cacheKey = self::CACHE_PREFIX . $user->id;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            // Primary check: user type field
            $userType = strtolower($user->type ?? '');
            
            // Direct type mapping
            if (in_array($userType, [self::ROLE_HR, self::ROLE_COMPANY, self::ROLE_EMPLOYEE, self::ROLE_SUPER_ADMIN])) {
                Log::info("Role detected from type field", ['user_id' => $user->id, 'role' => $userType]);
                return $userType;
            }
            
            // Fallback to permission-based detection using Spatie
            if ($user->hasRole('super admin') || $user->hasRole('Super Admin')) {
                return self::ROLE_SUPER_ADMIN;
            }
            
            if ($user->hasRole('hr') || $user->hasRole('HR')) {
                return self::ROLE_HR;
            }
            
            if ($user->hasRole('company') || $user->hasRole('Company')) {
                return self::ROLE_COMPANY;
            }
            
            // Check for HR-specific permissions
            $hrPermissions = ['manage employee', 'create employee', 'edit employee', 'delete employee'];
            foreach ($hrPermissions as $permission) {
                try {
                    if ($user->hasPermissionTo($permission)) {
                        return self::ROLE_HR;
                    }
                } catch (\Exception $e) {
                    // Permission doesn't exist, continue checking
                    continue;
                }
            }
            
            // Check for company-specific permissions
            $companyPermissions = ['manage company', 'view reports', 'manage plans'];
            foreach ($companyPermissions as $permission) {
                try {
                    if ($user->hasPermissionTo($permission)) {
                        return self::ROLE_COMPANY;
                    }
                } catch (\Exception $e) {
                    // Permission doesn't exist, continue checking
                    continue;
                }
            }
            
            // Default fallback
            Log::warning("Could not determine user role, defaulting to employee", ['user_id' => $user->id]);
            return self::ROLE_EMPLOYEE;
        });
    }
    
    /**
     * Get the dashboard type for a user
     *
     * @param User $user
     * @return string
     */
    public function getDashboardType(User $user): string
    {
        $role = $this->getUserRole($user);
        
        switch ($role) {
            case self::ROLE_HR:
            case self::ROLE_EMPLOYEE:
                return self::DASHBOARD_HR;
            case self::ROLE_COMPANY:
            case self::ROLE_SUPER_ADMIN:
                return self::DASHBOARD_COMPANY;
            default:
                return self::DASHBOARD_HR; // Default fallback
        }
    }
    
    /**
     * Check if user can access a specific dashboard type
     *
     * @param User $user
     * @param string $dashboardType
     * @return bool
     */
    public function isDashboardAccessible(User $user, string $dashboardType): bool
    {
        $userDashboardType = $this->getDashboardType($user);
        
        // Super admin can access both dashboards
        if ($this->getUserRole($user) === self::ROLE_SUPER_ADMIN) {
            return true;
        }
        
        return $userDashboardType === $dashboardType;
    }
    
    /**
     * Get permitted widgets for a user based on their role
     *
     * @param User $user
     * @return array
     */
    public function getPermittedWidgets(User $user): array
    {
        $role = $this->getUserRole($user);
        
        switch ($role) {
            case self::ROLE_HR:
                return [
                    'employee_metrics',
                    'attendance_gauge',
                    'leave_management',
                    'department_performance',
                    'hr_activities',
                    'employee_location'
                ];
                
            case self::ROLE_EMPLOYEE:
                return [
                    'employee_metrics',
                    'attendance_gauge',
                    'leave_management'
                ];
                
            case self::ROLE_COMPANY:
                return [
                    'revenue_analytics',
                    'business_growth',
                    'user_acquisition',
                    'plan_performance',
                    'company_activities',
                    'strategic_kpis'
                ];
                
            case self::ROLE_SUPER_ADMIN:
                // Super admin gets all widgets
                return [
                    'employee_metrics',
                    'attendance_gauge',
                    'leave_management',
                    'department_performance',
                    'hr_activities',
                    'employee_location',
                    'revenue_analytics',
                    'business_growth',
                    'user_acquisition',
                    'plan_performance',
                    'company_activities',
                    'strategic_kpis'
                ];
                
            default:
                return ['employee_metrics']; // Minimal access
        }
    }
    
    /**
     * Check if user can access a specific widget
     *
     * @param User $user
     * @param string $widgetType
     * @return bool
     */
    public function canAccessWidget(User $user, string $widgetType): bool
    {
        $permittedWidgets = $this->getPermittedWidgets($user);
        return in_array($widgetType, $permittedWidgets);
    }
    
    /**
     * Clear role cache for a user
     *
     * @param User $user
     * @return void
     */
    public function clearRoleCache(User $user): void
    {
        $cacheKey = self::CACHE_PREFIX . $user->id;
        Cache::forget($cacheKey);
        Log::info("Role cache cleared for user", ['user_id' => $user->id]);
    }
    
    /**
     * Check if user has HR privileges
     *
     * @param User $user
     * @return bool
     */
    public function isHRUser(User $user): bool
    {
        $role = $this->getUserRole($user);
        return in_array($role, [self::ROLE_HR, self::ROLE_SUPER_ADMIN]);
    }
    
    /**
     * Check if user has company privileges
     *
     * @param User $user
     * @return bool
     */
    public function isCompanyUser(User $user): bool
    {
        $role = $this->getUserRole($user);
        return in_array($role, [self::ROLE_COMPANY, self::ROLE_SUPER_ADMIN]);
    }
    
    /**
     * Get all available dashboard types
     *
     * @return array
     */
    public static function getAvailableDashboardTypes(): array
    {
        return [
            self::DASHBOARD_HR,
            self::DASHBOARD_COMPANY
        ];
    }
    
    /**
     * Get all available user roles
     *
     * @return array
     */
    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_HR,
            self::ROLE_COMPANY,
            self::ROLE_EMPLOYEE,
            self::ROLE_SUPER_ADMIN
        ];
    }
}