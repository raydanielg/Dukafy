<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:25', 'unique:users,phone'],
                'password' => ['required', 'string', 'min:6'],
            ]);

            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'is_approved' => false,
            ]);

            // Auto-assign Manager role to new users
            $managerRoleId = DB::table('roles')->where('slug', 'manager')->value('id');
            if ($managerRoleId) {
                DB::table('role_user')->insert([
                    'role_id' => $managerRoleId,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'is_approved' => (bool) $user->is_approved,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('API Register Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(['phone' => $data['phone'], 'password' => $data['password']])) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 422);
        }

        /** @var User $user */
        $user = $request->user();
        if (!$user) {
            $user = User::where('phone', $data['phone'])->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'is_approved' => (bool) $user->is_approved,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function approveInitial(Request $request)
    {
        try {
            /** @var User|null $user */
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            if (!$user->is_approved) {
                $user->forceFill(['is_approved' => true])->save();
            }

            return response()->json([
                'message' => 'Verified',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'is_approved' => (bool) $user->is_approved,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Approve Initial Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completeOnboarding(Request $request)
    {
        try {
            $data = $request->validate([
                'role' => ['required', 'string', 'in:owner,cashier'],
                'manager_id' => ['nullable', 'required_if:role,cashier', 'exists:users,id'],
                // Optional business info for owners
                'business_name' => ['nullable', 'string', 'max:255'],
                'business_type' => ['nullable', 'string', 'exists:business_types,slug'],
            ]);

            /** @var User|null $user */
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $roleSlug = $data['role'] === 'owner' ? 'manager' : 'cashier';
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');

            // 1. Create business if owner provided name
            $businessId = $user->business_id;
            if ($data['role'] === 'owner' && !empty($data['business_name'])) {
                $typeId = null;
                if (!empty($data['business_type'])) {
                    $typeId = DB::table('business_types')->where('slug', $data['business_type'])->value('id');
                }

                $businessId = DB::table('businesses')->insertGetId([
                    'name' => $data['business_name'],
                    'slug' => \Illuminate\Support\Str::slug($data['business_name']) . '-' . rand(100, 999),
                    'business_type_id' => $typeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Update user
            $user->forceFill([
                'business_id' => $businessId,
                'manager_id' => $data['manager_id'] ?? null,
            ])->save();

            // 3. Assign Role
            if ($roleId) {
                DB::table('role_user')->updateOrInsert(
                    ['user_id' => $user->id],
                    ['role_id' => $roleId, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            return response()->json([
                'message' => 'Onboarding completed',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'is_approved' => (bool) $user->is_approved,
                    'role' => $roleSlug,
                    'business_id' => $user->business_id,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Onboarding Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Setup failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchManagers(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Simplest possible query to avoid JOIN issues with SQLite/Eloquent
        $managers = User::query()
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->with(['business'])
            ->limit(10)
            ->get()
            ->filter(function($user) {
                // Filter by role manually to ensure it works even if relationships are tricky
                return $user->roles()->where('slug', 'manager')->exists() || $user->is_admin;
            })
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'phone' => $m->phone,
                    'email' => $m->email,
                    'business_name' => $m->business?->name ?? 'Dukafy Business',
                    'business_address' => $m->business?->address ?? 'N/A',
                ];
            })
            ->values();

        return response()->json($managers);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}
