<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'site_name',
        'site_logo',
        'site_description',
        'site_email',
        'site_phone',
        'site_address',
        'facebook_link',
        'twitter_link',
        'instagram_link',
        'linkedin_link',
        'youtube_link',
        'is_maintenance_mode',
        'maintenance_message',
    ];

   
}
