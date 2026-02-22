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
                ->paginate(request('per_page', 15));
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
            'team.*' => 'exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('projects', 'public');
            $validated['attachment'] = $path;
        }
        // إنشاء المشروع
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'budget' => $validated['budget'] ?? null,
            'currency' => $validated['currency'] ?? null,
            'attachment' => $validated['attachment'] ?? null,

            'user_id' => auth()->id(), // المستخدم الحالي هو منشئ المشروع
        ]);

        // إضافة أعضاء الفريق
        $project->team()->sync($validated['team']);

        return redirect()->route('admin.projects.index')
            ->with('success', 'تم إنشاء المشروع بنجاح');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if ($project->user_id == auth()->id() || auth()->user()->isAdmin()) {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'color' => 'required|string|max:7',
                'status' => 'required|in:NOT_STARTED,IN_PROGRESS,COMPLETED,ON_HOLD,DELAYED',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'team' => 'required|array',
                'team.*' => 'exists:users,id',
                'budget' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|size:3',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            ]);

            // معالجة رفع الملف
            if ($request->hasFile('attachment')) {

                // حذف الملف القديم إن وجد
                if ($project->attachment && Storage::disk('public')->exists($project->attachment)) {
                    Storage::disk('public')->delete($project->attachment);
                }

                $validated['attachment'] = $request->file('attachment')
                    ->store('projects/attachments', 'public');
            }

            // تحديث البيانات بدون لمس attachment لو لم يتم رفع ملف
            $project->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'color' => $validated['color'],
                'status' => $validated['status'],
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'budget' => $validated['budget'] ?? null,
                'currency' => $validated['currency'] ?? null,
                'attachment' => $validated['attachment'] ?? $project->attachment,
            ]);

            $project->team()->sync($validated['team']);

            return redirect()->route('admin.projects.index')
                ->with('success', 'تم تحديث المشروع بنجاح');
        }

        abort(403, 'غير مصرح لك بتعديل هذا المشروع');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);

        // التحقق من الصلاحيات
        if ($project->user_id == auth()->id() || auth()->user()->username == 'admin') {

            // dd($project->user_id != auth()->id());
            $users = User::all();
            $selectedTeam = $project->team->pluck('id')->toArray();

            return view('admin.projects.edit', compact('project', 'users', 'selectedTeam'));
        } else {
            abort(403, 'غير مصرح لك بتعديل هذا المشروع');
        }
    }

    public function destroy($id)
    {
        $item = Project::findOrFail($id);
        // التحقق من الصلاحيات

        if ($item->user_id == auth()->id() || auth()->user()->username == 'admin') {
            $item->team()->detach();

            // ثم حذف المشروع
            $item->delete();

            return redirect()->route('admin.projects.index')
                ->with('success', 'تم حذف المشروع بنجاح');
        } else {
            abort(403, 'غير مصرح لك بحذف هذا المشروع');
        }

        // حذف العلاقات أولاً
    }
}
