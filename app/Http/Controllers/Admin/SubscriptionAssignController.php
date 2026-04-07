<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionAssignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function create()
    {
        $users = DB::table('users')->orderBy('name')->get();
        $plans = DB::table('plans')->where('active', true)->orderBy('name')->get();

        return view('admin.subscription.assign.create', compact('users', 'plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'plan_id' => ['required', 'integer'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date'],
            'status' => ['required', 'string'],
        ]);

        $subscriptionId = DB::table('subscriptions')->insertGetId([
            'user_id' => $data['user_id'],
            'plan_id' => $data['plan_id'],
            'status' => $data['status'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('subscription_histories')->insert([
            'subscription_id' => $subscriptionId,
            'user_id' => $data['user_id'],
            'event' => 'created',
            'meta' => json_encode(['assigned_by' => auth()->id(), 'plan_id' => $data['plan_id'], 'status' => $data['status']]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($data['status'] !== 'trial') {
            $amount = (int) (DB::table('plans')->where('id', $data['plan_id'])->value('price_monthly') ?? 0);

            DB::table('invoices')->insert([
                'subscription_id' => $subscriptionId,
                'amount' => $amount,
                'currency' => 'TZS',
                'status' => 'unpaid',
                'issued_at' => now()->toDateString(),
                'due_at' => now()->addDays(7)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.subscriptions.index');
    }
}
