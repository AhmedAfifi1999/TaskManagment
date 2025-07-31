<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // Validate data
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'site_description' => 'required|string',
            'site_email' => 'nullable|email',
            'site_phone' => 'nullable|string',
            'site_address' => 'nullable|string',
            'facebook_link' => 'nullable|url',
            'twitter_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
            'is_maintenance_mode' => 'required|in:0,1',
            'maintenance_message' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $maintenanceMode = (int)$request->input('is_maintenance_mode', 0);

            $settings = Setting::firstOrNew([]);

            // Handle checkbox input
            if ($request->hasFile('site_logo')) {
                if ($settings->site_logo) {
                    Storage::disk('public')->delete($settings->site_logo);
                }

                $imagePath = $request->file('site_logo')->store('settings', 'public');
                $settings->site_logo = $imagePath;
            }


            // Update all fields
            $settings->fill($validated);
            $settings->is_maintenance_mode = $maintenanceMode;
            // dd($settings->is_maintenance_mode);

            $settings->save();

            DB::commit();

            return redirect()->route('admin.settings.edit')
                ->with('success', 'تم تحديث الإعدادات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage());
        }
    }
}
