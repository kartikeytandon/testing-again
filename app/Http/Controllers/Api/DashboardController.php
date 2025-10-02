<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'users' => User::count(),
            'orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'attendance_today' => Attendance::whereDate('attendance_date', today())->count(),
            'sales_this_month' => SalesReport::whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)
                ->sum('order_value') ?? 0,
        ];

        // Role-based filtering
        if ($user->isClient()) {
            $stats['orders'] = Order::where('client_id', $user->id)->count();
            $stats['pending_orders'] = Order::where('client_id', $user->id)
                ->where('status', 'pending')->count();
        }

        if ($user->isSalesPerson()) {
            $stats['sales_this_month'] = SalesReport::where('sales_person_id', $user->id)
                ->whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)
                ->sum('order_value') ?? 0;
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getOverview(Request $request)
    {
        $user = $request->user();
        
        $overview = [
            'recent_orders' => Order::with('client')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'upcoming_tasks' => Task::with('user')
                ->where('status', 'pending')
                ->where('due_date', '>=', today())
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get(),
            'attendance_summary' => [
                'present_today' => Attendance::whereDate('attendance_date', today())
                    ->where('status', 'present')
                    ->count(),
                'absent_today' => Attendance::whereDate('attendance_date', today())
                    ->where('status', 'absent')
                    ->count(),
            ],
            'sales_trends' => SalesReport::select(
                DB::raw('DATE(visit_date) as date'),
                DB::raw('SUM(order_value) as total_sales')
            )
            ->where('visit_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
        ];

        // Role-based filtering
        if ($user->isClient()) {
            $overview['recent_orders'] = Order::with('client')
                ->where('client_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        if ($user->isSalesPerson()) {
            $overview['sales_trends'] = SalesReport::select(
                DB::raw('DATE(visit_date) as date'),
                DB::raw('SUM(order_value) as total_sales')
            )
            ->where('sales_person_id', $user->id)
            ->where('visit_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $overview
        ]);
    }

    public function getRecentActivity(Request $request)
    {
        $activities = [];

        // Recent orders
        $recentOrders = Order::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'id' => $order->id,
                'type' => 'order_created',
                'description' => "New order created by {$order->client->name}",
                'timestamp' => $order->created_at,
                'user' => [
                    'id' => $order->client->id,
                    'name' => $order->client->name
                ]
            ];
        }

        // Recent tasks
        $recentTasks = Task::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentTasks as $task) {
            $activities[] = [
                'id' => $task->id,
                'type' => 'task_created',
                'description' => "New task '{$task->task_title}' assigned to {$task->user->name}",
                'timestamp' => $task->created_at,
                'user' => [
                    'id' => $task->user->id,
                    'name' => $task->user->name
                ]
            ];
        }

        // Sort by timestamp and limit
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $activities = array_slice($activities, 0, 20);

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $activities
            ]
        ]);
    }
}
