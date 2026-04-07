<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'admin@dukafy.co.tz'],
            [
                'name' => 'Dukafy Admin',
                'password' => Hash::make('Admin@12345'),
                'email_verified_at' => now(),
                'approved_at' => now(),
                'is_admin' => true,
            ]
        );

        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        if ($adminRoleId) {
            DB::table('role_user')->updateOrInsert(
                ['role_id' => $adminRoleId, 'user_id' => $user->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
