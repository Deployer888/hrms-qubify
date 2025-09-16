<?php

namespace App\Widgets\Company;

use App\Widgets\BaseWidget;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserAcquisitionWidget extends BaseWidget
{
    public function getData(): array
    {
        $acquisitionStats = $this->getAcquisitionStatistics();
        $dailySignups = $this->getDailySignups();
        $userTypeBreakdown = $this->getUserTypeBreakdown();
        $acquisitionTrends = $this->getAcquisitionTrends();

        return [
            'acquisition_stats' => $acquisitionStats,
            'daily_signups' => $dailySignups,
            'user_type_breakdown' => $userTypeBreakdown,
            'acquisition_trends' => $acquisitionTrends,
            'summary' => [
                'total_users' => $acquisitionStats['total_users'],
                'new_users_today' => $acquisitionStats['new_users_today'],
                'growth_rate' => $acquisitionTrends['monthly']['percentage_change'],
                'most_common_type' => $this->getMostCommonUserType($userTypeBreakdown)
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view user metrics'];
    }

    protected function getWidgetType(): string
    {
        return 'user_acquisition';
    }

    protected function getDefaultTitle(): string
    {
        return 'User Acquisition';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-user-plus';
    }

    protected function getDefaultColor(): string
    {
        return 'info';
    }

    private function getAcquisitionStatistics(): array
    {
        $totalUsers = User::count();
        
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
        
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $averageDailySignups = $this->getAverageDailySignups();

        return [
            'total_users' => $totalUsers,
            'new_users_today' => $newUsersToday,
            'new_users_this_week' => $newUsersThisWeek,
            'new_users_this_month' => $newUsersThisMonth,
            'average_daily_signups' => $averageDailySignups
        ];
    }

    private function getDailySignups(): array
    {
        $dailyData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $signups = User::whereDate('created_at', $date)->count();
            
            $dailyData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'signups' => $signups,
                'is_today' => $date->isToday()
            ];
        }

        return $dailyData;
    }

    private function getUserTypeBreakdown(): array
    {
        return User::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                $totalUsers = User::count();
                return [
                    'type' => $item->type ?: 'Unknown',
                    'count' => $item->count,
                    'percentage' => $totalUsers > 0 ? round(($item->count / $totalUsers) * 100, 1) : 0,
                    'color' => $this->getUserTypeColor($item->type)
                ];
            })
            ->toArray();
    }

    private function getAcquisitionTrends(): array
    {
        // Monthly trend
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthUsers = User::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        $lastMonthUsers = User::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $monthlyChange = $currentMonthUsers - $lastMonthUsers;
        $monthlyPercentageChange = $lastMonthUsers > 0 
            ? round(($monthlyChange / $lastMonthUsers) * 100, 1)
            : ($currentMonthUsers > 0 ? 100 : 0);

        // Weekly trend
        $currentWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();

        $currentWeekUsers = User::where('created_at', '>=', $currentWeek)->count();
        $lastWeekUsers = User::where('created_at', '>=', $lastWeek)
            ->where('created_at', '<', $currentWeek)
            ->count();

        $weeklyChange = $currentWeekUsers - $lastWeekUsers;
        $weeklyPercentageChange = $lastWeekUsers > 0 
            ? round(($weeklyChange / $lastWeekUsers) * 100, 1)
            : ($currentWeekUsers > 0 ? 100 : 0);

        // Monthly data for chart
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userCount = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'users' => $userCount
            ];
        }

        return [
            'monthly' => [
                'current' => $currentMonthUsers,
                'last' => $lastMonthUsers,
                'change' => $monthlyChange,
                'percentage_change' => $monthlyPercentageChange,
                'trend' => $monthlyChange > 0 ? 'up' : ($monthlyChange < 0 ? 'down' : 'stable'),
                'data' => $monthlyData
            ],
            'weekly' => [
                'current' => $currentWeekUsers,
                'last' => $lastWeekUsers,
                'change' => $weeklyChange,
                'percentage_change' => $weeklyPercentageChange,
                'trend' => $weeklyChange > 0 ? 'up' : ($weeklyChange < 0 ? 'down' : 'stable')
            ]
        ];
    }

    private function getAverageDailySignups(): float
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $totalSignups = User::where('created_at', '>=', $thirtyDaysAgo)->count();
        
        return round($totalSignups / 30, 1);
    }

    private function getUserTypeColor(string $type): string
    {
        $colors = [
            'company' => '#3b82f6',
            'hr' => '#10b981',
            'employee' => '#f59e0b',
            'admin' => '#ef4444',
            'super admin' => '#8b5cf6',
            'manager' => '#06b6d4'
        ];

        return $colors[strtolower($type)] ?? '#6b7280';
    }

    private function getMostCommonUserType(array $userTypeBreakdown): ?string
    {
        if (empty($userTypeBreakdown)) {
            return null;
        }

        $mostCommon = collect($userTypeBreakdown)->sortByDesc('count')->first();
        
        return $mostCommon['type'] ?? null;
    }
}