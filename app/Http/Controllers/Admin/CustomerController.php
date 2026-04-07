<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = DB::table('customers')
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers.group_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'customers.business_id')
            ->select(
                'customers.*',
                'customer_groups.name as group_name',
                'businesses.name as business_name'
            )
            ->orderBy('customers.id', 'desc')
            ->paginate(20);

        return view('admin.business_data.customers.index', compact('customers'));
    }

    public function create()
    {
        $businesses = DB::table('businesses')->orderBy('name')->get();
        $groups = DB::table('customer_groups')->orderBy('name')->get();

        return view('admin.business_data.customers.create', compact('businesses', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'group_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_blacklisted' => ['nullable'],
        ]);

        DB::table('customers')->insert([
            'business_id' => $data['business_id'],
            'group_id' => $data['group_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'is_blacklisted' => isset($data['is_blacklisted']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.customers.index');
    }

    public function edit(int $id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        abort_if(!$customer, 404);

        $businesses = DB::table('businesses')->orderBy('name')->get();
        $groups = DB::table('customer_groups')->orderBy('name')->get();

        return view('admin.business_data.customers.edit', compact('customer', 'businesses', 'groups'));
    }

    public function update(Request $request, int $id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        abort_if(!$customer, 404);

        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'group_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_blacklisted' => ['nullable'],
        ]);

        DB::table('customers')->where('id', $id)->update([
            'business_id' => $data['business_id'],
            'group_id' => $data['group_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'is_blacklisted' => isset($data['is_blacklisted']),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.customers.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('customers')->where('id', $id)->delete();

        return redirect()->route('admin.business_data.customers.index');
    }

    public function blacklisted()
    {
        $customers = DB::table('customers')
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers.group_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'customers.business_id')
            ->select(
                'customers.*',
                'customer_groups.name as group_name',
                'businesses.name as business_name'
            )
            ->where('customers.is_blacklisted', true)
            ->orderBy('customers.id', 'desc')
            ->paginate(20);

        return view('admin.business_data.blacklisted_customers.index', compact('customers'));
    }
}
