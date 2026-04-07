<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    private function readLogFile(?string $path, int $maxLines = 400): array
    {
        if (!$path || !is_file($path)) {
            return ['path' => $path, 'exists' => false, 'lines' => []];
        }

        $content = @file($path, FILE_IGNORE_NEW_LINES);
        if ($content === false) {
            return ['path' => $path, 'exists' => true, 'lines' => []];
        }

        $tail = array_slice($content, -$maxLines);

        return ['path' => $path, 'exists' => true, 'lines' => $tail];
    }

    public function access()
    {
        $log = $this->readLogFile(storage_path('logs/laravel.log'));

        return view('admin.logs.access', compact('log'));
    }

    public function errors()
    {
        $log = $this->readLogFile(storage_path('logs/laravel.log'));

        return view('admin.logs.errors', compact('log'));
    }

    public function payments()
    {
        $logs = DB::table('payment_logs')
            ->leftJoin('users', 'users.id', '=', 'payment_logs.user_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'payment_logs.business_id')
            ->select('payment_logs.*', 'users.name as user_name', 'businesses.name as business_name')
            ->orderBy('payment_logs.id', 'desc')
            ->paginate(20);

        return view('admin.logs.payments', compact('logs'));
    }

    public function emails()
    {
        $logs = DB::table('email_logs')
            ->leftJoin('users', 'users.id', '=', 'email_logs.user_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'email_logs.business_id')
            ->select('email_logs.*', 'users.name as user_name', 'businesses.name as business_name')
            ->orderBy('email_logs.id', 'desc')
            ->paginate(20);

        return view('admin.logs.emails', compact('logs'));
    }
}
