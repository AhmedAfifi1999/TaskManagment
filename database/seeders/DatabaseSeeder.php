<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        

        // Setting::factory()->create();

        // $this->call([
        //     RolesAndPermissionsSeeder::class,
        // ]);

                $admin = User::factory()
            ->admin()
            ->create();

        $admin->assignRole('admin');



        $manager = User::factory()
            ->manager()
            ->create();

        $manager->assignRole('manager');



        $member = User::factory()
            ->member()
            ->create();

        $member->assignRole('member');

    }
}
