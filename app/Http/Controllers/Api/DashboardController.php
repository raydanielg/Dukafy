<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get all dashboard data in one request
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->business_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business associated'
                ], 404);
            }

            $businessId = $user->business_id;
            $today = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();

            // Get all metrics with individual try-catch for each
            $data = [
                'stock_in' => $this->safeCall(fn() => $this->getInventoryValue($businessId)),
                'profit' => $this->safeCall(fn() => $this->getProfit($businessId)),
                'orders' => $this->safeCall(fn() => $this->getOrderCount($businessId)),
                'credits' => $this->safeCall(fn() => $this->getOutstandingCredits($businessId)),
                'expenses' => $this->safeCall(fn() => $this->getTotalExpenses($businessId)),
                'sales' => $this->safeCall(fn() => $this->getTotalSales($businessId)),
                'balance' => $this->safeCall(fn() => $this->getCurrentBalance($businessId)),
                'today_sales' => $this->safeCall(fn() => $this->getTodaySales($businessId, $today)),
                'month_sales' => $this->safeCall(fn() => $this->getMonthSales($businessId, $startOfMonth)),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'data' => [
                    'stock_in' => 0, 'profit' => 0, 'orders' => 0,
                    'credits' => 0, 'expenses' => 0, 'sales' => 0,
                    'balance' => 0, 'today_sales' => 0, 'month_sales' => 0,
                ]
            ], 500);
        }
    }

    /**
     * Safely execute a callback and return 0 on failure
     */
    private function safeCall(callable $callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            \Log::warning('Dashboard metric error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get dashboard stats summary
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['success' => false, 'message' => 'No business associated'], 404);
        }

        $businessId = $user->business_id;

        return response()->json([
            'success' => true,
            'data' => [
                'profit' => $this->getProfit($businessId),
                'sales' => $this->getTotalSales($businessId),
                'balance' => $this->getCurrentBalance($businessId),
            ]
        ]);
    }

    /**
     * Get dashboard summary
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['success' => false, 'message' => 'No business associated'], 404);
        }

        $businessId = $user->business_id;

        return response()->json([
            'success' => true,
            'data' => [
                'profit' => $this->getProfit($businessId),
                'balance' => $this->getCurrentBalance($businessId),
            ]
        ]);
    }

    /**
     * Get today's sales
     */
    public function todaySales(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['success' => false, 'message' => 'No business associated'], 404);
        }

        $businessId = $user->business_id;
        $today = Carbon::today();

        $todaySales = DB::table('sales')
            ->where('business_id', $businessId)
            ->whereDate('sold_at', $today)
            ->sum('total');

        $orderCount = DB::table('sales')
            ->where('business_id', $businessId)
            ->whereDate('sold_at', $today)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'today' => (int) $todaySales,
                'order_count' => $orderCount,
            ]
        ]);
    }

    /**
     * Get inventory value
     */
    public function inventoryValue(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['success' => false, 'message' => 'No business associated'], 404);
        }

        $businessId = $user->business_id;

        return response()->json([
            'success' => true,
            'data' => [
                'total_value' => $this->getInventoryValue($businessId),
                'stock_value' => $this->getInventoryValue($businessId),
            ]
        ]);
    }

    /**
     * Get outstanding credits
     */
    public function outstandingCredits(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['success' => false, 'message' => 'No business associated'], 404);
        }

        $businessId = $user->business_id;

        // Assuming credits are tracked in sales with payment_method = 'credit'
        $credits = DB::table('sales')
            ->where('business_id', $businessId)
            ->where('payment_method', 'credit')
            ->sum('total');

        return response()->json([
            'success' => true,
            'data' => [
                'total_outstanding' => (int) $credits,
                'amount' => (int) $credits,
            ]
        ]);
    }

    /**
     * Get total expenses
     */
    public function totalExpenses(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['success' => false, 'message' => 'No business associated'], 404);
        }

        $businessId = $user->business_id;

        // Sum of all expenses for this business
        $expenses = DB::table('expenses')
            ->where('business_id', $businessId)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'total' => (int) $expenses,
                'amount' => (int) $expenses,
            ]
        ]);
    }

    // Helper methods

    private function getInventoryValue($businessId)
    {
        try {
            return (int) DB::table('products')
                ->where('business_id', $businessId)
                ->where('is_active', true)
                ->selectRaw('SUM(stock_qty * cost) as value')
                ->value('value') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getProfit($businessId)
    {
        try {
            // Calculate profit from sales (total - cost of goods)
            $sales = DB::table('sales')
                ->where('business_id', $businessId)
                ->sum('total') ?? 0;

            // Check if sale_items table exists and has data
            $costOfGoods = 0;
            if (DB::table('sale_items')->exists()) {
                $costOfGoods = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.business_id', $businessId)
                    ->selectRaw('SUM(sale_items.qty * products.cost) as cost')
                    ->join('products', 'sale_items.product_id', '=', 'products.id')
                    ->value('cost') ?? 0;
            }

            return (int) ($sales - $costOfGoods);
        } catch (\Exception $e) {
            \Log::warning('Profit calculation error: ' . $e->getMessage());
            return 0;
        }
    }

    private function getOrderCount($businessId)
    {
        try {
            return DB::table('sales')
                ->where('business_id', $businessId)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getOutstandingCredits($businessId)
    {
        try {
            return (int) DB::table('sales')
                ->where('business_id', $businessId)
                ->where('payment_method', 'credit')
                ->sum('total') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalExpenses($businessId)
    {
        try {
            return (int) DB::table('expenses')
                ->where('business_id', $businessId)
                ->sum('amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalSales($businessId)
    {
        try {
            return (int) DB::table('sales')
                ->where('business_id', $businessId)
                ->sum('total') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getCurrentBalance($businessId)
    {
        // Balance = Total Sales - Total Expenses
        $sales = $this->getTotalSales($businessId);
        $expenses = $this->getTotalExpenses($businessId);

        return $sales - $expenses;
    }

    private function getTodaySales($businessId, $today)
    {
        return (int) DB::table('sales')
            ->where('business_id', $businessId)
            ->whereDate('sold_at', $today)
            ->sum('total');
    }

    private function getMonthSales($businessId, $startOfMonth)
    {
        return (int) DB::table('sales')
            ->where('business_id', $businessId)
            ->whereDate('sold_at', '>=', $startOfMonth)
            ->sum('total');
    }
}
