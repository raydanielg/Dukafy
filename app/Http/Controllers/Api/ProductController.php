<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $q = $request->string('q')->toString();
        $type = $request->string('type')->toString(); // all|bundle|product

        $query = DB::table('products')
            ->where('business_id', $user->business_id)
            ->where('is_active', true);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        if ($type === 'bundle') {
            $query->where('is_bundle', true);
        } elseif ($type === 'product') {
            $query->where('is_bundle', false);
        }

        $perPage = (int) $request->get('per_page', 50);
        $products = $query->orderBy('name')->paginate($perPage);

        // Map to include image_url
        $products->getCollection()->transform(function ($p) {
            $p->image_url = null;
            if (!empty($p->image)) {
                if (str_starts_with($p->image, 'http://') || str_starts_with($p->image, 'https://')) {
                    $p->image_url = $p->image;
                } elseif (file_exists(public_path($p->image))) {
                    $p->image_url = url($p->image);
                } else {
                    $p->image_url = asset('storage/' . $p->image);
                }
            }
            return $p;
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_qty' => 'integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        $validated['business_id'] = $user->business_id;

        $product = DB::table('products')->insertGetId($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $product,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $product = DB::table('products')
            ->where('id', $id)
            ->where('business_id', $user->business_id)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'nullable|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_qty' => 'sometimes|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        $updated = DB::table('products')
            ->where('id', $id)
            ->where('business_id', $user->business_id)
            ->update($validated);

        if (!$updated) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $deleted = DB::table('products')
            ->where('id', $id)
            ->where('business_id', $user->business_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $totalProducts = DB::table('products')
            ->where('business_id', $user->business_id)
            ->where('is_active', true)
            ->count();

        $totalValue = DB::table('products')
            ->where('business_id', $user->business_id)
            ->where('is_active', true)
            ->selectRaw('SUM(stock_qty * cost) as total_value')
            ->value('total_value') ?? 0;

        $lowStock = DB::table('products')
            ->where('business_id', $user->business_id)
            ->where('is_active', true)
            ->where('stock_qty', '<', 10)
            ->where('stock_qty', '>', 0)
            ->count();

        $outOfStock = DB::table('products')
            ->where('business_id', $user->business_id)
            ->where('is_active', true)
            ->where('stock_qty', '=', 0)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $totalProducts,
                'total_value' => (int) $totalValue,
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
            ],
        ]);
    }
}
