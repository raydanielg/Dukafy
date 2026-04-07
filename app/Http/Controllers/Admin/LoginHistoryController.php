<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LoginHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $logins = DB::table('user_login_histories')
            ->leftJoin('users', 'users.id', '=', 'user_login_histories.user_id')
            ->select('user_login_histories.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('user_login_histories.id')
            ->paginate(15);

        return view('admin.users.login_history.index', compact('logins'));
    }
}
