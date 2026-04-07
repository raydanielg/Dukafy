<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('roles')->updateOrInsert(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('roles')->updateOrInsert(
            ['slug' => 'manager'],
            ['name' => 'Manager', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('roles')->updateOrInsert(
            ['slug' => 'cashier'],
            ['name' => 'Cashier', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('permissions')->updateOrInsert(
            ['slug' => 'users.manage'],
            ['name' => 'Manage Users', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('permissions')->updateOrInsert(
            ['slug' => 'roles.manage'],
            ['name' => 'Manage Roles', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('permissions')->updateOrInsert(
            ['slug' => 'articles.manage'],
            ['name' => 'Manage Articles', 'created_at' => now(), 'updated_at' => now()]
        );

        $superAdminId = DB::table('roles')->where('slug', 'super-admin')->value('id');
        $adminId = DB::table('roles')->where('slug', 'admin')->value('id');

        $permissionIds = DB::table('permissions')->pluck('id');

        foreach ([$superAdminId, $adminId] as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->updateOrInsert(
                    ['permission_id' => $permissionId, 'role_id' => $roleId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
