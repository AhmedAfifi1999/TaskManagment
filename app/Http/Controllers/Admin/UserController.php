<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 15));

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
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
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2000', // الصورة
        ]);

        // تشفير كلمة المرور
        $data['password'] = Hash::make($data['password']);
        $data['name'] = $request->full_name;
        // رفع الصورة إذا تم رفعها
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = uniqid().'.'.$file->getClientOriginalExtension();

            $path = $file->storeAs('users', $filename, 'public');

            $data['image'] = $path;
        }        $user = User::create($data);

        // 👇 إضافة الدور
        $user->assignRole($request->role);

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

        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // التحقق من صحة البيانات المدخلة
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$id, // استثناء المستخدم الحالي
            'email' => 'required|email|max:255|unique:users,email,'.$id, // استثناء البريد الإلكتروني الحالي
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|confirmed|min:6', // كلمة المرور اختياري في التحديث
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:800', // الصورة
        ]);

        // العثور على المستخدم
        $user = User::findOrFail($id);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']); // لا تقم بتحديث كلمة المرور
        }
        $data['name'] = $request->full_name;

        // رفع الصورة إذا تم رفعها
        if ($request->hasFile('avatar')) {

            if ($user->image && file_exists(storage_path('app/public/'.$user->image))) {
                unlink(storage_path('app/public/'.$user->image));
            }

            $file = $request->file('avatar');

            $filename = uniqid().'.'.$file->getClientOriginalExtension();

            $path = $file->storeAs('users', $filename, 'public');

            $data['image'] = $path;
        }

        // تحديث بيانات المستخدم
        $user->update($data);
        $user->syncRoles([$request->role]);

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
