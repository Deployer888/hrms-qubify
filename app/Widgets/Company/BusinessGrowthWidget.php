<?php

namespace App\Widgets\Company;

use App\Widgets\BaseWidget;
use App\Models\User;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessGrowthWidget extends BaseWidget
{
    public function getData(): array
    {
        $growthMetrics = $this->getGrowthMetrics();
        $userGrowth = $this->getUserGrowthTrend();
        $revenueGrowth = $this->getRevenueGrowthTrend();
        $conversionMetrics = $this->getConversionMetrics();

        return [
            'growth_metrics' => $growthMetrics,
            'user_growth' => $userGrowth,
            'revenue_growth' => $revenueGrowth,
            'conversion_metrics' => $conversionMetrics,
            'summary' => [
                'total_users' => $growthMetrics['total_users'],
                'user_growth_rate' => $userGrowth['percentage_change'],
                'revenue_growth_rate' => $revenueGrowth['percentage_change'],
                'conversion_rate' => $conversionMetrics['conversion_rate']
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view business metrics'];
    }

    protected function getWidgetType(): string
    {
        return 'business_growth';
    }

    protected function getDefaultTitle(): string
    {
        return 'Business Growth';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-chart-area';
    }

    protected function getDefaultColor(): string
    {
        return 'primary';
    }

    private function getGrowthMetrics(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('plan', '!=', 1)
            ->where('plan_expire_date', '>', Carbon::now())
            ->count();

        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $totalRevenue = Order::where('payment_status', 'succeeded')->sum('price');

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'total_revenue' => round($totalRevenue, 2),
            'active_user_percentage' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0
        ];
    }

    private function getUserGrowthTrend(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthUsers = User::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        $lastMonthUsers = User::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $change = $currentMonthUsers - $lastMonthUsers;
        $percentageChange = $lastMonthUsers > 0 
            ? round(($change / $lastMonthUsers) * 100, 1)
            : ($currentMonthUsers > 0 ? 100 : 0);

        // Get monthly data for the last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userCount = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'users' => $userCount,
                'cumulative' => User::where('created_at', '<=', $date->endOfMonth())->count()
            ];
        }

        return [
            'current_month' => $currentMonthUsers,
            'last_month' => $lastMonthUsers,
            'change' => $change,
            'percentage_change' => $percentageChange,
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            'monthly_data' => $monthlyData
        ];
    }

    private function getRevenueGrowthTrend(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthRevenue = Order::where('payment_status', 'succeeded')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('price');

        $lastMonthRevenue = Order::where('payment_status', 'succeeded')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('price');

        $change = $currentMonthRevenue - $lastMonthRevenue;
        $percentageChange = $lastMonthRevenue > 0 
            ? round(($change / $lastMonthRevenue) * 100, 1)
            : ($currentMonthRevenue > 0 ? 100 : 0);

        // Get monthly revenue data for the last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Order::where('payment_status', 'succeeded')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('price');

            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'revenue' => round($revenue, 2)
            ];
        }

        return [
            'current_month' => round($currentMonthRevenue, 2),
            'last_month' => round($lastMonthRevenue, 2),
            'change' => round($change, 2),
            'percentage_change' => $percentageChange,
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            'monthly_data' => $monthlyData
        ];
    }

    private function getConversionMetrics(): array
    {
        $totalUsers = User::count();
        $paidUsers = User::where('plan', '!=', 1)->count();
        $conversionRate = $totalUsers > 0 ? round(($paidUsers / $totalUsers) * 100, 1) : 0;

        // Monthly conversion rates
        $monthlyConversions = [];
        for ($i = 2; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $monthlyTotalUsers = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $monthlyPaidUsers = User::where('plan', '!=', 1)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $monthlyConversionRate = $monthlyTotalUsers > 0 
                ? round(($monthlyPaidUsers / $monthlyTotalUsers) * 100, 1) 
                : 0;

            $monthlyConversions[] = [
                'month' => $date->format('M Y'),
                'total_users' => $monthlyTotalUsers,
                'paid_users' => $monthlyPaidUsers,
                'conversion_rate' => $monthlyConversionRate
            ];
        }

        // Plan distribution
        $planDistribution = Plan::select(
                'plans.id',
                'plans.name',
                'plans.price',
                DB::raw('COUNT(users.id) as user_count')
            )
            ->leftJoin('users', 'plans.id', '=', 'users.plan')
            ->groupBy('plans.id', 'plans.name', 'plans.price')
            ->orderBy('user_count', 'desc')
            ->get()
            ->map(function ($plan) use ($totalUsers) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'user_count' => $plan->user_count,
                    'percentage' => $totalUsers > 0 ? round(($plan->user_count / $totalUsers) * 100, 1) : 0
                ];
            })
            ->toArray();

        return [
            'total_users' => $totalUsers,
            'paid_users' => $paidUsers,
            'free_users' => $totalUsers - $paidUsers,
            'conversion_rate' => $conversionRate,
            'monthly_conversions' => $monthlyConversions,
            'plan_distribution' => $planDistribution
        ];
    }
}