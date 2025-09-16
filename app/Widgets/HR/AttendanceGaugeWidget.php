<?php

namespace App\Widgets\HR;

use App\Widgets\BaseWidget;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceGaugeWidget extends BaseWidget
{
    public function getData(): array
    {
        $attendanceRate = $this->calculateAttendanceRate();
        $todayAttendance = $this->getTodayAttendance();
        $weeklyTrend = $this->getWeeklyAttendanceTrend();
        $monthlyStats = $this->getMonthlyAttendanceStats();

        return [
            'attendance_rate' => $attendanceRate,
            'today_attendance' => $todayAttendance,
            'weekly_trend' => $weeklyTrend,
            'monthly_stats' => $monthlyStats,
            'gauge_data' => [
                'value' => $attendanceRate,
                'max' => 100,
                'color' => $this->getGaugeColor($attendanceRate),
                'label' => 'Overall Attendance Rate'
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['manage attendance', 'view attendance'];
    }

    protected function getWidgetType(): string
    {
        return 'attendance_gauge';
    }

    protected function getDefaultTitle(): string
    {
        return 'Attendance Rate';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-clock';
    }

    protected function getDefaultColor(): string
    {
        return 'success';
    }

    private function calculateAttendanceRate(): float
    {
        $totalWorkingDays = $this->getTotalWorkingDaysThisMonth();
        $totalPresentDays = $this->getTotalPresentDaysThisMonth();

        if ($totalWorkingDays == 0) {
            return 0.0;
        }

        return round(($totalPresentDays / $totalWorkingDays) * 100, 2);
    }

    private function getTotalWorkingDaysThisMonth(): int
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $activeEmployees = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('is_active', 1)
            ->count();

        $workingDays = 0;
        $current = $startOfMonth->copy();
        
        while ($current->lte($endOfMonth) && $current->lte(Carbon::now())) {
            // Skip weekends (assuming Saturday and Sunday are non-working days)
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays * $activeEmployees;
    }

    private function getTotalPresentDaysThisMonth(): int
    {
        return AttendanceEmployee::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('status', 'Present')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();
    }

    private function getTodayAttendance(): array
    {
        $today = Carbon::today();
        
        $totalEmployees = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('is_active', 1)
            ->count();

        $presentToday = AttendanceEmployee::where('date', $today)
            ->where('status', 'Present')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        $absentToday = AttendanceEmployee::where('date', $today)
            ->whereIn('status', ['Absent', 'Leave'])
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        return [
            'total' => $totalEmployees,
            'present' => $presentToday,
            'absent' => $absentToday,
            'not_marked' => $totalEmployees - $presentToday - $absentToday,
            'present_percentage' => $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0
        ];
    }

    private function getWeeklyAttendanceTrend(): array
    {
        $weeklyData = [];
        $startOfWeek = Carbon::now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            
            if ($date->gt(Carbon::now())) {
                break;
            }

            $presentCount = AttendanceEmployee::where('date', $date)
                ->where('status', 'Present')
                ->whereHas('employee', function ($query) {
                    $query->where('created_by', $this->user->created_by ?: $this->user->id);
                })
                ->count();

            $totalEmployees = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
                ->where('is_active', 1)
                ->count();

            $weeklyData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'present' => $presentCount,
                'total' => $totalEmployees,
                'percentage' => $totalEmployees > 0 ? round(($presentCount / $totalEmployees) * 100, 1) : 0
            ];
        }

        return $weeklyData;
    }

    private function getMonthlyAttendanceStats(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthRate = $this->getMonthAttendanceRate($currentMonth);
        $lastMonthRate = $this->getMonthAttendanceRate($lastMonth);

        $trend = $currentMonthRate - $lastMonthRate;

        return [
            'current_month' => $currentMonthRate,
            'last_month' => $lastMonthRate,
            'trend' => $trend,
            'trend_direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable')
        ];
    }

    private function getMonthAttendanceRate(Carbon $month): float
    {
        $totalWorkingDays = $this->getWorkingDaysInMonth($month);
        $totalPresentDays = AttendanceEmployee::whereMonth('date', $month->month)
            ->whereYear('date', $month->year)
            ->where('status', 'Present')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        if ($totalWorkingDays == 0) {
            return 0.0;
        }

        return round(($totalPresentDays / $totalWorkingDays) * 100, 2);
    }

    private function getWorkingDaysInMonth(Carbon $month): int
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        
        $activeEmployees = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('is_active', 1)
            ->count();

        $workingDays = 0;
        $current = $startOfMonth->copy();
        
        while ($current->lte($endOfMonth)) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays * $activeEmployees;
    }

    private function getGaugeColor(float $rate): string
    {
        if ($rate >= 90) {
            return '#10b981'; // Green
        } elseif ($rate >= 75) {
            return '#f59e0b'; // Yellow
        } else {
            return '#ef4444'; // Red
        }
    }
}