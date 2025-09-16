<?php

namespace App\Widgets\HR;

use App\Widgets\BaseWidget;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\AttendanceEmployee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRActivitiesWidget extends BaseWidget
{
    public function getData(): array
    {
        $recentActivities = $this->getRecentActivities();
        $activitySummary = $this->getActivitySummary();
        $todayActivities = $this->getTodayActivities();

        return [
            'recent_activities' => $recentActivities,
            'activity_summary' => $activitySummary,
            'today_activities' => $todayActivities,
            'stats' => [
                'total_activities_today' => count($todayActivities),
                'pending_actions' => $this->getPendingActionsCount(),
                'recent_count' => count($recentActivities)
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view hr activities'];
    }

    protected function getWidgetType(): string
    {
        return 'hr_activities';
    }

    protected function getDefaultTitle(): string
    {
        return 'HR Activities';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-clipboard-list';
    }

    protected function getDefaultColor(): string
    {
        return 'secondary';
    }

    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent employee additions
        $newEmployees = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['department:id,name', 'branch:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($newEmployees as $employee) {
            $activities[] = [
                'type' => 'employee_added',
                'title' => 'New Employee Added',
                'description' => "Employee {$employee->name} joined {$employee->department->name ?? 'Unknown'} department",
                'timestamp' => $employee->created_at,
                'icon' => 'fas fa-user-plus',
                'color' => '#10b981',
                'data' => [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'department' => $employee->department->name ?? 'Unknown'
                ]
            ];
        }

        // Recent leave applications
        $recentLeaves = Leave::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['employee:id,name,employee_id'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentLeaves as $leave) {
            $activities[] = [
                'type' => 'leave_application',
                'title' => 'Leave Application',
                'description' => "{$leave->employee->name} applied for {$leave->leave_type}",
                'timestamp' => $leave->created_at,
                'icon' => 'fas fa-calendar-minus',
                'color' => $this->getLeaveStatusColor($leave->status),
                'data' => [
                    'leave_id' => $leave->id,
                    'employee_name' => $leave->employee->name,
                    'leave_type' => $leave->leave_type,
                    'status' => $leave->status,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date
                ]
            ];
        }

        // Recent user registrations
        $recentUsers = User::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_registered',
                'title' => 'New User Registration',
                'description' => "User {$user->name} registered with {$user->type} role",
                'timestamp' => $user->created_at,
                'icon' => 'fas fa-user-check',
                'color' => '#3b82f6',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_type' => $user->type,
                    'email' => $user->email
                ]
            ];
        }

        // Sort all activities by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });

        return array_slice($activities, 0, 10);
    }

    private function getActivitySummary(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today' => [
                'employees_added' => Employee::where('created_by', $this->user->created_by ?: $this->user->id)
                    ->whereDate('created_at', $today)
                    ->count(),
                'leave_applications' => Leave::whereHas('employee', function ($query) {
                        $query->where('created_by', $this->user->created_by ?: $this->user->id);
                    })
                    ->whereDate('created_at', $today)
                    ->count(),
                'attendance_marked' => AttendanceEmployee::whereHas('employee', function ($query) {
                        $query->where('created_by', $this->user->created_by ?: $this->user->id);
                    })
                    ->whereDate('date', $today)
                    ->count()
            ],
            'this_week' => [
                'employees_added' => Employee::where('created_by', $this->user->created_by ?: $this->user->id)
                    ->where('created_at', '>=', $thisWeek)
                    ->count(),
                'leave_applications' => Leave::whereHas('employee', function ($query) {
                        $query->where('created_by', $this->user->created_by ?: $this->user->id);
                    })
                    ->where('created_at', '>=', $thisWeek)
                    ->count(),
                'users_registered' => User::where('created_by', $this->user->created_by ?: $this->user->id)
                    ->where('created_at', '>=', $thisWeek)
                    ->count()
            ],
            'this_month' => [
                'employees_added' => Employee::where('created_by', $this->user->created_by ?: $this->user->id)
                    ->where('created_at', '>=', $thisMonth)
                    ->count(),
                'leave_applications' => Leave::whereHas('employee', function ($query) {
                        $query->where('created_by', $this->user->created_by ?: $this->user->id);
                    })
                    ->where('created_at', '>=', $thisMonth)
                    ->count(),
                'total_attendance_records' => AttendanceEmployee::whereHas('employee', function ($query) {
                        $query->where('created_by', $this->user->created_by ?: $this->user->id);
                    })
                    ->where('date', '>=', $thisMonth)
                    ->count()
            ]
        ];
    }

    private function getTodayActivities(): array
    {
        $today = Carbon::today();
        $activities = [];

        // Today's attendance
        $todayAttendance = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->whereDate('date', $today)
            ->with(['employee:id,name,employee_id'])
            ->orderBy('clock_in', 'desc')
            ->limit(5)
            ->get();

        foreach ($todayAttendance as $attendance) {
            $activities[] = [
                'type' => 'attendance',
                'title' => 'Attendance Marked',
                'description' => "{$attendance->employee->name} marked {$attendance->status}",
                'timestamp' => Carbon::parse($attendance->date . ' ' . $attendance->clock_in),
                'icon' => 'fas fa-clock',
                'color' => $this->getAttendanceStatusColor($attendance->status),
                'data' => [
                    'employee_name' => $attendance->employee->name,
                    'status' => $attendance->status,
                    'clock_in' => $attendance->clock_in,
                    'clock_out' => $attendance->clock_out
                ]
            ];
        }

        // Today's leave applications
        $todayLeaves = Leave::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->whereDate('created_at', $today)
            ->with(['employee:id,name,employee_id'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($todayLeaves as $leave) {
            $activities[] = [
                'type' => 'leave_today',
                'title' => 'Leave Application Today',
                'description' => "{$leave->employee->name} applied for {$leave->leave_type}",
                'timestamp' => $leave->created_at,
                'icon' => 'fas fa-calendar-minus',
                'color' => $this->getLeaveStatusColor($leave->status),
                'data' => [
                    'employee_name' => $leave->employee->name,
                    'leave_type' => $leave->leave_type,
                    'status' => $leave->status
                ]
            ];
        }

        // Sort by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });

        return $activities;
    }

    private function getPendingActionsCount(): int
    {
        return Leave::where('status', 'Pending')
            ->whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->count();
    }

    private function getLeaveStatusColor(string $status): string
    {
        switch ($status) {
            case 'Approved':
                return '#10b981';
            case 'Reject':
                return '#ef4444';
            case 'Pending':
                return '#f59e0b';
            default:
                return '#6b7280';
        }
    }

    private function getAttendanceStatusColor(string $status): string
    {
        switch ($status) {
            case 'Present':
                return '#10b981';
            case 'Absent':
                return '#ef4444';
            case 'Leave':
                return '#f59e0b';
            case 'Half Day':
                return '#3b82f6';
            default:
                return '#6b7280';
        }
    }
}