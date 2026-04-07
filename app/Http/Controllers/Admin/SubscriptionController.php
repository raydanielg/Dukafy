<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $subscriptions = DB::table('subscriptions')
            ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
            ->leftJoin('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->select('subscriptions.*', 'users.name as user_name', 'users.email as user_email', 'plans.name as plan_name')
            ->orderByDesc('subscriptions.id')
            ->paginate(15);

        return view('admin.subscription.subscriptions.index', compact('subscriptions'));
    }

    public function history()
    {
        $history = DB::table('subscription_histories')
            ->leftJoin('subscriptions', 'subscriptions.id', '=', 'subscription_histories.subscription_id')
            ->leftJoin('users', 'users.id', '=', 'subscription_histories.user_id')
            ->select('subscription_histories.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('subscription_histories.id')
            ->paginate(15);

        return view('admin.subscription.subscriptions.history', compact('history'));
    }

    public function expiringSoon()
    {
        $from = now()->toDateString();
        $to = now()->addDays(14)->toDateString();

        $subscriptions = DB::table('subscriptions')
            ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
            ->leftJoin('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('subscriptions.status', 'active')
            ->whereBetween('subscriptions.ends_at', [$from, $to])
            ->select('subscriptions.*', 'users.name as user_name', 'users.email as user_email', 'plans.name as plan_name')
            ->orderBy('subscriptions.ends_at')
            ->paginate(15);

        return view('admin.subscription.subscriptions.expiring', compact('subscriptions', 'from', 'to'));
    }

    public function cancelled()
    {
        $subscriptions = DB::table('subscriptions')
            ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
            ->leftJoin('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('subscriptions.status', 'cancelled')
            ->select('subscriptions.*', 'users.name as user_name', 'users.email as user_email', 'plans.name as plan_name')
            ->orderByDesc('subscriptions.cancelled_at')
            ->paginate(15);

        return view('admin.subscription.subscriptions.cancelled', compact('subscriptions'));
    }

    public function trialRequests()
    {
        $requests = DB::table('trial_requests')
            ->leftJoin('users', 'users.id', '=', 'trial_requests.user_id')
            ->select('trial_requests.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('trial_requests.id')
            ->paginate(15);

        return view('admin.subscription.trials.index', compact('requests'));
    }

    public function approveTrial(int $id)
    {
        $req = DB::table('trial_requests')->where('id', $id)->first();
        abort_if(!$req, 404);

        DB::table('trial_requests')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now(),
        ]);

        $planId = DB::table('plans')->where('slug', 'pro')->value('id');

        $subscriptionId = DB::table('subscriptions')->insertGetId([
            'user_id' => $req->user_id,
            'plan_id' => $planId,
            'status' => 'trial',
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addDays((int) $req->requested_days)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('subscription_histories')->insert([
            'subscription_id' => $subscriptionId,
            'user_id' => $req->user_id,
            'event' => 'trial_approved',
            'meta' => json_encode(['trial_request_id' => $id, 'approved_by' => auth()->id()]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function rejectTrial(int $id)
    {
        DB::table('trial_requests')->where('id', $id)->update([
            'status' => 'rejected',
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function invoices()
    {
        $invoices = DB::table('invoices')
            ->leftJoin('subscriptions', 'subscriptions.id', '=', 'invoices.subscription_id')
            ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
            ->select('invoices.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('invoices.id')
            ->paginate(15);

        $payments = DB::table('payments')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->select('payments.*')
            ->orderByDesc('payments.id')
            ->paginate(15);

        return view('admin.subscription.billing.invoices_payments', compact('invoices', 'payments'));
    }

    public function addPayment(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'integer'],
            'amount' => ['required', 'integer', 'min:1'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
        ]);

        $invoice = DB::table('invoices')->where('id', $data['invoice_id'])->first();
        abort_if(!$invoice, 404);

        DB::table('payments')->insert([
            'invoice_id' => $data['invoice_id'],
            'amount' => $data['amount'],
            'method' => $data['method'] ?? null,
            'reference' => $data['reference'] ?? null,
            'paid_at' => $data['paid_at'] ?? now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('invoices')->where('id', $data['invoice_id'])->update([
            'status' => 'paid',
            'paid_at' => $data['paid_at'] ?? now()->toDateString(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }
}
