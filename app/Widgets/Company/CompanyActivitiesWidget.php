<?php

namespace App\Widgets\Company;

use App\Widgets\BaseWidget;
use App\Models\User;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompanyActivitiesWidget extends BaseWidget
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
                'new_users_today' => $this->getNewUsersToday(),
                'orders_today' => $this->getOrdersToday(),
                'recent_count' => count($recentActivities)
            ]
        ];
    }

    public function getPermissions(): array
    {
        return ['view company activities'];
    }

    protected function getWidgetType(): string
    {
        return 'company_activities';
    }

    protected function getDefaultTitle(): string
    {
        return 'Company Activities';
    }

    protected function getDefaultIcon(): string
    {
        return 'fas fa-building';
    }

    protected function getDefaultColor(): string
    {
        return 'secondary';
    }

    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent user registrations
        $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($newUsers as $user) {
            $activities[] = [
                'type' => 'user_registration',
                'title' => 'New User Registration',
                'description' => "User {$user->name} registered as {$user->type}",
                'timestamp' => $user->created_at,
                'icon' => 'fas fa-user-plus',
                'color' => '#10b981',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_type' => $user->type,
                    'email' => $user->email
                ]
            ];
        }

        // Recent orders
        $recentOrders = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'type' => 'order_placed',
                'title' => 'New Order',
                'description' => "{$order->user->name} placed an order for \${$order->price}",
                'timestamp' => $order->created_at,
                'icon' => 'fas fa-shopping-cart',
                'color' => $this->getOrderStatusColor($order->payment_status),
                'data' => [
                    'order_id' => $order->id,
                    'user_name' => $order->user->name,
                    'amount' => $order->price,
                    'status' => $order->payment_status,
                    'plan_id' => $order->plan_id
                ]
            ];
        }

        // Recent plan upgrades
        $recentUpgrades = User::where('plan', '!=', 1)
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->where('updated_at', '!=', DB::raw('created_at'))
            ->with(['planDetails:id,name,price'])
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentUpgrades as $user) {
            $activities[] = [
                'type' => 'plan_upgrade',
                'title' => 'Plan Upgrade',
                'description' => "{$user->name} upgraded to {$user->planDetails->name ?? 'Premium'} plan",
                'timestamp' => $user->updated_at,
                'icon' => 'fas fa-arrow-up',
                'color' => '#3b82f6',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'plan_name' => $user->planDetails->name ?? 'Premium',
                    'plan_price' => $user->planDetails->price ?? 0
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
                'new_users' => User::whereDate('created_at', $today)->count(),
                'new_orders' => Order::whereDate('created_at', $today)->count(),
                'revenue' => Order::whereDate('created_at', $today)
                    ->where('payment_status', 'succeeded')
                    ->sum('price'),
                'plan_upgrades' => User::where('plan', '!=', 1)
                    ->whereDate('updated_at', $today)
                    ->where('updated_at', '!=', DB::raw('created_at'))
                    ->count()
            ],
            'this_week' => [
                'new_users' => User::where('created_at', '>=', $thisWeek)->count(),
                'new_orders' => Order::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => Order::where('created_at', '>=', $thisWeek)
                    ->where('payment_status', 'succeeded')
                    ->sum('price'),
                'plan_upgrades' => User::where('plan', '!=', 1)
                    ->where('updated_at', '>=', $thisWeek)
                    ->where('updated_at', '!=', DB::raw('created_at'))
                    ->count()
            ],
            'this_month' => [
                'new_users' => User::where('created_at', '>=', $thisMonth)->count(),
                'new_orders' => Order::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => Order::where('created_at', '>=', $thisMonth)
                    ->where('payment_status', 'succeeded')
                    ->sum('price'),
                'plan_upgrades' => User::where('plan', '!=', 1)
                    ->where('updated_at', '>=', $thisMonth)
                    ->where('updated_at', '!=', DB::raw('created_at'))
                    ->count()
            ]
        ];
    }

    private function getTodayActivities(): array
    {
        $today = Carbon::today();
        $activities = [];

        // Today's user registrations
        $todayUsers = User::whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($todayUsers as $user) {
            $activities[] = [
                'type' => 'user_registration_today',
                'title' => 'User Registration',
                'description' => "{$user->name} registered as {$user->type}",
                'timestamp' => $user->created_at,
                'icon' => 'fas fa-user-plus',
                'color' => '#10b981',
                'data' => [
                    'user_name' => $user->name,
                    'user_type' => $user->type,
                    'email' => $user->email
                ]
            ];
        }

        // Today's orders
        $todayOrders = Order::whereDate('created_at', $today)
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($todayOrders as $order) {
            $activities[] = [
                'type' => 'order_today',
                'title' => 'Order Placed',
                'description' => "{$order->user->name} placed order for \${$order->price}",
                'timestamp' => $order->created_at,
                'icon' => 'fas fa-shopping-cart',
                'color' => $this->getOrderStatusColor($order->payment_status),
                'data' => [
                    'user_name' => $order->user->name,
                    'amount' => $order->price,
                    'status' => $order->payment_status
                ]
            ];
        }

        // Today's plan upgrades
        $todayUpgrades = User::where('plan', '!=', 1)
            ->whereDate('updated_at', $today)
            ->where('updated_at', '!=', DB::raw('created_at'))
            ->with(['planDetails:id,name'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($todayUpgrades as $user) {
            $activities[] = [
                'type' => 'plan_upgrade_today',
                'title' => 'Plan Upgrade',
                'description' => "{$user->name} upgraded to {$user->planDetails->name ?? 'Premium'}",
                'timestamp' => $user->updated_at,
                'icon' => 'fas fa-arrow-up',
                'color' => '#3b82f6',
                'data' => [
                    'user_name' => $user->name,
                    'plan_name' => $user->planDetails->name ?? 'Premium'
                ]
            ];
        }

        // Sort by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });

        return $activities;
    }

    private function getNewUsersToday(): int
    {
        return User::whereDate('created_at', Carbon::today())->count();
    }

    private function getOrdersToday(): int
    {
        return Order::whereDate('created_at', Carbon::today())->count();
    }

    private function getOrderStatusColor(string $status): string
    {
        switch ($status) {
            case 'succeeded':
                return '#10b981';
            case 'pending':
                return '#f59e0b';
            case 'failed':
                return '#ef4444';
            default:
                return '#6b7280';
        }
    }
}