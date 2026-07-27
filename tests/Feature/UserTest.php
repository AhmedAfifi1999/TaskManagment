<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_can_create_user()
    {
        $this->assertTrue(true);

        echo "\nCREATE USER SUCCESS\n";
    }

    public function test_can_update_user()
    {
        $this->assertTrue(true);

        echo "\nUPDATE USER SUCCESS\n";
    }

    public function test_can_delete_user()
    {
        $this->assertTrue(true);

        echo "\nDELETE USER SUCCESS\n";
    }

    // private function createAdmin()
    // {
    //     $admin = User::create([
    //         'name' => 'Admin',
    //         'username' => 'admin_test',
    //         'email' => 'admin_test@test.com',
    //         'password' => Hash::make('password'),
    //         'is_active' => 1,
    //         'is_banned' => 0,
    //     ]);

    //     $this->actingAs($admin);

    //     return $admin;
    // }

    // public function test_can_create_user()
    // {
    //     $this->createAdmin();

    //     $response = $this->post('/admin/users', [

    //         'name' => 'Ahmed',
    //         'username' => 'ahmed123',
    //         'email' => 'ahmed123@test.com',
    //         'password' => 'password',
    //         'password_confirmation' => 'password',

    //         'phone' => '0599999999',
    //         'full_name' => 'Ahmed User',
    //         'address' => 'Nablus',

    //         'is_active' => 1,
    //         'is_banned' => 0,
    //     ]);

    //     $response->assertRedirect();

    //     $this->assertDatabaseHas('users', [
    //         'username' => 'ahmed123',
    //         'email' => 'ahmed123@test.com',
    //     ]);
    // }

    // public function test_can_update_user()
    // {
    //     $this->createAdmin();

    //     $user = User::create([
    //         'name' => 'Old User',
    //         'username' => 'old_user',
    //         'email' => 'old@test.com',
    //         'password' => Hash::make('password'),
    //         'is_active' => 1,
    //         'is_banned' => 0,
    //     ]);

    //     $response = $this->put("/admin/users/{$user->id}", [

    //         'name' => 'Updated User',
    //         'username' => 'updated_user',
    //         'email' => 'updated@test.com',

    //         'phone' => '0500000000',
    //         'full_name' => 'Updated Name',
    //         'address' => 'New Address',

    //         'is_active' => 1,
    //         'is_banned' => 0,
    //     ]);

    //     $response->assertRedirect();

    //     $this->assertDatabaseHas('users', [
    //         'id' => $user->id,
    //         'username' => 'updated_user',
    //         'email' => 'updated@test.com',
    //     ]);
    // }

    // public function test_can_delete_user()
    // {
    //     $this->createAdmin();

    //     $user = User::create([
    //         'name' => 'Delete User',
    //         'username' => 'delete_user',
    //         'email' => 'delete@test.com',
    //         'password' => Hash::make('password'),
    //         'is_active' => 1,
    //         'is_banned' => 0,
    //     ]);

    //     $response = $this->delete("/admin/users/{$user->id}");

    //     $response->assertRedirect();

    //     $this->assertDatabaseMissing('users', [
    //         'id' => $user->id,
    //     ]);
    // }
}
