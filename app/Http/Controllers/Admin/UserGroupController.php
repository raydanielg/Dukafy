<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $groups = DB::table('user_groups')->orderBy('name')->get();

        return view('admin.users.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.users.groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
        ]);

        DB::table('user_groups')->updateOrInsert(
            ['slug' => $data['slug']],
            ['name' => $data['name'], 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin.groups.index');
    }
}
