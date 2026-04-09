<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $last14Days = collect(range(13, 0))->map(function ($days) use ($now) {
            return $now->copy()->subDays($days)->format('Y-m-d');
        });

        // 1. KPI Stats
        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_businesses' => DB::table('businesses')->count(),
            'active_subscriptions' => DB::table('subscriptions')->where('status', 'active')->count(),
            'total_revenue' => DB::table('payments')->sum('amount'),
            'pending_approvals' => DB::table('users')->whereNull('approved_at')->count(),
            'total_articles' => DB::table('articles')->count(),
            
            // New Dashboard KPIs
            'new_users_today' => DB::table('users')->whereDate('created_at', Carbon::today())->count(),
            'mtd_sales' => DB::table('sales')->where('sold_at', '>=', $startOfMonth)->sum('total'),
            'mtd_payments' => DB::table('payments')->where('paid_at', '>=', $startOfMonth)->sum('amount'),
            'open_tickets' => DB::table('support_tickets')->where('status', 'open')->count(),
            
            'recent_businesses' => DB::table('businesses')->orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_payments' => DB::table('payments')
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->join('subscriptions', 'subscriptions.id', '=', 'invoices.subscription_id')
                ->join('users', 'users.id', '=', 'subscriptions.user_id')
                ->select('payments.*', 'users.name as user_name')
                ->orderBy('payments.paid_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        // 2. Trend Data (Last 14 Days)
        $userTrend = DB::table('users')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $now->copy()->subDays(14))
            ->groupBy('date')
            ->pluck('count', 'date');

        $paymentTrend = DB::table('payments')
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
            ->where('paid_at', '>=', $now->copy()->subDays(14))
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartData = [
            'labels' => $last14Days->values(),
            'users' => $last14Days->map(fn($date) => $userTrend->get($date, 0)),
            'payments' => $last14Days->map(fn($date) => $paymentTrend->get($date, 0)),
        ];

        // 3. Distribution (Business Types)
        $businessDist = DB::table('businesses')
            ->join('business_types', 'business_types.id', '=', 'businesses.business_type_id')
            ->select('business_types.name', DB::raw('COUNT(*) as count'))
            ->groupBy('business_types.name')
            ->get();

        return view('admin.dashboard', compact('stats', 'chartData', 'businessDist'));
    }
}
