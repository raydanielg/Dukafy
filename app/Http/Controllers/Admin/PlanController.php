<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $plans = DB::table('plans')->orderBy('name')->get();

        return view('admin.subscription.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_yearly' => ['required', 'integer', 'min:0'],
            'user_limit' => ['nullable', 'integer', 'min:1'],
            'product_limit' => ['nullable', 'integer', 'min:1'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'string'],
            'active' => ['nullable'],
        ]);

        DB::table('plans')->insert([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price_monthly' => $data['price_monthly'],
            'price_yearly' => $data['price_yearly'],
            'user_limit' => $data['user_limit'] ?? null,
            'product_limit' => $data['product_limit'] ?? null,
            'trial_days' => $data['trial_days'] ?? null,
            'features' => !empty($data['features']) ? json_encode(array_values(array_filter(array_map('trim', explode("\n", $data['features']))))) : null,
            'active' => isset($data['active']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.plans.index');
    }

    public function edit(int $id)
    {
        $plan = DB::table('plans')->where('id', $id)->first();
        abort_if(!$plan, 404);

        return view('admin.subscription.plans.edit', compact('plan'));
    }

    public function update(Request $request, int $id)
    {
        $plan = DB::table('plans')->where('id', $id)->first();
        abort_if(!$plan, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_yearly' => ['required', 'integer', 'min:0'],
            'user_limit' => ['nullable', 'integer', 'min:1'],
            'product_limit' => ['nullable', 'integer', 'min:1'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'string'],
            'active' => ['nullable'],
        ]);

        DB::table('plans')->where('id', $id)->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'price_monthly' => $data['price_monthly'],
            'price_yearly' => $data['price_yearly'],
            'user_limit' => $data['user_limit'] ?? null,
            'product_limit' => $data['product_limit'] ?? null,
            'trial_days' => $data['trial_days'] ?? null,
            'features' => !empty($data['features']) ? json_encode(array_values(array_filter(array_map('trim', explode("\n", $data['features']))))) : null,
            'active' => isset($data['active']),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.plans.edit', $id);
    }
}
