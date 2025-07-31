<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //

    public function loginPage()
    {
        $website = Setting::select('site_name', 'site_logo')->first();
        // dd($website->site_name);
    return view('auth.login', compact('website'));
    }
    public function login(Request $request)
    {
        // التحقق من البيانات
        $request->validate([
            'email' => 'required|string', // يمكن أن يكون email أو username
            'password' => 'required',
        ]);

        // تحديد ما إذا كان المدخل email أو username
        $key = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';

        // محاولة تسجيل الدخول
        $attempt = [
            $key => $request->email,
            'password' => $request->password,
        ];
        //  dd(Auth::attempt($attempt));

        if (Auth::attempt($attempt)) {

            // التحقق من أن المستخدم مفعل وغير محظور
            $user = Auth::user();

            if ($user->is_active == 1 && $user->is_banned == 0) {

                $request->session()->regenerate(); // تجديد session لمنع هجمات fixation
                return redirect()->intended('/admin')->with('success', 'تم تسجيل الدخول بنجاح!'); // رسالة نجاح
            } else {
                dd('test wrong');

                // إذا كان المستخدم غير مفعل أو محظور
                Auth::logout(); // تسجيل الخروج
                return redirect()->back()->with('error', 'الحساب غير مفعل أو محظور.'); // رسالة خطأ
            }
        }
        //  dd('test not ok');

        // إذا فشل تسجيل الدخول
        return redirect()->back()->with('error', 'بيانات الاعتماد غير صحيحة.'); // رسالة خطأ
    }

    public function logout(Request $request)
    {
        Auth::logout(); // تسجيل الخروج

        $request->session()->invalidate(); // إبطال الـ Session الحالي
        $request->session()->regenerateToken(); // تجديد رمز الـ CSRF

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح!'); // رسالة نجاح
    }
}
