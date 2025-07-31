<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        // فلتر التاريخ
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        // معالجة الفلتر السريع
        if ($request->has('days')) {
            $days = $request->input('days');
            $endDate = now();
            $startDate = now()->subDays($days);
        }

        // معالجة فلتر الشهر
        if ($request->has('month')) {
            $monthType = $request->input('month');
            $now = now();

            if ($monthType === 'this') {
                $startDate = $now->startOfMonth();
                $endDate = $now->endOfMonth();
            } else {
                $startDate = $now->subMonth()->startOfMonth();
                $endDate = $now->subMonth()->endOfMonth();
            }
        }



        $totalProjectsQuery = Project::query();
        if ($startDate && $endDate) {
            $totalProjectsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalProjects = $totalProjectsQuery->count();


        // المشاريع المنتهية
        $completedProjectsQuery = Project::where('status', 'COMPLETED');
        if ($startDate && $endDate) {
            $completedProjectsQuery->whereBetween('end_date', [$startDate, $endDate]);
        }
        $completedProjects = $completedProjectsQuery->count();

        // المشاريع قيد التنفيذ
        $activeProjectsQuery = Project::where('status', 'IN_PROGRESS');
        if ($startDate && $endDate) {
            $activeProjectsQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }
        $activeProjects = $activeProjectsQuery->count();

        $delayedProjectsQuery = Project::where('end_date', '<', now()) // تاريخ الانتهاء قد مضى
            ->where('status', '!=', 'completed'); // الحالة ليست مكتملة

        if ($startDate && $endDate) {
            $delayedProjectsQuery->whereBetween('end_date', [$startDate, $endDate]);
        }

        $delayedProjects = $delayedProjectsQuery->count();

        // المشاريع المتعثرة
        $failedProjectsQuery = Project::where('end_date', '<', now()) // تاريخ الانتهاء قد مضى
            ->where('status',  'IN_PROGRESS');
        if ($startDate && $endDate) {
            $failedProjectsQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }
        $failedProjects = $failedProjectsQuery->count();

        // إجمالي المهام
        $totalTasksQuery = Task::query();
        if ($startDate && $endDate) {
            $totalTasksQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalTasks = $totalTasksQuery->count();

        // المهام المكتملة
        $completedTasksQuery = Task::where('status', 'completed');
        if ($startDate && $endDate) {
            $completedTasksQuery->whereBetween('updated_at', [$startDate, $endDate]);
        }
        $completedTasks = $completedTasksQuery->count();


        $delayedTasksQuery = Task::where('end_time', '<', now()) // تاريخ انتهاء المهمة قد مضى
            ->where('status', '!=', 'completed'); // الحالة ليست مكتملة

        if ($startDate && $endDate) {
            $delayedTasksQuery->whereBetween('end_time', [$startDate, $endDate]);
        }

        $delayedTasks = $delayedTasksQuery->count();

        // إجمالي المستخدمين
        $totalUsersQuery = User::query();
        if ($startDate && $endDate) {
            $totalUsersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalUsers = $totalUsersQuery->count();


        $expiringProjects = Project::where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(14))
            ->where('status', '!=', 'COMPLETED')
            ->withCount('team')
            ->orderBy('end_date')
            ->limit(5)
            ->get()
            ->map(function ($project) {
                $project->days_remaining = now()->diffInDays($project->end_date, false);
                return $project;
            });

        $recentTasks = Task::with(['user', 'projectTask'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        // بيانات المقارنة للنسب المئوية (يمكن استبدالها ببيانات فعلية من فترة سابقة)
        $total_projects_prev = $totalProjects - rand(1, 5);
        $completed_projects_prev = $completedProjects - rand(1, 3);
        $active_projects_prev = $activeProjects - rand(1, 2);
        $total_tasks_prev = $totalTasks - rand(5, 15);
        $completed_tasks_prev = $completedTasks - rand(5, 10);
        $delayed_tasks_prev = $delayedTasks - rand(1, 3);
        $total_users_prev = $totalUsers - rand(1, 2);
        $failed_projects_prev = $failedProjects - rand(0, 1);

        $data = [
            'total_projects' => $totalProjects,
            'completed_projects' => $completedProjects,
            'active_projects' => $activeProjects,
            'delayed_projects' => $delayedProjects,
            'failed_projects' => $failedProjects,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'delayed_tasks' => $delayedTasks,
            'total_users' => $totalUsers,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'expiring_projects' => $expiringProjects,
            'recent_tasks' => $recentTasks,
            'total_projects_prev' => $total_projects_prev,
            'completed_projects_prev' => $completed_projects_prev,
            'active_projects_prev' => $active_projects_prev,
            'total_tasks_prev' => $total_tasks_prev,
            'completed_tasks_prev' => $completed_tasks_prev,
            'delayed_tasks_prev' => $delayed_tasks_prev,
            'total_users_prev' => $total_users_prev,
            'failed_projects_prev' => $failed_projects_prev,
        ];
        //  dd($data);
        return view('admin.dashboard', $data);
    }
}
