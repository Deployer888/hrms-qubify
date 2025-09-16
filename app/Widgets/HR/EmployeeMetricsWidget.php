<?php

namespace App\Widgets\HR;

use App\Widgets\BaseWidget;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeMetricsWidget extends BaseWidget
{
    public function getData(): array
    {
        $totalEmployees = $this->getTotalEmployees();
        $activeEmployees = $this->getActiveEmployees();
        $newEmployeesThisMonth = $this->getNewEmployeesThisMonth();
        $employeeGrowthRate = $this->calculateGrowthRate();
        $departmentBreakdown = $this->getDepartmentBreakdown();
        $branchBreakdown = $this->getBranchBreakdown();

        return [
            'total_employees' => $totalEmployees,
            'active_employees' => $activeEmployees,
            'inactive_employees' => $totalEmployees - $activeEmployees,
            'new_employees_this_month' => $newEmployeesThisMonth,
            'growth_rate' => $employeeGrowthRate,
            'department_breakdown' => $departmentBreakdown,
            'branch_breakdown' => $branchBreakdown,
            'metrics' => [
                'total' => $totalEmployees,
                'active' => $activeEmployees,
                'active_percentage' => $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0,
                'new_this_month' => $newEmployeesThisMonth,
                'growth_rate' => $employeeGrowthRate
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['manage employees', 'view employees'];
    }

    protected function getWidgetType(): string
    {
        return 'employee_metrics';
    }

    protected function getDefaultTitle(): string
    {
        return 'Employee Metrics';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-users';
    }

    protected function getDefaultColor(): string
    {
        return 'primary';
    }

    private function getTotalEmployees(): int
    {
        return Employee::where('created_by', $this->user->created_by ?: $this->user->id)->count();
    }

    private function getActiveEmployees(): int
    {
        return Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('is_active', 1)
            ->count();
    }

    private function getNewEmployeesThisMonth(): int
    {
        return Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function calculateGrowthRate(): float
    {
        $currentMonth = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100.0 : 0.0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function getDepartmentBreakdown(): array
    {
        return Employee::select('departments.name', DB::raw('count(*) as count'))
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->where('employees.created_by', $this->user->created_by ?: $this->user->id)
            ->where('employees.is_active', 1)
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function getBranchBreakdown(): array
    {
        return Employee::select('branches.name', DB::raw('count(*) as count'))
            ->join('branches', 'employees.branch_id', '=', 'branches.id')
            ->where('employees.created_by', $this->user->created_by ?: $this->user->id)
            ->where('employees.is_active', 1)
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }
}