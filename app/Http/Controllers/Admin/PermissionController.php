<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $roles = DB::table('roles')->orderBy('name')->get();
        $permissions = DB::table('permissions')->orderBy('name')->get();

        $rolePermissions = DB::table('permission_role')->get()->groupBy('role_id');
        $map = [];
        foreach ($rolePermissions as $roleId => $items) {
            $map[$roleId] = $items->pluck('permission_id')->all();
        }

        return view('admin.users.permissions.index', compact('roles', 'permissions', 'map'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer'],
        ]);

        $roleId = (int) $data['role_id'];
        $permissionIds = $data['permission_ids'] ?? [];

        DB::table('permission_role')->where('role_id', $roleId)->delete();

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->insert([
                'permission_id' => (int) $permissionId,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back();
    }
}
