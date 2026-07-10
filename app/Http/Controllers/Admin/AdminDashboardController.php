<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\TeamMatch;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'orders' => Order::count(),
            'revenue' => Order::whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_grosze'),
            'products' => Product::count(),
            'matches' => TeamMatch::count(),
            'ordersPending' => Order::where('status', 'pending_payment')->count(),
            'ordersShipped' => Order::where('status', 'shipped')->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentMatches = TeamMatch::latest()
            ->take(5)
            ->get();

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'recentMatches',
            'ordersByStatus'
        ));
    }
}
