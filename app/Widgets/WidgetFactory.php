<?php

namespace App\Widgets;

use App\Models\User;
use App\Services\RoleDetectionService;
use Illuminate\Support\Collection;

class WidgetFactory
{
    protected $roleDetectionService;
    protected $widgetRegistry = [];

    public function __construct(RoleDetectionService $roleDetectionService)
    {
        $this->roleDetectionService = $roleDetectionService;
        $this->registerDefaultWidgets();
    }

    /**
     * Create widgets for a specific user based on their role
     */
    public function createWidgetsForUser(User $user): Collection
    {
        $userRole = $this->roleDetectionService->getUserRole($user);
        $permittedWidgets = $this->getPermittedWidgets($user);
        
        $widgets = collect();

        foreach ($permittedWidgets as $widgetClass) {
            if (class_exists($widgetClass)) {
                $widget = new $widgetClass($user);
                if ($widget->canAccess($user)) {
                    $widgets->push($widget);
                }
            }
        }

        return $widgets;
    }

    /**
     * Create a specific widget by type
     */
    public function createWidget(string $widgetType, User $user, array $config = []): ?BaseWidget
    {
        $widgetClass = $this->getWidgetClass($widgetType);
        
        if (!$widgetClass || !class_exists($widgetClass)) {
            return null;
        }

        $widget = new $widgetClass($user, $config);
        
        return $widget->canAccess($user) ? $widget : null;
    }

    /**
     * Register a widget class
     */
    public function register(string $widgetType, string $widgetClass, array $roles = []): void
    {
        $this->widgetRegistry[$widgetType] = [
            'class' => $widgetClass,
            'roles' => $roles
        ];
    }

    /**
     * Get permitted widgets for a user
     */
    public function getPermittedWidgets(User $user): array
    {
        $userRole = $this->roleDetectionService->getUserRole($user);
        $permittedWidgets = [];

        foreach ($this->widgetRegistry as $widgetType => $config) {
            // If no roles specified, widget is available to all
            if (empty($config['roles'])) {
                $permittedWidgets[] = $config['class'];
                continue;
            }

            // Check if user role matches any of the widget's allowed roles
            if (in_array($userRole, $config['roles']) || in_array('all', $config['roles'])) {
                $permittedWidgets[] = $config['class'];
            }
        }

        return $permittedWidgets;
    }

    /**
     * Get widget class by type
     */
    protected function getWidgetClass(string $widgetType): ?string
    {
        return $this->widgetRegistry[$widgetType]['class'] ?? null;
    }

    /**
     * Register default widgets
     */
    protected function registerDefaultWidgets(): void
    {
        // HR Widgets
        $this->register('employee_metrics', 'App\Widgets\HR\EmployeeMetricsWidget', ['hr', 'admin']);
        $this->register('attendance_gauge', 'App\Widgets\HR\AttendanceGaugeWidget', ['hr', 'admin']);
        $this->register('leave_management', 'App\Widgets\HR\LeaveManagementWidget', ['hr', 'admin']);
        $this->register('department_performance', 'App\Widgets\HR\DepartmentPerformanceWidget', ['hr', 'admin']);
        $this->register('hr_activities', 'App\Widgets\HR\HRActivitiesWidget', ['hr', 'admin']);
        $this->register('employee_location', 'App\Widgets\HR\EmployeeLocationWidget', ['hr', 'admin']);

        // Company Widgets
        $this->register('revenue_analytics', 'App\Widgets\Company\RevenueAnalyticsWidget', ['company', 'admin']);
        $this->register('business_growth', 'App\Widgets\Company\BusinessGrowthWidget', ['company', 'admin']);
        $this->register('user_acquisition', 'App\Widgets\Company\UserAcquisitionWidget', ['company', 'admin']);
        $this->register('plan_performance', 'App\Widgets\Company\PlanPerformanceWidget', ['company', 'admin']);
        $this->register('company_activities', 'App\Widgets\Company\CompanyActivitiesWidget', ['company', 'admin']);
        $this->register('strategic_kpis', 'App\Widgets\Company\StrategicKPIsWidget', ['company', 'admin']);

        // Shared Widgets (available to all roles)
        $this->register('notifications', 'App\Widgets\Shared\NotificationsWidget', ['all']);
        $this->register('quick_actions', 'App\Widgets\Shared\QuickActionsWidget', ['all']);
    }

    /**
     * Get all registered widgets
     */
    public function getRegisteredWidgets(): array
    {
        return $this->widgetRegistry;
    }

    /**
     * Get widgets by role
     */
    public function getWidgetsByRole(string $role): array
    {
        $widgets = [];

        foreach ($this->widgetRegistry as $widgetType => $config) {
            if (empty($config['roles']) || in_array($role, $config['roles']) || in_array('all', $config['roles'])) {
                $widgets[$widgetType] = $config;
            }
        }

        return $widgets;
    }
}