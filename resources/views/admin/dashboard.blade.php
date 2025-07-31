@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- فلتر التاريخ -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body">
                        <form id="dateFilterForm" method="GET" action="{{ route('admin.dashboard') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="startDate" class="form-label">من تاريخ</label>
                                    <input type="date" class="form-control" id="startDate" name="start_date"
                                        value="{{ $start_date ?? '' }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="endDate" class="form-label">إلى تاريخ</label>
                                    <input type="date" class="form-control" id="endDate" name="end_date"
                                        value="{{ $end_date ?? '' }}">
                                </div>
                                <div class="col-md-6 d-flex align-items-end justify-content-between flex-wrap">
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="ti ti-filter ti-xs me-1"></i> تصفية
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-refresh ti-xs me-1"></i> إعادة تعيين
                                        </a>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                            id="quickFilter" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-clock ti-xs me-1"></i> فلتر سريع
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="quickFilter">
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-days="7">آخر 7
                                                    أيام</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-days="30">آخر 30
                                                    يوم</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-days="90">آخر 3
                                                    أشهر</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-month="this">هذا
                                                    الشهر</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-month="last">الشهر
                                                    الماضي</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-6">
            <!-- بطاقة عدد المشاريع -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-primary mb-3 rounded">
                            <i class="ti ti-folders ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">إجمالي المشاريع</h5>
                        <p class="card-subtitle">جميع المشاريع في النظام</p>
                        <p class="text-heading mb-3 mt-1">{{ $total_projects }} مشروع</p>
                        <div>
                            <span
                                class="badge bg-label-primary">+{{ round((($total_projects - $total_projects_prev) / max($total_projects_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة المشاريع المنتهية -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-success mb-3 rounded">
                            <i class="ti ti-checkbox ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">المشاريع المنتهية</h5>
                        <p class="card-subtitle">تم تسليمها بنجاح</p>
                        <p class="text-heading mb-3 mt-1">{{ $completed_projects }} مشروع</p>
                        <div>
                            <span
                                class="badge bg-label-success">+{{ round((($completed_projects - $completed_projects_prev) / max($completed_projects_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة المشاريع قيد التنفيذ -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-warning mb-3 rounded">
                            <i class="ti ti-progress-check ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">قيد التنفيذ</h5>
                        <p class="card-subtitle">المشاريع النشطة حالياً</p>
                        <p class="text-heading mb-3 mt-1">{{ $active_projects }} مشروع</p>
                        <div>
                            <span
                                class="badge bg-label-warning">+{{ round((($active_projects - $active_projects_prev) / max($active_projects_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة عدد المهام -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-info mb-3 rounded">
                            <i class="ti ti-list-check ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">إجمالي المهام</h5>
                        <p class="card-subtitle">جميع المهام في النظام</p>
                        <p class="text-heading mb-3 mt-1">{{ $total_tasks }} مهمة</p>
                        <div>
                            <span
                                class="badge bg-label-info">+{{ round((($total_tasks - $total_tasks_prev) / max($total_tasks_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة عدد المستخدمين -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-secondary mb-3 rounded">
                            <i class="ti ti-users ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">المستخدمون</h5>
                        <p class="card-subtitle">إجمالي المستخدمين في النظام</p>
                        <p class="text-heading mb-3 mt-1">{{ $total_users }} مستخدم</p>
                        <div>
                            <span
                                class="badge bg-label-secondary">+{{ round((($total_users - $total_users_prev) / max($total_users_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة المهام المكتملة -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-success mb-3 rounded">
                            <i class="ti ti-circle-check ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">المهام المكتملة</h5>
                        <p class="card-subtitle">تم إنهاؤها بنجاح</p>
                        <p class="text-heading mb-3 mt-1">{{ $completed_tasks }} مهمة</p>
                        <div>
                            <span
                                class="badge bg-label-success">+{{ round((($completed_tasks - $completed_tasks_prev) / max($completed_tasks_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة المهام المتأخرة -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-danger mb-3 rounded">
                            <i class="ti ti-alert-circle ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">المهام المتأخرة</h5>
                        <p class="card-subtitle">تجاوزت الموعد النهائي</p>
                        <p class="text-heading mb-3 mt-1">{{ $delayed_tasks }} مهمة</p>
                        <div>
                            <span
                                class="badge bg-label-danger">+{{ round((($delayed_tasks - $delayed_tasks_prev) / max($delayed_tasks_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة المشاريع المتعثرة -->
            <div class="col-xxl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="badge p-2 bg-label-danger mb-3 rounded">
                            <i class="ti ti-alert-triangle ti-28px"></i>
                        </div>
                        <h5 class="card-title mb-1">المشاريع المتعثرة</h5>
                        <p class="card-subtitle">تواجه صعوبات في التنفيذ</p>
                        <p class="text-heading mb-3 mt-1">{{ $failed_projects }} مشروع</p>
                        <div>
                            <span
                                class="badge bg-label-danger">+{{ round((($failed_projects - $failed_projects_prev) / max($failed_projects_prev, 1)) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم المشاريع القريبة من الانتهاء -->
        <div class="col-xxl-6 col-md-12 mt-4">
            <div class="card ">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">المشاريع القريبة من الانتهاء</h5>
                    {{-- <div class="dropdown">
                        <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1" type="button"
                            id="expiringProjectsMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical ti-md text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="expiringProjectsMenu">
                            <li><a class="dropdown-item" href="{{ route('admin.projects.index') }}">عرض الكل</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">إنشاء تقرير</a></li>
                        </ul>
                    </div> --}}
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>اسم المشروع</th>
                                <th>الفريق</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الأيام المتبقية</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($expiring_projects as $project)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="rounded-circle me-2"
                                                style="
              width: 24px; 
              height: 24px;
              background-color: {{ $project->color ?? '#696cff' }};
              display: inline-flex;
              align-items: center;
              justify-content: center;
          ">
                                            </span>
                                            <strong>{{ $project->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $project->team_count }} أعضاء</td>
                                    <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $project->days_remaining <= 7 ? 'warning' : 'success' }}">
                                            {{ number_format($project->days_remaining, 2) }} أيام
                                        </span>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد مشاريع قريبة من الانتهاء</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- قسم المهام الأخيرة -->
        <div class="col-xxl-6 col-md-12 mt-4">
            <div class="card ">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">آخر المهام المضافة</h5>

                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المهمة</th>
                                <th>المشروع</th>
                                <th>المسؤول</th>
                                <th>الحالة</th>
                                <th>الموعد النهائي</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($recent_tasks as $task)
                                <tr>
                                    <td>{{ $task->name }}</td>
                                    <td>{{ $task->projectTask->name ?? '--' }}</td>
                                    <td>
                                        <div class="avatar avatar-xs">
                                            <span
                                                class="avatar-initial rounded-circle bg-label-primary">{{ substr($task->user->name, 0, 1) }}</span>
                                        </div>
                                        {{ $task->user->name }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $task->status == 'completed'
                                                ? 'success'
                                                : ($task->status == 'in_progress'
                                                    ? 'info'
                                                    : ($task->status == 'pending'
                                                        ? 'warning'
                                                        : 'danger')) }}">
                                            {{ __($task->status) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد مهام مضافة حديثاً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // فلتر سريع حسب الأيام
        document.querySelectorAll('.dropdown-item[data-days]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const days = this.getAttribute('data-days');
                window.location.href = "{{ route('admin.dashboard') }}?days=" + days;
            });
        });

        // فلتر الشهر الحالي أو السابق
        document.querySelectorAll('.dropdown-item[data-month]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const monthType = this.getAttribute('data-month');
                window.location.href = "{{ route('admin.dashboard') }}?month=" + monthType;
            });
        });
    });
</script>
