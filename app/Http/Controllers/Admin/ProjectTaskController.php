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

        // جلب المهام مع التصفية حسب الصلاحيات
        $project->load(['tasks' => function ($query) use ($isAdminOrOwner) {
            if (!$isAdminOrOwner) {
                $query->where('user_id', auth()->id());
            }
            $query->orderBy('priority');
        }]);

        return view('admin.tasks.index', compact('project'));
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
}
