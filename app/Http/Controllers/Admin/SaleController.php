<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index()
    {
        $sales = DB::table('sales')
            ->leftJoin('businesses', 'businesses.id', '=', 'sales.business_id')
            ->leftJoin('users', 'users.id', '=', 'sales.user_id')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'sales.*',
                'businesses.name as business_name',
                'users.name as user_name',
                'customers.name as customer_name'
            )
            ->orderBy('sales.sold_at', 'desc')
            ->paginate(20);

        return view('admin.business_data.sales.index', compact('sales'));
    }

    public function create()
    {
        $businesses = DB::table('businesses')->orderBy('name')->get();
        $users = DB::table('users')->orderBy('name')->get();
        $customers = DB::table('customers')->orderBy('name')->get();
        $products = DB::table('products')->where('is_active', true)->orderBy('name')->get();

        return view('admin.business_data.sales.create', compact('businesses', 'users', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'user_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'sale_no' => ['nullable', 'string', 'max:255'],
            'sold_at' => ['required', 'date'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.sku' => ['nullable', 'string', 'max:255'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $saleNo = $data['sale_no'] ?? null;
        if (!$saleNo) {
            $saleNo = 'SALE-' . strtoupper(Str::random(8));
        }

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += ((int) $item['qty']) * ((float) $item['unit_price']);
        }
        $tax = (float) ($data['tax'] ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $total = max(0, $subtotal + $tax - $discount);

        DB::beginTransaction();
        try {
            $saleId = DB::table('sales')->insertGetId([
                'business_id' => $data['business_id'],
                'user_id' => $data['user_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'sale_no' => $saleNo,
                'sold_at' => $data['sold_at'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $data['payment_method'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                $lineTotal = ((int) $item['qty']) * ((float) $item['unit_price']);

                DB::table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'sku' => $item['sku'] ?? null,
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!empty($item['product_id'])) {
                    DB::table('products')->where('id', $item['product_id'])->decrement('stock_qty', (int) $item['qty']);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('admin.business_data.sales.index');
    }

    public function show(int $id)
    {
        $sale = DB::table('sales')
            ->leftJoin('businesses', 'businesses.id', '=', 'sales.business_id')
            ->leftJoin('users', 'users.id', '=', 'sales.user_id')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'sales.*',
                'businesses.name as business_name',
                'users.name as user_name',
                'customers.name as customer_name'
            )
            ->where('sales.id', $id)
            ->first();

        abort_if(!$sale, 404);

        $items = DB::table('sale_items')->where('sale_id', $id)->orderBy('id')->get();

        return view('admin.business_data.sales.show', compact('sale', 'items'));
    }

    public function destroy(int $id)
    {
        DB::table('sales')->where('id', $id)->delete();

        return redirect()->route('admin.business_data.sales.index');
    }

    public function salesByBusiness()
    {
        $rows = DB::table('sales')
            ->join('businesses', 'businesses.id', '=', 'sales.business_id')
            ->select('businesses.id', 'businesses.name', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(sales.total) as total_amount'))
            ->groupBy('businesses.id', 'businesses.name')
            ->orderByDesc('total_amount')
            ->get();

        return view('admin.business_data.sales_by_business.index', compact('rows'));
    }

    public function salesByUser()
    {
        $rows = DB::table('sales')
            ->leftJoin('users', 'users.id', '=', 'sales.user_id')
            ->select(DB::raw('COALESCE(users.name, "Unknown") as name'), DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(sales.total) as total_amount'))
            ->groupBy(DB::raw('COALESCE(users.name, "Unknown")'))
            ->orderByDesc('total_amount')
            ->get();

        return view('admin.business_data.sales_by_user.index', compact('rows'));
    }
}
