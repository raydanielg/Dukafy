<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function revenueOverview()
    {
        $totalPaid = (int) DB::table('payments')->sum('amount');
        $totalInvoices = (int) DB::table('invoices')->sum('amount');
        $unpaid = (int) DB::table('invoices')->where('status', 'unpaid')->sum('amount');

        return view('admin.finance.revenue_overview', compact('totalPaid', 'totalInvoices', 'unpaid'));
    }

    public function paymentMethods()
    {
        $methods = DB::table('payment_methods')->orderBy('name')->get();

        return view('admin.finance.payment_methods', compact('methods'));
    }

    public function addPaymentMethod(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'enabled' => ['nullable'],
        ]);

        DB::table('payment_methods')->updateOrInsert(
            ['slug' => $data['slug']],
            ['name' => $data['name'], 'enabled' => isset($data['enabled']), 'created_at' => now(), 'updated_at' => now()]
        );

        return redirect()->back();
    }

    public function paymentGateway()
    {
        $gateway = DB::table('payment_gateway_settings')->where('id', 1)->first();

        return view('admin.finance.payment_gateway', compact('gateway'));
    }

    public function updatePaymentGateway(Request $request)
    {
        $data = $request->validate([
            'provider' => ['nullable', 'string', 'max:255'],
            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'enabled' => ['nullable'],
        ]);

        DB::table('payment_gateway_settings')->where('id', 1)->update([
            'provider' => $data['provider'] ?? null,
            'public_key' => $data['public_key'] ?? null,
            'secret_key' => $data['secret_key'] ?? null,
            'enabled' => isset($data['enabled']),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function invoiceSettings()
    {
        $settings = DB::table('invoice_settings')->where('id', 1)->first();

        return view('admin.finance.invoice_settings', compact('settings'));
    }

    public function updateInvoiceSettings(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
            'prefix' => ['nullable', 'string', 'max:20'],
        ]);

        DB::table('invoice_settings')->where('id', 1)->update([
            'business_name' => $data['business_name'] ?? null,
            'tin' => $data['tin'] ?? null,
            'address' => $data['address'] ?? null,
            'currency' => $data['currency'],
            'prefix' => $data['prefix'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function taxSettings()
    {
        $settings = DB::table('tax_settings')->where('id', 1)->first();

        return view('admin.finance.tax_settings', compact('settings'));
    }

    public function updateTaxSettings(Request $request)
    {
        $data = $request->validate([
            'vat_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'vat_enabled' => ['nullable'],
        ]);

        DB::table('tax_settings')->where('id', 1)->update([
            'vat_percent' => $data['vat_percent'],
            'vat_enabled' => isset($data['vat_enabled']),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function expenses()
    {
        $expenses = DB::table('expenses')->orderByDesc('spent_at')->orderByDesc('id')->paginate(15);

        return view('admin.finance.expenses', compact('expenses'));
    }

    public function addExpense(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:0'],
            'category' => ['nullable', 'string', 'max:255'],
            'spent_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::table('expenses')->insert([
            'title' => $data['title'],
            'amount' => $data['amount'],
            'category' => $data['category'] ?? null,
            'spent_at' => $data['spent_at'] ?? now()->toDateString(),
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function profitLoss()
    {
        $revenue = (int) DB::table('payments')->sum('amount');
        $costs = (int) DB::table('expenses')->sum('amount');
        $profit = $revenue - $costs;

        return view('admin.finance.profit_loss', compact('revenue', 'costs', 'profit'));
    }
}
