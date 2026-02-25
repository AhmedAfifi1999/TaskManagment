<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // مسح الكاش الخاص بالصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | 1) إنشاء الصلاحيات
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Projects
            'view projects',
            'view own projects',
            'create project',
            'edit project',
            'delete project',

            // Tasks
            'view tasks',
            'view own tasks',
            'create task',
            'edit task',
            'delete task',
            'update task status',

            // Users
            'view users',
            'create user',
            'edit user',
            'delete user',

            // Roles & Permissions
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2) إنشاء الأدوار
        |--------------------------------------------------------------------------
        */

        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $member  = Role::firstOrCreate(['name' => 'member']);

        /*
        |--------------------------------------------------------------------------
        | 3) ربط الصلاحيات بالأدوار
        |--------------------------------------------------------------------------
        */

        // Admin يأخذ كل الصلاحيات
        $admin->syncPermissions(Permission::all());

        // Manager
        $manager->syncPermissions([
            'view projects',
            'view own projects',
            'create project',
            'edit project',

            'view tasks',
            'create task',
            'edit task',
            'update task status',
        ]);

        // Member
        $member->syncPermissions([
            'view own projects',
            'view own tasks',
            'update task status',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4) تعيين Admin لأول مستخدم (اختياري لكن مهم)
        |--------------------------------------------------------------------------
        */

        $user = User::first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}