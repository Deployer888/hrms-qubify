<?php

namespace App\Widgets\HR;

use App\Widgets\BaseWidget;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveManagementWidget extends BaseWidget
{
    public function getData(): array
    {
        $leaveStats = $this->getLeaveStatistics();
        $pendingLeaves = $this->getPendingLeaves();
        $leaveTypeDistribution = $this->getLeaveTypeDistribution();
        $monthlyLeaveComparison = $this->getMonthlyLeaveComparison();
        $upcomingLeaves = $this->getUpcomingLeaves();

        return [
            'leave_stats' => $leaveStats,
            'pending_leaves' => $pendingLeaves,
            'leave_type_distribution' => $leaveTypeDistribution,
            'monthly_comparison' => $monthlyLeaveComparison,
            'upcoming_leaves' => $upcomingLeaves,
            'summary' => [
                'total_leaves_this_month' => $leaveStats['total_this_month'],
                'pending_approvals' => $pendingLeaves['count'],
                'approved_leaves' => $leaveStats['approved'],
                'rejected_leaves' => $leaveStats['rejected']
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['manage leaves', 'view leaves'];
    }

    protected function getWidgetType(): string
    {
        return 'leave_management';
    }

    protected function getDefaultTitle(): string
    {
        return 'Leave Management';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-calendar-minus';
    }

    protected function getDefaultColor(): string
    {
        return 'warning';
    }

    private function getLeaveStatistics(): array
    {
        $currentMonth = Carbon::now();
        
        $totalThisMonth = Leave::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        $approved = Leave::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->where('status', 'Approved')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        $rejected = Leave::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->where('status', 'Reject')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        $pending = Leave::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->where('status', 'Pending')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        return [
            'total_this_month' => $totalThisMonth,
            'approved' => $approved,
            'rejected' => $rejected,
            'pending' => $pending,
            'approval_rate' => $totalThisMonth > 0 ? round(($approved / $totalThisMonth) * 100, 1) : 0
        ];
    }

    private function getPendingLeaves(): array
    {
        $pendingLeaves = Leave::where('status', 'Pending')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->with(['employee:id,name,employee_id'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'count' => $pendingLeaves->count(),
            'leaves' => $pendingLeaves->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'employee_name' => $leave->employee->name ?? 'Unknown',
                    'employee_id' => $leave->employee->employee_id ?? 'N/A',
                    'leave_type' => $leave->leave_type,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'days' => Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1,
                    'reason' => $leave->leave_reason,
                    'applied_on' => $leave->created_at->format('Y-m-d')
                ];
            })
        ];
    }

    private function getLeaveTypeDistribution(): array
    {
        return Leave::select('leave_type', DB::raw('count(*) as count'))
            ->whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', Carbon::now()->year)
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->groupBy('leave_type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->leave_type,
                    'count' => $item->count,
                    'color' => $this->getLeaveTypeColor($item->leave_type)
                ];
            })
            ->toArray();
    }

    private function getMonthlyLeaveComparison(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthLeaves = Leave::whereMonth('start_date', $currentMonth->month)
            ->whereYear('start_date', $currentMonth->year)
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        $lastMonthLeaves = Leave::whereMonth('start_date', $lastMonth->month)
            ->whereYear('start_date', $lastMonth->year)
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();

        $percentageChange = $lastMonthLeaves > 0 
            ? round((($currentMonthLeaves - $lastMonthLeaves) / $lastMonthLeaves) * 100, 1)
            : ($currentMonthLeaves > 0 ? 100 : 0);

        return [
            'current_month' => $currentMonthLeaves,
            'last_month' => $lastMonthLeaves,
            'change' => $currentMonthLeaves - $lastMonthLeaves,
            'percentage_change' => $percentageChange,
            'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'stable')
        ];
    }

    private function getUpcomingLeaves(): array
    {
        $upcomingLeaves = Leave::where('status', 'Approved')
            ->where('start_date', '>', Carbon::now())
            ->where('start_date', '<=', Carbon::now()->addDays(7))
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->with(['employee:id,name,employee_id'])
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return $upcomingLeaves->map(function ($leave) {
            return [
                'id' => $leave->id,
                'employee_name' => $leave->employee->name ?? 'Unknown',
                'employee_id' => $leave->employee->employee_id ?? 'N/A',
                'leave_type' => $leave->leave_type,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'days' => Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1,
                'days_until' => Carbon::now()->diffInDays(Carbon::parse($leave->start_date))
            ];
        })->toArray();
    }

    private function getLeaveTypeColor(string $leaveType): string
    {
        $colors = [
            'Sick Leave' => '#ef4444',
            'Casual Leave' => '#3b82f6',
            'Annual Leave' => '#10b981',
            'Maternity Leave' => '#f59e0b',
            'Paternity Leave' => '#8b5cf6',
            'Emergency Leave' => '#f97316',
            'Medical Leave' => '#ec4899',
            'Other' => '#6b7280'
        ];

        return $colors[$leaveType] ?? '#6b7280';
    }
}