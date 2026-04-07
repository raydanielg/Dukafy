<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('product_categories')
            ->leftJoin('businesses', 'businesses.id', '=', 'product_categories.business_id')
            ->select('product_categories.*', 'businesses.name as business_name')
            ->orderBy('product_categories.id', 'desc')
            ->paginate(20);

        return view('admin.business_data.product_categories.index', compact('categories'));
    }

    public function create()
    {
        $businesses = DB::table('businesses')->orderBy('name')->get();

        return view('admin.business_data.product_categories.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::table('product_categories')->insert([
            'business_id' => $data['business_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.product_categories.index');
    }

    public function edit(int $id)
    {
        $category = DB::table('product_categories')->where('id', $id)->first();
        abort_if(!$category, 404);

        $businesses = DB::table('businesses')->orderBy('name')->get();

        return view('admin.business_data.product_categories.edit', compact('category', 'businesses'));
    }

    public function update(Request $request, int $id)
    {
        $category = DB::table('product_categories')->where('id', $id)->first();
        abort_if(!$category, 404);

        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::table('product_categories')->where('id', $id)->update([
            'business_id' => $data['business_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.product_categories.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('product_categories')->where('id', $id)->delete();

        return redirect()->route('admin.business_data.product_categories.index');
    }
}
