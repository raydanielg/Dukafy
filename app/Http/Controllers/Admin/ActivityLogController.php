<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $logs = DB::table('admin_activity_logs')
            ->leftJoin('users', 'users.id', '=', 'admin_activity_logs.user_id')
            ->select('admin_activity_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('admin_activity_logs.id')
            ->paginate(15);

        return view('admin.users.activity.index', compact('logs'));
    }
}
