<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectTaskController extends Controller
{
    //

    public function index($id)
    {
        $project = Project::findOrFail($id);

        // تحقق إذا كان المستخدم أدمن أو منشئ المشروع
        $isAdminOrOwner = auth()->user()->username == 'admin' || $project->user_id == auth()->id();


        // جلب المهام مع التصفية حسب الصلاحيات + ترقيم الصفحات
        $tasks = $project->tasks()
            ->when(!$isAdminOrOwner, function ($query) {
                $query->where('user_id', auth()->id());
            })
           

            ->orderBy('priority') // الترتيب حسب الحقل المخصص
            ->paginate(request('per_page', 15));

        return view('admin.tasks.index', compact('project', 'tasks'));
    }

    public function create($projectId)
    {
        $project = Project::with('team')->find($projectId);
        return view('admin.tasks.create', compact('project'));
    }
    public function edit($projectId, $taskId)
    {
        $task = Task::find($taskId);

        $project = Project::with('team')->find($projectId);
        return view('admin.tasks.edit', compact('project', 'task'));
    }
    public function store(Request $request, $projectId)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|between:1,10',
            'user_id' => 'required|exists:users,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:not_started,in_progress,completed',
        ]);
        $project = Project::with(['user', 'team'])->findOrFail($projectId);
        $currentUser = auth()->user();

        // 2. التحقق من الصلاحية:
        $isAllowed = $currentUser->username === 'admin' ||       // إذا كان أدمن
            $currentUser->id == $project->user_id ||    // أو هو منشئ المشروع
            $project->team->contains($currentUser->id); // أو عضو في الفريق

        // 3. إذا لم يكن مخولاً، نمنعه من إنشاء المهمة
        if (!$isAllowed) {
            return back()
                ->withErrors(['user_id' => 'ليست لديك صلاحية إنشاء مهام في هذا المشروع'])
                ->withInput();
        }


        // إنشاء المشروع
        $task = Task::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'project' => $projectId,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'user_id' => $validated['user_id'],
            'project_id' => $projectId,
        ]);

        return redirect()->route('admin.projects.tasks.index', $projectId)
            ->with('success', 'تم إنشاء المهمة بنجاح');
    }
    public function update(Request $request, $projectId, $taskId)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|integer|between:1,10',
            'user_id' => 'required|exists:users,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'status' => 'required|in:not_started,in_progress,completed',
        ]);

        // الحصول على المشروع والمهمة
        $project = Project::with(['user', 'team'])->findOrFail($projectId);
        $task = Task::findOrFail($taskId);

        $currentUser = auth()->user();

        // التحقق من الصلاحية
        $isAllowed = $currentUser->username === 'admin' ||       // إذا كان أدمن
            $currentUser->id == $project->user_id ||    // أو هو منشئ المشروع
            $project->team->contains($currentUser->id); // أو عضو في الفريق

        if (!$isAllowed) {
            return back()
                ->withErrors(['user_id' => 'ليست لديك صلاحية تعديل مهام في هذا المشروع'])
                ->withInput();
        }

        // التحقق الإضافي: هل المستخدم الجديد مسموح به؟
        $newAssignedUser = User::find($validated['user_id']);
        $isUserValid = $newAssignedUser->id == $project->user_id ||
            $project->team->contains('id', $newAssignedUser->id);

        if (!$isUserValid) {
            return back()
                ->withErrors(['user_id' => 'لا يمكن تعيين المهمة لهذا المستخدم'])
                ->withInput();
        }

        // تحديث المهمة
        $task->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'user_id' => $validated['user_id'],
        ]);

        return redirect()->route('admin.projects.tasks.index', $projectId)
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'taskIds' => 'required|array',
            'taskIds.*' => 'integer|exists:tasks,id'
        ]);

        $taskIds = $request->input('taskIds', []);

        DB::transaction(function () use ($taskIds) {
            foreach ($taskIds as $index => $taskId) {
                Task::where('id', $taskId)
                    ->update(['priority' => $index + 1]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function status(Request $request, Project $project, $TaskId)
    {

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,completed' // تأكد من تطابق القيم مع جدولك
        ]);
        $task = Task::find($TaskId);
        $task->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }

    public function destroy($projectId, $id)
    {
        $item = Project::findOrFail($projectId);
        // التحقق من الصلاحيات
        if ($item->user_id != auth()->id()) {
            // dd([$item, auth()->id()]);
            abort(403, 'غير مصرح لك بحذف هذا المشروع');
        }

        // حذف العلاقات أولاً
        $task = Task::find($id);
        // ثم حذف المشروع
        $task->delete();

        return redirect()->route('admin.projects.tasks.index', ['project' => $projectId])
            ->with('success', 'تم حذف المهمة بنجاح');
    }
}
