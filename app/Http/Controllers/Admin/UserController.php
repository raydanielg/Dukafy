<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = DB::table('users')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create()
    {
        $roles = DB::table('roles')->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'is_admin' => ['nullable'],
            'role_id' => ['nullable', 'integer'],
            'approved' => ['nullable'],
        ]);

        $userId = DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => isset($data['is_admin']),
            'approved_at' => isset($data['approved']) ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!empty($data['role_id'])) {
            DB::table('role_user')->updateOrInsert(
                ['role_id' => $data['role_id'], 'user_id' => $userId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return redirect()->route('admin.users.index');
    }

    public function pending()
    {
        $users = DB::table('users')
            ->whereNull('approved_at')
            ->whereNull('banned_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('admin.users.pending', compact('users'));
    }

    public function banned()
    {
        $users = DB::table('users')
            ->whereNotNull('banned_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('admin.users.banned', compact('users'));
    }

    public function approve(int $id)
    {
        DB::table('users')->where('id', $id)->update([
            'approved_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function ban(int $id)
    {
        DB::table('users')->where('id', $id)->update([
            'banned_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function unban(int $id)
    {
        DB::table('users')->where('id', $id)->update([
            'banned_at' => null,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }
}
