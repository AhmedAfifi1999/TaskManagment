<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    
    public function index(Request $request)
    {
        $roles = Role::with('permissions')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // تقسيم الصلاحيات حسب الموديول (الكلمة الثانية في الاسم)
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'general';
        });

        return view('admin.roles.index', compact('roles', 'permissions'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إضافة الدور بنجاح');
    }


    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id
        ]);

        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم تعديل الدور بنجاح');
    }


    public function destroy(Role $role)
    {
        // حماية إضافية: منع حذف Admin الأساسي
        if ($role->name === 'Admin') {
            return back()->with('error', 'لا يمكن حذف دور Admin');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
}