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
        $status = trim((string) $request->get('status', ''));

        $users = DB::table('users')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($status === 'approved', fn ($query) => $query->whereNotNull('approved_at')->whereNull('banned_at'))
            ->when($status === 'pending', fn ($query) => $query->whereNull('approved_at')->whereNull('banned_at'))
            ->when($status === 'banned', fn ($query) => $query->whereNotNull('banned_at'))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'status'));
    }

    public function create()
    {
        $roles = DB::table('roles')->orderBy('name')->get();
        $groups = DB::table('user_groups')->orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'is_admin' => ['nullable'],
            'role_id' => ['nullable', 'integer'],
            'group_id' => ['nullable', 'integer'],
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

        if (!empty($data['group_id'])) {
            DB::table('user_group_user')->updateOrInsert(
                ['user_group_id' => $data['group_id'], 'user_id' => $userId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return redirect()->route('admin.users.index');
    }

    public function edit(int $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        abort_if(!$user, 404);

        $roles = DB::table('roles')->orderBy('name')->get();
        $groups = DB::table('user_groups')->orderBy('name')->get();

        $selectedRoleId = DB::table('role_user')->where('user_id', $id)->value('role_id');
        $selectedGroupId = DB::table('user_group_user')->where('user_id', $id)->value('user_group_id');

        return view('admin.users.edit', compact('user', 'roles', 'groups', 'selectedRoleId', 'selectedGroupId'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_admin' => ['nullable'],
            'role_id' => ['nullable', 'integer'],
            'group_id' => ['nullable', 'integer'],
            'approved' => ['nullable'],
        ]);

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => isset($data['is_admin']),
            'approved_at' => isset($data['approved']) ? now() : null,
            'updated_at' => now(),
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        DB::table('users')->where('id', $id)->update($update);

        DB::table('role_user')->where('user_id', $id)->delete();
        if (!empty($data['role_id'])) {
            DB::table('role_user')->insert([
                'role_id' => $data['role_id'],
                'user_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('user_group_user')->where('user_id', $id)->delete();
        if (!empty($data['group_id'])) {
            DB::table('user_group_user')->insert([
                'user_group_id' => $data['group_id'],
                'user_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.users.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('users')->where('id', $id)->delete();

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
