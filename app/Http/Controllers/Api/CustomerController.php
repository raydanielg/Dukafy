<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->business_id) {
            return response()->json(['message' => 'No business associated'], 404);
        }

        $q = $request->string('q')->toString();

        $query = DB::table('customers')->where('business_id', $user->business_id);
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $customers = $query->orderBy('name')->paginate($perPage);

        return response()->json($customers);
    }
}
