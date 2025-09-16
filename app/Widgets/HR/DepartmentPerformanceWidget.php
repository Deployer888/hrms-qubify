<?php

namespace App\Widgets\HR;

use App\Widgets\BaseWidget;
use App\Models\Employee;
use App\Models\Department;
use App\Models\AttendanceEmployee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentPerformanceWidget extends BaseWidget
{
    public function getData(): array
    {
        $departmentStats = $this->getDepartmentStatistics();
        $attendanceByDepartment = $this->getAttendanceByDepartment();
        $departmentGrowth = $this->getDepartmentGrowth();
        $topPerformingDepartments = $this->getTopPerformingDepartments();

        return [
            'department_stats' => $departmentStats,
            'attendance_by_department' => $attendanceByDepartment,
            'department_growth' => $departmentGrowth,
            'top_performing' => $topPerformingDepartments,
            'summary' => [
                'total_departments' => count($departmentStats),
                'best_attendance_dept' => $this->getBestAttendanceDepartment($attendanceByDepartment),
                'fastest_growing_dept' => $this->getFastestGrowingDepartment($departmentGrowth)
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['manage departments', 'view departments'];
    }

    protected function getWidgetType(): string
    {
        return 'department_performance';
    }

    protected function getDefaultTitle(): string
    {
        return 'Department Performance';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-building';
    }

    protected function getDefaultColor(): string
    {
        return 'info';
    }

    private function getDepartmentStatistics(): array
    {
        return Department::select(
                'departments.id',
                'departments.name',
                DB::raw('COUNT(employees.id) as employee_count'),
                DB::raw('COUNT(CASE WHEN employees.is_active = 1 THEN 1 END) as active_employees'),
                DB::raw('COUNT(CASE WHEN employees.is_active = 0 THEN 1 END) as inactive_employees')
            )
            ->leftJoin('employees', function ($join) {
                $join->on('departments.id', '=', 'employees.department_id')
                     ->where('employees.created_by', $this->user->created_by ?: $this->user->id);
            })
            ->where('departments.created_by', $this->user->created_by ?: $this->user->id)
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('employee_count', 'desc')
            ->get()
            ->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'total_employees' => $dept->employee_count,
                    'active_employees' => $dept->active_employees,
                    'inactive_employees' => $dept->inactive_employees,
                    'activity_rate' => $dept->employee_count > 0 
                        ? round(($dept->active_employees / $dept->employee_count) * 100, 1) 
                        : 0,
                    'color' => $this->getDepartmentColor($dept->id)
                ];
            })
            ->toArray();
    }

    private function getAttendanceByDepartment(): array
    {
        $currentMonth = Carbon::now();
        
        return Department::select(
                'departments.id',
                'departments.name',
                DB::raw('COUNT(DISTINCT employees.id) as total_employees'),
                DB::raw('COUNT(CASE WHEN attendance_employees.status = "Present" THEN 1 END) as present_days'),
                DB::raw('COUNT(attendance_employees.id) as total_attendance_records')
            )
            ->leftJoin('employees', function ($join) {
                $join->on('departments.id', '=', 'employees.department_id')
                     ->where('employees.created_by', $this->user->created_by ?: $this->user->id)
                     ->where('employees.is_active', 1);
            })
            ->leftJoin('attendance_employees', function ($join) use ($currentMonth) {
                $join->on('employees.id', '=', 'attendance_employees.employee_id')
                     ->whereMonth('attendance_employees.date', $currentMonth->month)
                     ->whereYear('attendance_employees.date', $currentMonth->year);
            })
            ->where('departments.created_by', $this->user->created_by ?: $this->user->id)
            ->groupBy('departments.id', 'departments.name')
            ->having('total_employees', '>', 0)
            ->get()
            ->map(function ($dept) {
                $attendanceRate = $dept->total_attendance_records > 0 
                    ? round(($dept->present_days / $dept->total_attendance_records) * 100, 1)
                    : 0;

                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'total_employees' => $dept->total_employees,
                    'present_days' => $dept->present_days,
                    'total_records' => $dept->total_attendance_records,
                    'attendance_rate' => $attendanceRate,
                    'performance_level' => $this->getPerformanceLevel($attendanceRate),
                    'color' => $this->getDepartmentColor($dept->id)
                ];
            })
            ->sortByDesc('attendance_rate')
            ->values()
            ->toArray();
    }

    private function getDepartmentGrowth(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        return Department::select('departments.id', 'departments.name')
            ->where('departments.created_by', $this->user->created_by ?: $this->user->id)
            ->get()
            ->map(function ($dept) use ($currentMonth, $lastMonth) {
                $currentCount = Employee::where('department_id', $dept->id)
                    ->where('created_by', $this->user->created_by ?: $this->user->id)
                    ->whereMonth('created_at', '<=', $currentMonth->month)
                    ->whereYear('created_at', '<=', $currentMonth->year)
                    ->count();

                $lastMonthCount = Employee::where('department_id', $dept->id)
                    ->where('created_by', $this->user->created_by ?: $this->user->id)
                    ->whereMonth('created_at', '<=', $lastMonth->month)
                    ->whereYear('created_at', '<=', $lastMonth->year)
                    ->count();

                $growth = $currentCount - $lastMonthCount;
                $growthRate = $lastMonthCount > 0 
                    ? round(($growth / $lastMonthCount) * 100, 1)
                    : ($currentCount > 0 ? 100 : 0);

                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'current_count' => $currentCount,
                    'last_month_count' => $lastMonthCount,
                    'growth' => $growth,
                    'growth_rate' => $growthRate,
                    'trend' => $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'stable'),
                    'color' => $this->getDepartmentColor($dept->id)
                ];
            })
            ->sortByDesc('growth_rate')
            ->values()
            ->toArray();
    }

    private function getTopPerformingDepartments(): array
    {
        $attendanceData = $this->getAttendanceByDepartment();
        
        return collect($attendanceData)
            ->sortByDesc('attendance_rate')
            ->take(5)
            ->map(function ($dept, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $dept['id'],
                    'name' => $dept['name'],
                    'attendance_rate' => $dept['attendance_rate'],
                    'total_employees' => $dept['total_employees'],
                    'performance_level' => $dept['performance_level'],
                    'badge' => $this->getPerformanceBadge($index + 1),
                    'color' => $dept['color']
                ];
            })
            ->values()
            ->toArray();
    }

    private function getBestAttendanceDepartment(array $attendanceData): ?array
    {
        if (empty($attendanceData)) {
            return null;
        }

        $best = collect($attendanceData)->sortByDesc('attendance_rate')->first();
        
        return [
            'name' => $best['name'],
            'attendance_rate' => $best['attendance_rate']
        ];
    }

    private function getFastestGrowingDepartment(array $growthData): ?array
    {
        if (empty($growthData)) {
            return null;
        }

        $fastest = collect($growthData)->sortByDesc('growth_rate')->first();
        
        return [
            'name' => $fastest['name'],
            'growth_rate' => $fastest['growth_rate']
        ];
    }

    private function getDepartmentColor(int $deptId): string
    {
        $colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'
        ];
        
        return $colors[$deptId % count($colors)];
    }

    private function getPerformanceLevel(float $attendanceRate): string
    {
        if ($attendanceRate >= 95) {
            return 'Excellent';
        } elseif ($attendanceRate >= 85) {
            return 'Good';
        } elseif ($attendanceRate >= 75) {
            return 'Average';
        } elseif ($attendanceRate >= 60) {
            return 'Below Average';
        } else {
            return 'Poor';
        }
    }

    private function getPerformanceBadge(int $rank): string
    {
        switch ($rank) {
            case 1:
                return '🥇';
            case 2:
                return '🥈';
            case 3:
                return '🥉';
            default:
                return '⭐';
        }
    }
}