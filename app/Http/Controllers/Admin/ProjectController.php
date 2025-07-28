<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //

    public function index()
    {
        if (auth()->user()->isAdmin()) {
            // إذا كان المستخدم أدمن، عرض جميع المشاريع
            $projects = Project::with('creator')->latest()->paginate(10);
        } else {
            // إذا كان مستخدم عادي، عرض المشاريع التي أنشأها أو هو عضو فيها
            $projects = Project::with('creator')->where('user_id', auth()->id())
                ->orWhereHas('team', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->latest()
                ->paginate(10);
        }
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.projects.create', compact('users'));
    }


    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'status' => 'required|in:NOT_STARTED,IN_PROGRESS,COMPLETED,ON_HOLD,DELAYED',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'team' => 'required|array',
            'team.*' => 'exists:users,id'
        ]);

        // إنشاء المشروع
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'user_id' => auth()->id() // المستخدم الحالي هو منشئ المشروع
        ]);

        // إضافة أعضاء الفريق
        $project->team()->sync($validated['team']);

        return redirect()->route('admin.projects.index')
            ->with('success', 'تم إنشاء المشروع بنجاح');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // التحقق من الصلاحيات (يمكن استخدام Policy بدلاً من ذلك)
        if ($project->user_id != auth()->id()) {
            // dd([$project->user_id,auth()->id()]);
            abort(403, 'غير مصرح لك بتعديل هذا المشروع');
        }

        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'status' => 'required|in:NOT_STARTED,IN_PROGRESS,COMPLETED,ON_HOLD,DELAYED',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'team' => 'required|array',
            'team.*' => 'exists:users,id'
        ]);

        // تحديث بيانات المشروع
        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date']
        ]);

        // تحديث أعضاء الفريق
        $project->team()->sync($validated['team']);

        return redirect()->route('admin.projects.index')
            ->with('success', 'تم تحديث المشروع بنجاح');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);

        // التحقق من الصلاحيات
        if ($project->user_id != auth()->id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المشروع');
        }

        $users = User::all();
        $selectedTeam = $project->team->pluck('id')->toArray();

        return view('admin.projects.edit', compact('project', 'users', 'selectedTeam'));
    }
    public function destroy($id)
    {
        $item = Project::findOrFail($id);
        // التحقق من الصلاحيات
        if ($item->user_id != auth()->id()) {
            // dd([$item, auth()->id()]);
            abort(403, 'غير مصرح لك بحذف هذا المشروع');
        }

        // حذف العلاقات أولاً
        $item->team()->detach();

        // ثم حذف المشروع
        $item->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }
}
