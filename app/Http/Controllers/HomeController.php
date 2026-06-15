<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve all projects and tasks
        $projects = Project::getAllProjects();
        $tasks = Task::getAllTasks();

        // Get filters from request
        $projectFilter = $request->input('projectFilter', 0);
        $statusFilter = $request->input('statusFilter', '');

        // Apply filters if provided
        if ($projectFilter != 0 || $statusFilter != '') {
            $tasksFilter = [];

            // Apply project filter if not set to all
            if ($projectFilter != 'all') {
                $tasksFilter['project'] = $projectFilter;
            }

            // Apply status filter if not set to all
            if ($statusFilter != 'all') {
                $tasksFilter['is_completed'] = ($statusFilter == 'completed') ? 1 : 0;
            }

            // Get tasks with applied filters
            $tasks = Task::getAllTasksWithFilters($tasksFilter);
        }

        // Return view with data
        return view('home.home')->with([
            'projects' => $projects,
            'tasks' => $tasks,
            'projectFilter' => $projectFilter,
            'statusFilter' => $statusFilter,
        ]);
    }

public function manage(Request $request)
{
    $perPage = $request->get('per_page', 10);
    $now = Carbon::now();
    $user = Auth::user(); // المستخدم الحالي

    $isAdmin = $user->hasRole('admin') || $user->hasRole('super-admin'); 

    $projectQuery = Project::query();
    $statsQuery = Project::query();

    if (!$isAdmin) {
        $filterScope = function ($query) use ($user) {
            $query->where('user_id', $user->id) // هو من أنشأ المشروع (تأكد من اسم العمود إذا كان user_id أو creator_id)
                  ->orWhereHas('team', function ($q) use ($user) {
                      $q->where('users.id', $user->id); // هو عضو في فريق العمل
                  });
        };

        $projectQuery->where($filterScope);
        $statsQuery->where($filterScope);
    }

    // 2. حساب الإحصائيات بناءً على النطاق المحدد (المشاريع المتاحة له فقط)
    $stats = [
        'total'       => (clone $statsQuery)->count(),
        'not_started' => (clone $statsQuery)->where('status', 'NOT_STARTED')->count(),
        'in_progress' => (clone $statsQuery)->where('status', 'IN_PROGRESS')->count(),
        'on_hold'     => (clone $statsQuery)->where('status', 'ON_HOLD')->count(),
        'completed'   => (clone $statsQuery)->where('status', 'COMPLETED')->count(),
        
        'delayed'     => (clone $statsQuery)->where('status', '!=', 'COMPLETED')
                                            ->where('end_date', '<', $now)
                                            ->count(),
    ];

    // 3. جلب المشاريع المفلترة مع العلاقات والعدادات
    $projects = $projectQuery->with(['user', 'team'])
        ->withCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'COMPLETED');
            },
        ])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    // إرسال البيانات إلى صفحة البليد
    return view('admin.management.manage', compact('projects', 'stats'));
}}
