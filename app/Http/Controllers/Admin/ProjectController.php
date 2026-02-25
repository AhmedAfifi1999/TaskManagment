<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $user = auth()->user();

        $query = Project::with('creator')->latest();

        // إذا عنده صلاحية عرض كل المشاريع
        if ($user->can('view projects')) {

            $projects = $query->paginate(15);

        } else {

            // عنده فقط view own projects
            $projects = $query
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('team', function ($team) use ($user) {
                            $team->where('user_id', $user->id);
                        });
                })
                ->paginate(15);
        }

        return view('admin.projects.index', compact('projects'));
    }
    // public function index()
    // {
    //     if (auth()->user()->isAdmin()) {
    //         // إذا كان المستخدم أدمن، عرض جميع المشاريع
    //         $projects = Project::with('creator')->latest()->paginate(10);
    //     } else {
    //         // إذا كان مستخدم عادي، عرض المشاريع التي أنشأها أو هو عضو فيها
    //         $projects = Project::with('creator')->where('user_id', auth()->id())
    //             ->orWhereHas('team', function ($query) {
    //                 $query->where('user_id', auth()->id());
    //             })
    //             ->latest()
    //             ->paginate(request('per_page', 15));
    //     }

    //     return view('admin.projects.index', compact('projects'));
    // }

    public function create()
    {
        $this->authorize('create project', Project::class);

        $users = User::all();

        return view('admin.projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
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

        $this->authorize('update', $project);

        $users = User::all();
        $selectedTeam = $project->team->pluck('id')->toArray();

        return view('admin.projects.edit', compact('project', 'users', 'selectedTeam'));
    }

 

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->team()->detach();
        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }
}
