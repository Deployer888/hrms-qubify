<?php

namespace App\Widgets\HR;

use App\Widgets\BaseWidget;
use App\Models\Employee;
use App\Models\EmployeeLocation;
use App\Models\AttendanceEmployee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeLocationWidget extends BaseWidget
{
    public function getData(): array
    {
        $locationStats = $this->getLocationStatistics();
        $recentLocations = $this->getRecentLocationUpdates();
        $workingLocationBreakdown = $this->getWorkingLocationBreakdown();
        $remoteWorkStats = $this->getRemoteWorkStatistics();

        return [
            'location_stats' => $locationStats,
            'recent_locations' => $recentLocations,
            'working_location_breakdown' => $workingLocationBreakdown,
            'remote_work_stats' => $remoteWorkStats,
            'summary' => [
                'total_tracked_employees' => $locationStats['total_tracked'],
                'office_employees' => $workingLocationBreakdown['office'] ?? 0,
                'remote_employees' => $workingLocationBreakdown['remote'] ?? 0,
                'field_employees' => $workingLocationBreakdown['field'] ?? 0
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view employee locations'];
    }

    protected function getWidgetType(): string
    {
        return 'employee_location';
    }

    protected function getDefaultTitle(): string
    {
        return 'Employee Locations';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-map-marker-alt';
    }

    protected function getDefaultColor(): string
    {
        return 'danger';
    }

    private function getLocationStatistics(): array
    {
        $today = Carbon::today();
        
        $totalEmployees = Employee::where('created_by', $this->user->created_by ?: $this->user->id)
            ->where('is_active', 1)
            ->count();

        $trackedEmployees = EmployeeLocation::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id)
                      ->where('is_active', 1);
            })
            ->whereDate('time', $today)
            ->distinct('employee_id')
            ->count();

        $employeesWithAttendanceLocation = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id)
                      ->where('is_active', 1);
            })
            ->whereDate('date', $today)
            ->whereNotNull('clock_in_latitude')
            ->whereNotNull('clock_in_longitude')
            ->count();

        return [
            'total_employees' => $totalEmployees,
            'total_tracked' => $trackedEmployees,
            'attendance_with_location' => $employeesWithAttendanceLocation,
            'tracking_percentage' => $totalEmployees > 0 
                ? round(($trackedEmployees / $totalEmployees) * 100, 1) 
                : 0
        ];
    }

    private function getRecentLocationUpdates(): array
    {
        return EmployeeLocation::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->with(['employee:id,name,employee_id'])
            ->orderBy('time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($location) {
                return [
                    'employee_id' => $location->employee->id,
                    'employee_name' => $location->employee->name,
                    'employee_code' => $location->employee->employee_id,
                    'location_name' => $location->location_name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'timestamp' => $location->time,
                    'time_ago' => Carbon::parse($location->time)->diffForHumans(),
                    'is_recent' => Carbon::parse($location->time)->diffInMinutes(Carbon::now()) <= 30
                ];
            })
            ->toArray();
    }

    private function getWorkingLocationBreakdown(): array
    {
        $today = Carbon::today();
        
        $locationBreakdown = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id)
                      ->where('is_active', 1);
            })
            ->whereDate('date', $today)
            ->where('status', 'Present')
            ->select('working_location', DB::raw('count(*) as count'))
            ->groupBy('working_location')
            ->get()
            ->pluck('count', 'working_location')
            ->toArray();

        // Normalize location names
        $normalizedBreakdown = [];
        foreach ($locationBreakdown as $location => $count) {
            $normalizedLocation = $this->normalizeLocationName($location);
            $normalizedBreakdown[$normalizedLocation] = ($normalizedBreakdown[$normalizedLocation] ?? 0) + $count;
        }

        return $normalizedBreakdown;
    }

    private function getRemoteWorkStatistics(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        $remoteToday = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->whereDate('date', $today)
            ->where('status', 'Present')
            ->where(function ($query) {
                $query->where('working_location', 'LIKE', '%remote%')
                      ->orWhere('working_location', 'LIKE', '%home%')
                      ->orWhere('working_location', 'LIKE', '%wfh%');
            })
            ->count();

        $remoteThisWeek = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->where('date', '>=', $thisWeek)
            ->where('status', 'Present')
            ->where(function ($query) {
                $query->where('working_location', 'LIKE', '%remote%')
                      ->orWhere('working_location', 'LIKE', '%home%')
                      ->orWhere('working_location', 'LIKE', '%wfh%');
            })
            ->count();

        $remoteThisMonth = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->where('date', '>=', $thisMonth)
            ->where('status', 'Present')
            ->where(function ($query) {
                $query->where('working_location', 'LIKE', '%remote%')
                      ->orWhere('working_location', 'LIKE', '%home%')
                      ->orWhere('working_location', 'LIKE', '%wfh%');
            })
            ->count();

        $totalPresentToday = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->whereDate('date', $today)
            ->where('status', 'Present')
            ->count();

        return [
            'remote_today' => $remoteToday,
            'remote_this_week' => $remoteThisWeek,
            'remote_this_month' => $remoteThisMonth,
            'remote_percentage_today' => $totalPresentToday > 0 
                ? round(($remoteToday / $totalPresentToday) * 100, 1) 
                : 0,
            'trend' => $this->calculateRemoteWorkTrend()
        ];
    }

    private function calculateRemoteWorkTrend(): array
    {
        $currentWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();

        $currentWeekRemote = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->where('date', '>=', $currentWeek)
            ->where('status', 'Present')
            ->where(function ($query) {
                $query->where('working_location', 'LIKE', '%remote%')
                      ->orWhere('working_location', 'LIKE', '%home%')
                      ->orWhere('working_location', 'LIKE', '%wfh%');
            })
            ->count();

        $lastWeekRemote = AttendanceEmployee::whereHas('employee', function ($query) {
                $query->where('created_by', $this->user->created_by ?: $this->user->id);
            })
            ->where('date', '>=', $lastWeek)
            ->where('date', '<', $currentWeek)
            ->where('status', 'Present')
            ->where(function ($query) {
                $query->where('working_location', 'LIKE', '%remote%')
                      ->orWhere('working_location', 'LIKE', '%home%')
                      ->orWhere('working_location', 'LIKE', '%wfh%');
            })
            ->count();

        $change = $currentWeekRemote - $lastWeekRemote;
        $percentageChange = $lastWeekRemote > 0 
            ? round(($change / $lastWeekRemote) * 100, 1)
            : ($currentWeekRemote > 0 ? 100 : 0);

        return [
            'current_week' => $currentWeekRemote,
            'last_week' => $lastWeekRemote,
            'change' => $change,
            'percentage_change' => $percentageChange,
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
        ];
    }

    private function normalizeLocationName(?string $location): string
    {
        if (!$location) {
            return 'unknown';
        }

        $location = strtolower(trim($location));

        if (strpos($location, 'office') !== false || strpos($location, 'workplace') !== false) {
            return 'office';
        } elseif (strpos($location, 'remote') !== false || strpos($location, 'home') !== false || strpos($location, 'wfh') !== false) {
            return 'remote';
        } elseif (strpos($location, 'field') !== false || strpos($location, 'client') !== false || strpos($location, 'site') !== false) {
            return 'field';
        } else {
            return 'other';
        }
    }
}