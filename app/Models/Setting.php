<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'twitter_url',
        'linkedin_url',
        'instagram_url',
        'facebook_url',
        'contact_phone',
        'contact_email',
        'maintenance_message',
        'site_name',
        'site_logo',
        'site_description',
        'is_maintenance_mode'
    ];
}
