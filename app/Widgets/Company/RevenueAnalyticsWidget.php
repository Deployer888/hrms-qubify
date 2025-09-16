<?php

namespace App\Widgets\Company;

use App\Widgets\BaseWidget;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenueAnalyticsWidget extends BaseWidget
{
    public function getData(): array
    {
        $revenueStats = $this->getRevenueStatistics();
        $monthlyRevenue = $this->getMonthlyRevenue();
        $revenueByPlan = $this->getRevenueByPlan();
        $revenueGrowth = $this->getRevenueGrowth();

        return [
            'revenue_stats' => $revenueStats,
            'monthly_revenue' => $monthlyRevenue,
            'revenue_by_plan' => $revenueByPlan,
            'revenue_growth' => $revenueGrowth,
            'summary' => [
                'total_revenue' => $revenueStats['total_revenue'],
                'monthly_revenue' => $revenueStats['monthly_revenue'],
                'growth_rate' => $revenueGrowth['percentage_change'],
                'active_subscriptions' => $revenueStats['active_subscriptions']
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view revenue', 'manage finances'];
    }

    protected function getWidgetType(): string
    {
        return 'revenue_analytics';
    }

    protected function getDefaultTitle(): string
    {
        return 'Revenue Analytics';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-chart-line';
    }

    protected function getDefaultColor(): string
    {
        return 'success';
    }

    private function getRevenueStatistics(): array
    {
        $totalRevenue = Order::where('payment_status', 'succeeded')->sum('price');
        
        $monthlyRevenue = Order::where('payment_status', 'succeeded')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('price');

        $activeSubscriptions = User::where('plan', '!=', 1)
            ->where('plan_expire_date', '>', Carbon::now())
            ->count();

        $averageOrderValue = Order::where('payment_status', 'succeeded')
            ->avg('price') ?? 0;

        return [
            'total_revenue' => round($totalRevenue, 2),
            'monthly_revenue' => round($monthlyRevenue, 2),
            'active_subscriptions' => $activeSubscriptions,
            'average_order_value' => round($averageOrderValue, 2),
            'total_orders' => Order::where('payment_status', 'succeeded')->count()
        ];
    }

    private function getMonthlyRevenue(): array
    {
        $monthlyData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $revenue = Order::where('payment_status', 'succeeded')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('price');

            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'revenue' => round($revenue, 2),
                'orders' => Order::where('payment_status', 'succeeded')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count()
            ];
        }

        return $monthlyData;
    }

    private function getRevenueByPlan(): array
    {
        return Plan::select(
                'plans.id',
                'plans.name',
                'plans.price',
                DB::raw('COUNT(users.id) as subscribers'),
                DB::raw('SUM(orders.price) as total_revenue')
            )
            ->leftJoin('users', 'plans.id', '=', 'users.plan')
            ->leftJoin('orders', function ($join) {
                $join->on('users.id', '=', 'orders.user_id')
                     ->where('orders.payment_status', '=', 'succeeded');
            })
            ->groupBy('plans.id', 'plans.name', 'plans.price')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'subscribers' => $plan->subscribers,
                    'total_revenue' => round($plan->total_revenue ?? 0, 2),
                    'color' => $this->getPlanColor($plan->id)
                ];
            })
            ->toArray();
    }

    private function getRevenueGrowth(): array
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

        // Weekly growth
        $currentWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();

        $currentWeekRevenue = Order::where('payment_status', 'succeeded')
            ->where('created_at', '>=', $currentWeek)
            ->sum('price');

        $lastWeekRevenue = Order::where('payment_status', 'succeeded')
            ->where('created_at', '>=', $lastWeek)
            ->where('created_at', '<', $currentWeek)
            ->sum('price');

        $weeklyChange = $currentWeekRevenue - $lastWeekRevenue;
        $weeklyPercentageChange = $lastWeekRevenue > 0 
            ? round(($weeklyChange / $lastWeekRevenue) * 100, 1)
            : ($currentWeekRevenue > 0 ? 100 : 0);

        return [
            'monthly' => [
                'current' => round($currentMonthRevenue, 2),
                'last' => round($lastMonthRevenue, 2),
                'change' => round($change, 2),
                'percentage_change' => $percentageChange,
                'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
            ],
            'weekly' => [
                'current' => round($currentWeekRevenue, 2),
                'last' => round($lastWeekRevenue, 2),
                'change' => round($weeklyChange, 2),
                'percentage_change' => $weeklyPercentageChange,
                'trend' => $weeklyChange > 0 ? 'up' : ($weeklyChange < 0 ? 'down' : 'stable')
            ]
        ];
    }

    private function getPlanColor(int $planId): string
    {
        $colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'
        ];
        
        return $colors[$planId % count($colors)];
    }
}