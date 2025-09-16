<?php

namespace App\Widgets\Company;

use App\Widgets\BaseWidget;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlanPerformanceWidget extends BaseWidget
{
    public function getData(): array
    {
        $planStats = $this->getPlanStatistics();
        $planRevenue = $this->getPlanRevenue();
        $subscriptionTrends = $this->getSubscriptionTrends();
        $planConversions = $this->getPlanConversions();

        return [
            'plan_stats' => $planStats,
            'plan_revenue' => $planRevenue,
            'subscription_trends' => $subscriptionTrends,
            'plan_conversions' => $planConversions,
            'summary' => [
                'total_plans' => count($planStats),
                'total_subscribers' => array_sum(array_column($planStats, 'subscribers')),
                'most_popular_plan' => $this->getMostPopularPlan($planStats),
                'highest_revenue_plan' => $this->getHighestRevenuePlan($planRevenue)
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view plan metrics'];
    }

    protected function getWidgetType(): string
    {
        return 'plan_performance';
    }

    protected function getDefaultTitle(): string
    {
        return 'Plan Performance';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-layer-group';
    }

    protected function getDefaultColor(): string
    {
        return 'warning';
    }

    private function getPlanStatistics(): array
    {
        return Plan::select(
                'plans.id',
                'plans.name',
                'plans.price',
                'plans.duration',
                DB::raw('COUNT(users.id) as subscribers'),
                DB::raw('COUNT(CASE WHEN users.plan_expire_date > NOW() THEN 1 END) as active_subscribers'),
                DB::raw('COUNT(CASE WHEN users.plan_expire_date <= NOW() THEN 1 END) as expired_subscribers')
            )
            ->leftJoin('users', 'plans.id', '=', 'users.plan')
            ->groupBy('plans.id', 'plans.name', 'plans.price', 'plans.duration')
            ->orderBy('subscribers', 'desc')
            ->get()
            ->map(function ($plan) {
                $totalUsers = User::count();
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'subscribers' => $plan->subscribers,
                    'active_subscribers' => $plan->active_subscribers,
                    'expired_subscribers' => $plan->expired_subscribers,
                    'market_share' => $totalUsers > 0 ? round(($plan->subscribers / $totalUsers) * 100, 1) : 0,
                    'retention_rate' => $plan->subscribers > 0 ? round(($plan->active_subscribers / $plan->subscribers) * 100, 1) : 0,
                    'color' => $this->getPlanColor($plan->id)
                ];
            })
            ->toArray();
    }

    private function getPlanRevenue(): array
    {
        return Plan::select(
                'plans.id',
                'plans.name',
                'plans.price',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.price) as total_revenue'),
                DB::raw('SUM(CASE WHEN orders.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN orders.price ELSE 0 END) as monthly_revenue')
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
                    'total_orders' => $plan->total_orders,
                    'total_revenue' => round($plan->total_revenue ?? 0, 2),
                    'monthly_revenue' => round($plan->monthly_revenue ?? 0, 2),
                    'average_order_value' => $plan->total_orders > 0 ? round(($plan->total_revenue ?? 0) / $plan->total_orders, 2) : 0,
                    'color' => $this->getPlanColor($plan->id)
                ];
            })
            ->toArray();
    }

    private function getSubscriptionTrends(): array
    {
        $monthlyTrends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $newSubscriptions = User::where('plan', '!=', 1)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $expiredSubscriptions = User::where('plan', '!=', 1)
                ->whereMonth('plan_expire_date', $date->month)
                ->whereYear('plan_expire_date', $date->year)
                ->where('plan_expire_date', '<=', Carbon::now())
                ->count();

            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                'new_subscriptions' => $newSubscriptions,
                'expired_subscriptions' => $expiredSubscriptions,
                'net_growth' => $newSubscriptions - $expiredSubscriptions
            ];
        }

        // Current vs last month comparison
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthSubs = User::where('plan', '!=', 1)
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        $lastMonthSubs = User::where('plan', '!=', 1)
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $change = $currentMonthSubs - $lastMonthSubs;
        $percentageChange = $lastMonthSubs > 0 
            ? round(($change / $lastMonthSubs) * 100, 1)
            : ($currentMonthSubs > 0 ? 100 : 0);

        return [
            'monthly_trends' => $monthlyTrends,
            'current_month' => $currentMonthSubs,
            'last_month' => $lastMonthSubs,
            'change' => $change,
            'percentage_change' => $percentageChange,
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
        ];
    }

    private function getPlanConversions(): array
    {
        $totalUsers = User::count();
        $freeUsers = User::where('plan', 1)->count();
        $paidUsers = User::where('plan', '!=', 1)->count();

        $conversionsByPlan = Plan::select(
                'plans.id',
                'plans.name',
                'plans.price',
                DB::raw('COUNT(users.id) as conversions')
            )
            ->leftJoin('users', 'plans.id', '=', 'users.plan')
            ->where('plans.id', '!=', 1) // Exclude free plan
            ->groupBy('plans.id', 'plans.name', 'plans.price')
            ->orderBy('conversions', 'desc')
            ->get()
            ->map(function ($plan) use ($totalUsers) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'conversions' => $plan->conversions,
                    'conversion_rate' => $totalUsers > 0 ? round(($plan->conversions / $totalUsers) * 100, 1) : 0,
                    'color' => $this->getPlanColor($plan->id)
                ];
            })
            ->toArray();

        return [
            'total_users' => $totalUsers,
            'free_users' => $freeUsers,
            'paid_users' => $paidUsers,
            'overall_conversion_rate' => $totalUsers > 0 ? round(($paidUsers / $totalUsers) * 100, 1) : 0,
            'conversions_by_plan' => $conversionsByPlan
        ];
    }

    private function getMostPopularPlan(array $planStats): ?array
    {
        if (empty($planStats)) {
            return null;
        }

        $mostPopular = collect($planStats)->sortByDesc('subscribers')->first();
        
        return [
            'name' => $mostPopular['name'],
            'subscribers' => $mostPopular['subscribers']
        ];
    }

    private function getHighestRevenuePlan(array $planRevenue): ?array
    {
        if (empty($planRevenue)) {
            return null;
        }

        $highestRevenue = collect($planRevenue)->sortByDesc('total_revenue')->first();
        
        return [
            'name' => $highestRevenue['name'],
            'revenue' => $highestRevenue['total_revenue']
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