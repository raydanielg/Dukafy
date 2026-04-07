<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('products')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'products.business_id')
            ->select(
                'products.*',
                'product_categories.name as category_name',
                'businesses.name as business_name'
            )
            ->orderBy('products.id', 'desc')
            ->paginate(20);

        return view('admin.business_data.products.index', compact('products'));
    }

    public function create()
    {
        $businesses = DB::table('businesses')->orderBy('name')->get();
        $categories = DB::table('product_categories')->orderBy('name')->get();

        return view('admin.business_data.products.create', compact('businesses', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer'],
            'low_stock_threshold' => ['required', 'integer'],
            'is_active' => ['nullable'],
            'is_bundle' => ['nullable'],
        ]);

        $imagePath = $data['image'] ?? null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('products', 'public');
        }

        DB::table('products')->insert([
            'business_id' => $data['business_id'],
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'image' => $imagePath,
            'price' => $data['price'],
            'cost' => $data['cost'] ?? null,
            'stock_qty' => $data['stock_qty'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'is_active' => isset($data['is_active']),
            'is_bundle' => isset($data['is_bundle']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.products.index');
    }

    public function edit(int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        abort_if(!$product, 404);

        $businesses = DB::table('businesses')->orderBy('name')->get();
        $categories = DB::table('product_categories')->orderBy('name')->get();

        return view('admin.business_data.products.edit', compact('product', 'businesses', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        abort_if(!$product, 404);

        $data = $request->validate([
            'business_id' => ['required', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer'],
            'low_stock_threshold' => ['required', 'integer'],
            'is_active' => ['nullable'],
            'is_bundle' => ['nullable'],
        ]);

        $imagePath = $data['image'] ?? $product->image;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('products', 'public');
        }

        DB::table('products')->where('id', $id)->update([
            'business_id' => $data['business_id'],
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'image' => $imagePath,
            'price' => $data['price'],
            'cost' => $data['cost'] ?? null,
            'stock_qty' => $data['stock_qty'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'is_active' => isset($data['is_active']),
            'is_bundle' => isset($data['is_bundle']),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.business_data.products.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('products')->where('id', $id)->delete();

        return redirect()->route('admin.business_data.products.index');
    }
}
