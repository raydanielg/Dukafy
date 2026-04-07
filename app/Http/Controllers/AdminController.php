<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $stats = [
            'total_users' => DB::table('users')->count(),
            'total_businesses' => DB::table('businesses')->count(),
            'active_subscriptions' => DB::table('subscriptions')->where('status', 'active')->count(),
            'total_revenue' => DB::table('payments')->sum('amount'),
            'pending_approvals' => DB::table('users')->whereNull('approved_at')->count(),
            'total_articles' => DB::table('articles')->count(),
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

        return view('admin.dashboard', compact('stats'));
    }
}
