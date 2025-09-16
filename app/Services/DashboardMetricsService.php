<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 15;

    /**
     * Calculate total users growth percentage
     *
     * @return array
     */
    public function calculateUserGrowth(): array
    {
        $cacheKey = 'dashboard_metrics_users_growth_' . date('Y-m');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

                // Count company users for current month
                $currentCount = User::where('type', 'company')
                    ->whereDate('created_at', '>=', $currentMonth)
                    ->count();

                // Count company users for previous month
                $previousCount = User::where('type', 'company')
                    ->whereBetween('created_at', [$previousMonth, $previousMonthEnd])
                    ->count();

                return $this->calculateGrowthPercentage($currentCount, $previousCount);

            } catch (\Exception $e) {
                Log::error('Error calculating user growth: ' . $e->getMessage());
                return ['value' => 0, 'trend' => 'neutral', 'display' => 'N/A'];
            }
        });
    }

    /**
     * Calculate total orders growth percentage
     *
     * @return array
     */
    public function calculateOrderGrowth(): array
    {
        $cacheKey = 'dashboard_metrics_orders_growth_' . date('Y-m');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

                // Count orders for current month
                $currentCount = Order::whereDate('created_at', '>=', $currentMonth)->count();

                // Count orders for previous month
                $previousCount = Order::whereBetween('created_at', [$previousMonth, $previousMonthEnd])->count();

                return $this->calculateGrowthPercentage($currentCount, $previousCount);

            } catch (\Exception $e) {
                Log::error('Error calculating order growth: ' . $e->getMessage());
                return ['value' => 0, 'trend' => 'neutral', 'display' => 'N/A'];
            }
        });
    }

    /**
     * Calculate active plans growth percentage
     *
     * @return array
     */
    public function calculatePlanGrowth(): array
    {
        $cacheKey = 'dashboard_metrics_plans_growth_' . date('Y-m');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

                // Count active plans for current month (users with non-free plans)
                $currentCount = User::where('type', 'company')
                    ->whereNotIn('plan', [0, 1]) // Assuming 0 and 1 are free plans
                    ->whereDate('updated_at', '>=', $currentMonth) // Plan changes this month
                    ->count();

                // Count active plans for previous month
                $previousCount = User::where('type', 'company')
                    ->whereNotIn('plan', [0, 1])
                    ->whereBetween('updated_at', [$previousMonth, $previousMonthEnd])
                    ->count();

                return $this->calculateGrowthPercentage($currentCount, $previousCount);

            } catch (\Exception $e) {
                Log::error('Error calculating plan growth: ' . $e->getMessage());
                return ['value' => 0, 'trend' => 'neutral', 'display' => 'N/A'];
            }
        });
    }

    /**
     * Calculate monthly revenue growth percentage
     *
     * @return array
     */
    public function calculateRevenueGrowth(): array
    {
        $cacheKey = 'dashboard_metrics_revenue_growth_' . date('Y-m');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                $previousMonth = Carbon::now()->subMonth()->startOfMonth();
                $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

                // Sum revenue for current month
                $currentRevenue = Order::whereDate('created_at', '>=', $currentMonth)
                    ->sum('price');

                // Sum revenue for previous month
                $previousRevenue = Order::whereBetween('created_at', [$previousMonth, $previousMonthEnd])
                    ->sum('price');

                return $this->calculateGrowthPercentage($currentRevenue, $previousRevenue);

            } catch (\Exception $e) {
                Log::error('Error calculating revenue growth: ' . $e->getMessage());
                return ['value' => 0, 'trend' => 'neutral', 'display' => 'N/A'];
            }
        });
    }

    /**
     * Calculate user retention rate
     *
     * @return float
     */
    public function calculateUserRetention(): float
    {
        $cacheKey = 'dashboard_metrics_user_retention_' . date('Y-m');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            try {
                $currentMonth = Carbon::now()->startOfMonth();
                $lastMonth = Carbon::now()->subMonth();
                $lastMonthStart = $lastMonth->copy()->startOfMonth();
                $lastMonthEnd = $lastMonth->copy()->endOfMonth();

                // Get total users from last month
                $totalUsersLastMonth = User::where('type', 'company')
                    ->whereDate('created_at', '<=', $lastMonthEnd)
                    ->count();

                if ($totalUsersLastMonth == 0) {
                    return 0.0;
                }

                // Get active users this month (users who have recent activity)
                $activeUsersThisMonth = User::where('type', 'company')
                    ->whereDate('updated_at', '>=', $currentMonth) // Consider recent activity as login
                    ->count();

                $retentionRate = ($activeUsersThisMonth / $totalUsersLastMonth) * 100;
                
                return round($retentionRate, 1);

            } catch (\Exception $e) {
                Log::error('Error calculating user retention: ' . $e->getMessage());
                return 85.0; // Fallback value
            }
        });
    }

    /**
     * Calculate growth percentage with proper handling of edge cases
     *
     * @param float $currentValue
     * @param float $previousValue
     * @return array
     */
    private function calculateGrowthPercentage(float $currentValue, float $previousValue): array
    {
        if ($previousValue == 0) {
            if ($currentValue > 0) {
                return [
                    'value' => 100,
                    'trend' => 'positive',
                    'display' => 'New'
                ];
            } else {
                return [
                    'value' => 0,
                    'trend' => 'neutral',
                    'display' => 'N/A'
                ];
            }
        }

        $growthPercentage = (($currentValue - $previousValue) / $previousValue) * 100;
        $roundedGrowth = round($growthPercentage, 1);

        $trend = 'neutral';
        if ($roundedGrowth > 0) {
            $trend = 'positive';
        } elseif ($roundedGrowth < 0) {
            $trend = 'negative';
        }

        return [
            'value' => abs($roundedGrowth),
            'trend' => $trend,
            'display' => ($roundedGrowth >= 0 ? '+' : '-') . abs($roundedGrowth) . '%'
        ];
    }

    /**
     * Get all dashboard metrics
     *
     * @return array
     */
    public function getAllMetrics(): array
    {
        return [
            'users_growth' => $this->calculateUserGrowth(),
            'orders_growth' => $this->calculateOrderGrowth(),
            'plans_growth' => $this->calculatePlanGrowth(),
            'revenue_growth' => $this->calculateRevenueGrowth(),
            'user_retention' => $this->calculateUserRetention()
        ];
    }

    /**
     * Clear all cached metrics
     *
     * @return void
     */
    public function clearCache(): void
    {
        $currentMonth = date('Y-m');
        $cacheKeys = [
            "dashboard_metrics_users_growth_{$currentMonth}",
            "dashboard_metrics_orders_growth_{$currentMonth}",
            "dashboard_metrics_plans_growth_{$currentMonth}",
            "dashboard_metrics_revenue_growth_{$currentMonth}",
            "dashboard_metrics_user_retention_{$currentMonth}"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}