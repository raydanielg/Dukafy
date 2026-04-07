<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerGroupController extends Controller
{
    public function index()
    {
        $groups = DB::table('customer_groups')
            ->leftJoin('businesses', 'businesses.id', '=', 'customer_groups.business_id')
            ->select('customer_groups.*', 'businesses.name as business_name')
            ->orderBy('customer_groups.id', 'desc')
            ->paginate(20);

        return view('admin.business_data.customer_groups.index', compact('groups'));
    }

    public function create()
    {
        $businesses = DB::table('businesses')->orderBy('name')->get();

        return view('admin.business_data.customer_groups.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::table('customer_groups')->insert([
            'business_id' => $data['business_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.customer_groups.index');
    }

    public function edit(int $id)
    {
        $group = DB::table('customer_groups')->where('id', $id)->first();
        abort_if(!$group, 404);

        $businesses = DB::table('businesses')->orderBy('name')->get();

        return view('admin.business_data.customer_groups.edit', compact('group', 'businesses'));
    }

    public function update(Request $request, int $id)
    {
        $group = DB::table('customer_groups')->where('id', $id)->first();
        abort_if(!$group, 404);

        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::table('customer_groups')->where('id', $id)->update([
            'business_id' => $data['business_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.customer_groups.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('customer_groups')->where('id', $id)->delete();

        return redirect()->route('admin.business_data.customer_groups.index');
    }
}
