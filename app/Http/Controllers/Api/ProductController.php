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
}
