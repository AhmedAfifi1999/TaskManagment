<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'name' => fake()->name(),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),

            'email_verified_at' => now(),

            'password' => Hash::make('123456'),

            'remember_token' => Str::random(10),

            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),

            'is_active' => 1,
            'is_banned' => 0,
        ];
    }


    public function admin(): static
    {
        return $this->state(fn () => [
            'username' => 'admin',
            'name' => 'Admin',
            'full_name' => 'System Administrator',
            'email' => 'admin@taskera.com',
            'password' => Hash::make('123456'),
        ]);
    }


    public function manager(): static
    {
        return $this->state(fn () => [
            'username' => 'manager',
            'name' => 'Manager',
            'full_name' => 'Project Manager',
            'email' => 'manager@taskera.com',
            'password' => Hash::make('123456'),
        ]);
    }


    public function member(): static
    {
        return $this->state(fn () => [
            'username' => 'member',
            'name' => 'Member',
            'full_name' => 'Team Member',
            'email' => 'member@taskera.com',
            'password' => Hash::make('123456'),
        ]);
    }
}