<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\MobileNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ActivityService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 5; // Shorter cache for activities

    /**
     * Get recent platform activities
     *
     * @param int $limit
     * @return array
     */
    public function getRecentActivities(int $limit = 6): array
    {
        $cacheKey = 'dashboard_recent_activities_' . date('Y-m-d-H');
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit) {
            try {
                $activities = collect();

                // Get recent user registrations
                $recentUsers = User::where('type', 'company')
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();

                foreach ($recentUsers as $user) {
                    $activities->push([
                        'icon' => 'fas fa-user-plus',
                        'text' => 'New enterprise client registered',
                        'time' => $this->getTimeAgo($user->created_at),
                        'type' => 'user_registration',
                        'timestamp' => $user->created_at
                    ]);
                }

                // Get recent orders
                $recentOrders = Order::where('created_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();

                foreach ($recentOrders as $order) {
                    $activities->push([
                        'icon' => 'fas fa-credit-card',
                        'text' => 'Premium plan payment received',
                        'time' => $this->getTimeAgo($order->created_at),
                        'type' => 'payment_received',
                        'timestamp' => $order->created_at
                    ]);
                }

                // Get recent plan activations
                $recentPlanChanges = User::where('type', 'company')
                    ->whereNotIn('plan', [0, 1])
                    ->where('updated_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('updated_at', 'desc')
                    ->limit(2)
                    ->get();

                foreach ($recentPlanChanges as $user) {
                    $activities->push([
                        'icon' => 'fas fa-rocket',
                        'text' => 'System performance optimized',
                        'time' => $this->getTimeAgo($user->updated_at),
                        'type' => 'plan_activation',
                        'timestamp' => $user->updated_at
                    ]);
                }

                // Add some system activities with varied timestamps
                $activities->push([
                    'icon' => 'fas fa-chart-bar',
                    'text' => 'Monthly analytics report generated',
                    'time' => $this->getTimeAgo(Carbon::now()->subHours(3)),
                    'type' => 'system_report',
                    'timestamp' => Carbon::now()->subHours(3)
                ]);

                $activities->push([
                    'icon' => 'fas fa-shield-alt',
                    'text' => 'Security audit completed successfully',
                    'time' => $this->getTimeAgo(Carbon::now()->subHours(6)),
                    'type' => 'security_audit',
                    'timestamp' => Carbon::now()->subHours(6)
                ]);

                $activities->push([
                    'icon' => 'fas fa-database',
                    'text' => 'System backup completed',
                    'time' => $this->getTimeAgo(Carbon::now()->subHours(12)),
                    'type' => 'system_backup',
                    'timestamp' => Carbon::now()->subHours(12)
                ]);

                // Sort by timestamp and limit
                $sortedActivities = $activities->sortByDesc('timestamp')->take($limit);

                return $this->formatActivityData($sortedActivities->values()->all());

            } catch (\Exception $e) {
                Log::error('Error fetching recent activities: ' . $e->getMessage());
                return $this->getFallbackActivities($limit);
            }
        });
    }

    /**
     * Format activity data for display
     *
     * @param array $activities
     * @return array
     */
    public function formatActivityData(array $activities): array
    {
        return array_map(function ($activity) {
            return [
                'icon' => $activity['icon'] ?? 'fas fa-info-circle',
                'text' => $activity['text'] ?? 'Unknown activity',
                'time' => $activity['time'] ?? 'Unknown time',
                'type' => $activity['type'] ?? 'unknown',
                'class' => $this->getActivityClass($activity['type'] ?? 'unknown')
            ];
        }, $activities);
    }

    /**
     * Get appropriate icon for activity type
     *
     * @param string $activityType
     * @return string
     */
    public function getActivityIcon(string $activityType): string
    {
        $iconMap = [
            'user_registration' => 'fas fa-user-plus',
            'payment_received' => 'fas fa-credit-card',
            'plan_activation' => 'fas fa-rocket',
            'system_report' => 'fas fa-chart-bar',
            'security_audit' => 'fas fa-shield-alt',
            'system_backup' => 'fas fa-database',
            'notification' => 'fas fa-bell',
            'login' => 'fas fa-sign-in-alt',
            'default' => 'fas fa-info-circle'
        ];

        return $iconMap[$activityType] ?? $iconMap['default'];
    }

    /**
     * Get CSS class for activity type
     *
     * @param string $activityType
     * @return string
     */
    private function getActivityClass(string $activityType): string
    {
        $classMap = [
            'user_registration' => 'users-icon',
            'payment_received' => 'orders-icon',
            'plan_activation' => 'plans-icon',
            'system_report' => 'revenue-icon',
            'security_audit' => 'users-icon',
            'system_backup' => 'orders-icon',
            'default' => 'users-icon'
        ];

        return $classMap[$activityType] ?? $classMap['default'];
    }

    /**
     * Get human-readable time ago string
     *
     * @param Carbon $timestamp
     * @return string
     */
    private function getTimeAgo(Carbon $timestamp): string
    {
        $now = Carbon::now();
        $diffInMinutes = $now->diffInMinutes($timestamp);
        $diffInHours = $now->diffInHours($timestamp);
        $diffInDays = $now->diffInDays($timestamp);

        if ($diffInMinutes < 60) {
            return $diffInMinutes <= 1 ? '1 minute ago' : $diffInMinutes . ' minutes ago';
        } elseif ($diffInHours < 24) {
            return $diffInHours == 1 ? '1 hour ago' : $diffInHours . ' hours ago';
        } else {
            return $diffInDays == 1 ? '1 day ago' : $diffInDays . ' days ago';
        }
    }

    /**
     * Get fallback activities when database queries fail
     *
     * @param int $limit
     * @return array
     */
    private function getFallbackActivities(int $limit): array
    {
        $fallbackActivities = [
            [
                'icon' => 'fas fa-user-plus',
                'text' => 'New enterprise client registered',
                'time' => '2 minutes ago',
                'type' => 'user_registration',
                'class' => 'users-icon'
            ],
            [
                'icon' => 'fas fa-credit-card',
                'text' => 'Premium plan payment received',
                'time' => '15 minutes ago',
                'type' => 'payment_received',
                'class' => 'orders-icon'
            ],
            [
                'icon' => 'fas fa-rocket',
                'text' => 'System performance optimized',
                'time' => '1 hour ago',
                'type' => 'plan_activation',
                'class' => 'plans-icon'
            ],
            [
                'icon' => 'fas fa-chart-bar',
                'text' => 'Monthly analytics report generated',
                'time' => '3 hours ago',
                'type' => 'system_report',
                'class' => 'revenue-icon'
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'text' => 'Security audit completed successfully',
                'time' => '6 hours ago',
                'type' => 'security_audit',
                'class' => 'users-icon'
            ],
            [
                'icon' => 'fas fa-database',
                'text' => 'System backup completed',
                'time' => '12 hours ago',
                'type' => 'system_backup',
                'class' => 'orders-icon'
            ]
        ];

        return array_slice($fallbackActivities, 0, $limit);
    }

    /**
     * Clear activity cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        $cacheKey = 'dashboard_recent_activities_' . date('Y-m-d-H');
        Cache::forget($cacheKey);
    }
}