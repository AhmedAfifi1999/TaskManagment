<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'site_name' => 'Taskera',

            'site_description' => 'Taskera is a project and task management system that helps teams organize projects, manage tasks, and improve productivity.',

            'site_email' => 'info@taskera.com',

            'site_phone' => '+970 599 123 456',

            'site_address' => 'Nablus, Palestine',

            'facebook_link' => 'https://facebook.com/taskera',

            'twitter_link' => 'https://twitter.com/taskera',

            'instagram_link' => 'https://instagram.com/taskera',

            'linkedin_link' => 'https://linkedin.com/company/taskera',

            'youtube_link' => 'https://youtube.com/@taskera',

            'is_maintenance_mode' => false,

            'maintenance_message' => 'The website is currently under maintenance. Please check back soon.',

            'site_logo' => 'images/logo.png',
        ];
    }
}