<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{

    public function edit()
    {
        $settings = Setting::first(); // الحصول على الإعدادات الحالية
        return view('admin.settings.setting', compact('settings'));
    }

    public function security()
    {
        return view('admin.settings.security');
    }
    public function changePassword(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ], [
            'current_password.required' => 'حقل كلمة المرور الحالية مطلوب',
            'new_password.required' => 'حقل كلمة المرور الجديدة مطلوب',
            'new_password.min' => 'يجب أن تحتوي كلمة المرور على الأقل على 8 أحرف',
            'new_password.confirmed' => 'كلمة المرور غير متطابقة',
            'new_password_confirmation.required' => 'حقل تأكيد كلمة المرور مطلوب',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'كلمة المرور الحالية غير صحيحة'
            ])->withInput();
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors([
                'new_password' => 'لا يمكن استخدام كلمة المرور الحالية ككلمة مرور جديدة'
            ])->withInput();
        }

        // تحديث كلمة المرور
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.settings.security')
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
    }


    public function account()
    {
        $user = auth()->user();
        return view('admin.settings.account', compact('user'));
    }

    public function accountUpdate(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . auth()->id(),
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:800',
        ]);

        $user = Auth::user();

        $user->update([
            'name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('users/image', 'public');
            $user->image = $imagePath;
            $user->save();
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات الحساب بنجاح');
    }

    /**
     * تحديث الإعدادات.
     */
    public function update(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'site_description' => 'required|string',
            'is_maintenance_mode' => 'nullable|boolean',
            'maintenance_message' => 'nullable|string',
            'site_email' => 'nullable|email',
            'site_phone' => 'nullable|string',
            'site_address' => 'nullable|string',
            'facebook_link' => 'nullable|url',
            'twitter_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
        ]);

        // الحصول على الإعدادات الحالية أو إنشاء جديدة إذا لم توجد
        $settings = Setting::firstOrNew([]);

        // تحديث البيانات
        $settings->fill([
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'site_email' => $request->site_email,
            'site_phone' => $request->site_phone,
            'site_address' => $request->site_address,
            'facebook_link' => $request->facebook_link,
            'twitter_link' => $request->twitter_link,
            'instagram_link' => $request->instagram_link,
            'linkedin_link' => $request->linkedin_link,
            'youtube_link' => $request->youtube_link,
            'is_maintenance_mode' => $request->has('is_maintenance_mode'),
            'maintenance_message' => $request->maintenance_message,
        ]);

        if ($request->hasFile('site_logo')) {
            if ($settings->site_logo) {
                Storage::disk('public')->delete($settings->site_logo);
            }

            $imagePath = $request->file('site_logo')->store('settings', 'public');
            $settings->site_logo = $imagePath;
        }

        // حفظ التعديلات
        $settings->save();

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('admin.settings.edit')->with('success', 'تم تحديث الإعدادات بنجاح.');
    }
}
