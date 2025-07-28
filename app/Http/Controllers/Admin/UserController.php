<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|confirmed|min:6',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:800', // الصورة
        ]);
    
        // تشفير كلمة المرور
        $data['password'] = Hash::make($data['password']);
        $data['name']=$request->full_name;
        // رفع الصورة إذا تم رفعها
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('users', $filename); // سيتم الحفظ في storage/app/users
            $data['image'] = $path;
        }
    
        User::create($data);
    
        return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // التحقق من صحة البيانات المدخلة
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id, // استثناء المستخدم الحالي
            'email' => 'required|email|max:255|unique:users,email,' . $id, // استثناء البريد الإلكتروني الحالي
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|confirmed|min:6', // كلمة المرور اختياري في التحديث
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:800', // الصورة
        ]);
    
        // العثور على المستخدم
        $user = User::findOrFail($id);
    
        // إذا كانت هناك كلمة مرور جديدة
        if ($request->has('password')) {
            $data['password'] = Hash::make($data['password']);
        }
        $data['name']=$request->full_name;

        // رفع الصورة إذا تم رفعها
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->image && file_exists(storage_path('app/public/' . $user->image))) {
                unlink(storage_path('app/public/' . $user->image));
            }
    
            // رفع صورة جديدة
            $file = $request->file('avatar');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('users', $filename); // حفظ الصورة في الدليل المحدد
            $data['image'] = $path; // تحديث الصورة
        }
    
        // تحديث بيانات المستخدم
        $user->update($data);
    
        // إعادة التوجيه مع رسالة النجاح
        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->image) {
            Storage::disk('local')->delete($user->image);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح.');


    }
}
