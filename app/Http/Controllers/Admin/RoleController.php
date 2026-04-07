<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $roles = DB::table('roles')->orderBy('name')->get();

        return view('admin.users.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.users.roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
        ]);

        DB::table('roles')->updateOrInsert(
            ['slug' => $data['slug']],
            ['name' => $data['name'], 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin.roles.index');
    }
}
