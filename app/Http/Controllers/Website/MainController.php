<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request)
    {

        $setting=Setting::first();
        return view('website.index',compact('setting'));
    }
}
