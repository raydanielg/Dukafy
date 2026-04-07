<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function systemUsage()
    {
        $days = 30;
        $dateLimit = Carbon::now()->subDays($days);

        // Stats
        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_businesses' => DB::table('businesses')->count(),
            'total_articles' => DB::table('articles')->count(),
            'total_newsletters' => DB::table('newsletters')->count(),
            'new_users_30d' => DB::table('users')->where('created_at', '>=', $dateLimit)->count(),
            'new_businesses_30d' => DB::table('businesses')->where('created_at', '>=', $dateLimit)->count(),
        ];

        // Chart: User Registrations (last 30 days)
        $userTrend = DB::table('users')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $dateLimit)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Chart: Business Registrations (last 30 days)
        $businessTrend = DB::table('businesses')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $dateLimit)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.system_usage', compact('stats', 'userTrend', 'businessTrend'));
    }

    public function businessPerformance()
    {
        $dateLimit = Carbon::now()->subDays(30);

        // Top Businesses by Revenue
        $topBusinesses = DB::table('sales')
            ->join('businesses', 'businesses.id', '=', 'sales.business_id')
            ->select('businesses.name', DB::raw('SUM(sales.total) as revenue'), DB::raw('COUNT(*) as sales_count'))
            ->groupBy('businesses.id', 'businesses.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Sales Trend (last 30 days)
        $salesTrend = DB::table('sales')
            ->select(DB::raw('DATE(sold_at) as date'), DB::raw('SUM(total) as total'))
            ->where('sold_at', '>=', $dateLimit)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Product categories distribution
        $categoryDist = DB::table('products')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->select('product_categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('product_categories.id', 'product_categories.name')
            ->get();

        return view('admin.reports.business_performance', compact('topBusinesses', 'salesTrend', 'categoryDist'));
    }

    public function subscriptionRevenue()
    {
        $dateLimit = Carbon::now()->subMonths(6);

        // Revenue Trend (last 6 months)
        $revenueTrend = DB::table('payments')
            ->select(DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"), DB::raw('SUM(amount) as total'))
            ->where('paid_at', '>=', $dateLimit)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Plan Distribution
        $planDist = DB::table('subscriptions')
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->select('plans.name', DB::raw('COUNT(*) as count'))
            ->where('subscriptions.status', 'active')
            ->groupBy('plans.id', 'plans.name')
            ->get();

        // Summary Stats
        $stats = [
            'total_revenue' => DB::table('payments')->sum('amount'),
            'unpaid_invoices' => DB::table('invoices')->where('status', 'unpaid')->sum('amount'),
            'active_subscriptions' => DB::table('subscriptions')->where('status', 'active')->count(),
        ];

        return view('admin.reports.subscription_revenue', compact('revenueTrend', 'planDist', 'stats'));
    }

    public function churnReport()
    {
        $stats = [
            'active' => DB::table('subscriptions')->where('status', 'active')->count(),
            'cancelled' => DB::table('subscriptions')->where('status', 'cancelled')->count(),
            'expired' => DB::table('subscriptions')->where('status', 'expired')->count(),
            'trial' => DB::table('subscriptions')->where('status', 'trial')->count(),
        ];

        // Monthly Churn (last 6 months) - based on cancelled_at
        $churnTrend = DB::table('subscriptions')
            ->select(DB::raw("DATE_FORMAT(cancelled_at, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
            ->whereNotNull('cancelled_at')
            ->where('cancelled_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // New Subscriptions Trend
        $newSubTrend = DB::table('subscriptions')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.churn_report', compact('stats', 'churnTrend', 'newSubTrend'));
    }
}
