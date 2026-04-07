<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businessId = $user->business_id;

        // Kama user hana business, mpeleke kuunda biashara au onyesha empty dashboard
        if (!$businessId) {
            return view('user.dashboard.no_business');
        }

        // Metrics (Sample queries - adjust based on your needs)
        $stats = [
            'shops_count' => DB::table('branches')->where('business_id', $businessId)->count(),
            'members_count' => DB::table('users')->where('business_id', $businessId)->count(),
            'customers_count' => DB::table('customers')->where('business_id', $businessId)->count(),
            'products_count' => DB::table('products')->where('business_id', $businessId)->count(),
            
            // Financial Metrics (Sample placeholders for loans/repayments)
            'active_borrowers_val' => 0.00,
            'pending_loans_val' => 0.00,
            'overdue_val' => 0.00,
            'today_disbursed' => 0.00,
            'today_collected' => 0.00,
            'repayment_rate' => 0.00,
        ];

        return view('user.dashboard.index', compact('user', 'stats'));
    }
}
