@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- العنوان وزر الإضافة -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h4 class="fw-bold mb-0">نظرة عامة على حالة المشاريع</h4>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> إضافة مشروع جديد
            </a>
        </div>

        <!-- قسم الإحصائيات السريعة (Dashboard Widgets) -->
        <div class="row mb-5">
            <!-- كارت إجمالي المشاريع -->
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $stats['total'] ?? 0 }}</h4>
                            <p class="mb-0 text-muted small">إجمالي المشاريع</p>
                        </div>
                        <div class="avatar bg-label-primary p-2 rounded">
                            <i class="ti ti-briefcase font-medium-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارت قيد التنفيذ -->
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-info">{{ $stats['in_progress'] ?? 0 }}</h4>
                            <p class="mb-0 text-muted small">قيد التنفيذ</p>
                        </div>
                        <div class="avatar bg-label-info p-2 rounded">
                            <i class="ti ti-settings font-medium-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارت المشاريع المتأخرة -->
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-danger">{{ $stats['delayed'] ?? 0 }}</h4>
                            <p class="mb-0 text-muted small">مشاريع متأخرة</p>
                        </div>
                        <div class="avatar bg-label-danger p-2 rounded">
                            <i class="ti ti-alert-triangle font-medium-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- كارت المشاريع المكتملة -->
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-success h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-success">{{ $stats['completed'] ?? 0 }}</h4>
                            <p class="mb-0 text-muted small">المشاريع المكتملة</p>
                        </div>
                        <div class="avatar bg-label-success p-2 rounded">
                            <i class="ti ti-circle-check font-medium-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- شبكة عرض المشاريع (Cards Grid) -->
        <div class="row">
            @forelse ($projects as $project)
                @php
                    // فحص إذا كان المشروع متأخراً منطقياً عن تاريخ اليوم
                    $isDelayed = $project->status !== 'COMPLETED' && $project->end_date && \Carbon\Carbon::parse($project->end_date)->isPast();
                    
                    // حساب نسبة المهام المكتملة لتمريرها لشريط التقدم
                    $totalTasks = $project->tasks_count ?? 0;
                    $completedTasks = $project->completed_tasks_count ?? 0;
                    $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp

                <div class="col-md-6 col-lg-4 mb-4">
                    <!-- تم ترتيب الكروت تلقائياً حسب الأحدث بناءً على استعلام الكنترولر -->
                    <div class="card h-100 shadow-sm border-start border-4 transition-card" style="border-color: {{ $project->color ?? '#7367f0' }} !important;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            
                            <!-- الرأس: الاسم والحالة المتاحة في الـ enum -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1 fw-bold text-truncate" style="max-width: 200px;" title="{{ $project->name }}">
                                        {{ $project->name }}
                                    </h5>
                                    <small class="text-muted">بواسطة: {{ $project->creator->name ?? 'غير معروف' }}</small>
                                </div>
                                <div>
                                    @switch($project->status)
                                        @case('NOT_STARTED') <span class="badge bg-label-secondary">لم يبدأ بعد</span> @break
                                        @case('IN_PROGRESS') <span class="badge bg-label-primary">قيد التنفيذ</span> @break
                                        @case('COMPLETED') <span class="badge bg-label-success">مكتمل</span> @break
                                        @case('ON_HOLD') <span class="badge bg-label-warning">معلق</span> @break
                                        @case('DELAYED') <span class="badge bg-label-danger">متأخر</span> @break
                                    @endswitch
                                </div>
                            </div>

                            <!-- التواريخ والميزانية -->
                            <div class="bg-light p-3 rounded mb-3 small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>📅 تاريخ البدء:</span>
                                    <span class="fw-medium text-dark">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '--' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>🏁 تاريخ الانتهاء:</span>
                                    <span class="fw-medium text-dark">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '--' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>💰 الميزانية:</span>
                                    <span class="fw-medium text-success">{{ $project->budget ?? '--' }} {{ $project->currency ?? '' }}</span>
                                </div>
                            </div>

                            <!-- فريق العمل التابع للمشروع -->
                            <div class="mb-3">
                                <h6 class="small fw-bold text-muted mb-2">فريق العمل:</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse ($project->team as $member)
                                        <span class="badge bg-label-info font-size-xs">{{ $member->name }}</span>
                                    @empty
                                        <span class="text-muted small">لا يوجد فريق مخصص</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- العداد الذكي للمهام وشريط التقدم -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-muted">📋 المهام: <strong>{{ $completedTasks }}</strong> من <strong>{{ $totalTasks }}</strong> إجمالي</span>
                                    <span class="fw-bold text-primary">{{ $progressPercentage }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <!-- تنبيه مباشر في حال تخطي تاريخ التسليم المنطقي -->
                                @if($isDelayed)
                                    <div class="text-danger small mt-2 d-flex align-items-center gap-1 fw-medium">
                                        <i class="ti ti-alert-triangle"></i>
                                        <span>⚠️ تجاوز تاريخ الانتهاء المحدد!</span>
                                    </div>
                                @endif
                            </div>

                            <!-- الإجراءات السفلية وزر الانتقال المباشر لصفحة المهام -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <!-- زر التوجيه لصفحة المهام الخاصة بالمشروع المعني -->
                                <a href="{{ route('admin.projects.tasks.index', $project->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                    <i class="ti ti-layout-list"></i> عرض مهام المشروع
                                </a>

                                <!-- الأزرار الفرعية (الملفات + العمليات الأساسية) -->
                                <div class="d-flex align-items-center gap-2">
                                    @if ($project->attachment)
                                        <a href="{{ asset('storage/' . $project->attachment) }}" class="btn btn-sm btn-icon btn-outline-secondary" target="_blank" title="عرض ملف المشروع">
                                            <i class="ti ti-file"></i>
                                        </a>
                                    @endif

                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @can('update', $project)
                                                <a class="dropdown-item" href="{{ route('admin.projects.edit', $project->id) }}">
                                                    <i class="ti ti-pencil me-1"></i> تعديل البيانات
                                                </a>
                                            @endcan

                                            @can('delete', $project)
                                                <form class="delete-project-form d-inline" method="POST" action="{{ route('admin.projects.destroy', $project->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="ti ti-trash me-1"></i> حذف المشروع
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center my-5 card p-5 shadow-sm border-0">
                    <div class="text-muted"><i class="ti ti-box font-large-2 mb-2 d-block"></i> لا توجد مشاريع مضافة حالياً.</div>
                </div>
            @endforelse
        </div>

        <!-- الترقيم والتحكم بعدد العناصر في الصفحة -->
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="per-page-select" class="small text-muted text-nowrap">عرض بقيمة:</label>
                    <select class="form-select form-select-sm" id="per-page-select" style="width: 80px">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                
                <div class="d-flex justify-content-center">
                    {!! $projects->appends(request()->query())->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // حماية مخصصة لعمليات الحذف عبر SweetAlert
        const deleteForms = document.querySelectorAll('.delete-project-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع بعد حذف المشروع!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // آلية تغيير كمية العرض في الصفحة تزامناً مع الباجينيشن
        document.getElementById('per-page-select').addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
    });
</script>

<style>
    .swal2-popup .swal2-deny {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    .font-size-xs {
        font-size: 0.75rem;
    }
    .transition-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
    }
</style>