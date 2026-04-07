<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessDataController extends Controller
{
    public function lowStockAlerts()
    {
        $products = DB::table('products')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'products.business_id')
            ->select('products.*', 'product_categories.name as category_name', 'businesses.name as business_name')
            ->where('products.is_active', true)
            ->whereColumn('products.stock_qty', '<=', 'products.low_stock_threshold')
            ->orderBy('products.stock_qty')
            ->paginate(20);

        return view('admin.business_data.low_stock_alerts.index', compact('products'));
    }

    public function bulkImportExport()
    {
        return view('admin.business_data.bulk_import_export.index');
    }

    public function exportProductsCsv()
    {
        $filename = 'products_export_' . now()->format('Ymd_His') . '.csv';

        $products = DB::table('products')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoin('businesses', 'businesses.id', '=', 'products.business_id')
            ->select(
                'businesses.slug as business_slug',
                'product_categories.slug as category_slug',
                'products.name',
                'products.sku',
                'products.price',
                'products.cost',
                'products.stock_qty',
                'products.low_stock_threshold',
                'products.is_active'
            )
            ->orderBy('products.id', 'desc')
            ->get();

        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, ['business_slug', 'category_slug', 'name', 'sku', 'price', 'cost', 'stock_qty', 'low_stock_threshold', 'is_active']);

        foreach ($products as $p) {
            fputcsv($handle, [
                $p->business_slug,
                $p->category_slug,
                $p->name,
                $p->sku,
                $p->price,
                $p->cost,
                $p->stock_qty,
                $p->low_stock_threshold,
                $p->is_active,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function importProductsCsv(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file'],
        ]);

        $path = $data['file']->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return redirect()->back();
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return redirect()->back();
        }

        $headerMap = array_flip($header);

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $businessSlug = $row[$headerMap['business_slug']] ?? null;
                $categorySlug = $row[$headerMap['category_slug']] ?? null;
                $name = $row[$headerMap['name']] ?? null;

                if (!$businessSlug || !$name) {
                    continue;
                }

                $businessId = DB::table('businesses')->where('slug', $businessSlug)->value('id');
                if (!$businessId) {
                    continue;
                }

                $categoryId = null;
                if ($categorySlug) {
                    $categoryId = DB::table('product_categories')
                        ->where('business_id', $businessId)
                        ->where('slug', $categorySlug)
                        ->value('id');
                }

                $sku = $row[$headerMap['sku']] ?? null;

                $payload = [
                    'business_id' => $businessId,
                    'category_id' => $categoryId,
                    'name' => $name,
                    'sku' => $sku ?: null,
                    'price' => (float) ($row[$headerMap['price']] ?? 0),
                    'cost' => ($row[$headerMap['cost']] ?? '') !== '' ? (float) $row[$headerMap['cost']] : null,
                    'stock_qty' => (int) ($row[$headerMap['stock_qty']] ?? 0),
                    'low_stock_threshold' => (int) ($row[$headerMap['low_stock_threshold']] ?? 0),
                    'is_active' => (bool) ($row[$headerMap['is_active']] ?? 1),
                    'updated_at' => now(),
                ];

                if ($sku) {
                    DB::table('products')->updateOrInsert(
                        ['business_id' => $businessId, 'sku' => $sku],
                        $payload + ['created_at' => now()]
                    );
                } else {
                    DB::table('products')->insert($payload + ['created_at' => now()]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }

        fclose($handle);

        return redirect()->route('admin.business_data.products.index');
    }
}
