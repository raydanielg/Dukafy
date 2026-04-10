<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $q = $request->string('q')->toString();

        $query = DB::table('customers')->where('business_id', $user->business_id);
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $customers = $query->orderBy('name')->paginate($perPage);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'group_id' => 'nullable|exists:customer_groups,id',
        ]);

        $validated['business_id'] = $user->business_id;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $customer = DB::table('customers')->insertGetId($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'customer_id' => $customer,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $customer = DB::table('customers')
            ->where('id', $id)
            ->where('business_id', $user->business_id)
            ->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Get customer stats
        $totalPurchases = DB::table('sales')
            ->where('customer_id', $id)
            ->sum('total') ?? 0;

        $outstandingBalance = DB::table('sales')
            ->where('customer_id', $id)
            ->where('payment_method', 'credit')
            ->sum('total') ?? 0;

        $customer->total_purchases = $totalPurchases;
        $customer->balance = $outstandingBalance;

        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'group_id' => 'nullable|exists:customer_groups,id',
        ]);

        $validated['updated_at'] = now();

        $updated = DB::table('customers')
            ->where('id', $id)
            ->where('business_id', $user->business_id)
            ->update($validated);

        if (!$updated) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $deleted = DB::table('customers')
            ->where('id', $id)
            ->where('business_id', $user->business_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $totalCustomers = DB::table('customers')
            ->where('business_id', $user->business_id)
            ->count();

        $activeCustomers = DB::table('customers')
            ->where('business_id', $user->business_id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sales')
                    ->whereColumn('sales.customer_id', 'customers.id');
            })
            ->count();

        $creditCustomers = DB::table('customers as c')
            ->where('c.business_id', $user->business_id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sales as s')
                    ->whereColumn('s.customer_id', 'c.id')
                    ->where('s.payment_method', 'credit');
            })
            ->count();

        $totalOutstanding = DB::table('sales')
            ->where('business_id', $user->business_id)
            ->where('payment_method', 'credit')
            ->sum('total') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'credit_customers' => $creditCustomers,
                'total_outstanding' => (int) $totalOutstanding,
            ],
        ]);
    }
}
