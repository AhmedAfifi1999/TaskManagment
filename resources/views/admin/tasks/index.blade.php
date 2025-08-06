@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center"
                        style="border-right: 5px solid {{ $project->color }};">
                        جميع مهام المشروع: {{ $project->name }}
                        <div>
                            <a href="{{ route('admin.projects.tasks.create', $project->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>إضافة مهمة
                            </a>
                        </div>
                    </h5>
                    <div class="card-body">
                        <!-- فلتر الحالة -->
                        <div class="mb-4">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary filter-btn active"
                                    data-status="all">الكل</button>
                                <button type="button" class="btn btn-outline-secondary filter-btn"
                                    data-status="not_started">لم تبدأ</button>
                                <button type="button" class="btn btn-outline-warning filter-btn"
                                    data-status="in_progress">قيد التنفيذ</button>
                                <button type="button" class="btn btn-outline-success filter-btn"
                                    data-status="completed">مكتملة</button>
                            </div>
                        </div>
                    </div>
                    <!-- جدول المهام مع إمكانية السحب -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tasks-table">
                            <thead>
                                <tr>
                                    <th width="50px">#</th>
                                    <th>اسم المهمة</th>
                                    <th>المسؤول</th>
                                    <th>الحالة</th>
                                    <th>المواعيد</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-tasks">
                                @foreach ($tasks as $task)
                                    <tr class="task-row" data-task-id="{{ $task->id }}"
                                        data-status="{{ $task->status }}">
                                        <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}
                                        </td>
                                        <td><strong>{{ $task->name }}</strong></td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($task->user)
                                                    <img src="{{ $task->user->image ? asset($task->user->image) : asset('cp/assets/img/avatars/1.png') }}"
                                                        alt="Avatar" class="rounded-circle me-2" width="30"
                                                        height="30">
                                                    <span>{{ $task->user->name }}</span>
                                                @else
                                                    <span class="text-muted">No assigned user</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <select class="form-select status-select" data-task-id="{{ $task->id }}"
                                                style="width: 140px">
                                                <option value="not_started"
                                                    {{ $task->status == 'not_started' ? 'selected' : '' }}>لم تبدأ
                                                </option>
                                                <option value="in_progress"
                                                    {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ
                                                </option>
                                                <option value="completed"
                                                    {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="small">
                                                @php
                                                    // حساب نسبة التقدم
                                                    $progressPercentage = 0;
                                                    $progressColor = 'secondary';

                                                    if ($task->start_time && $task->end_time) {
                                                        $now = now();
                                                        $start = \Carbon\Carbon::parse($task->start_time);
                                                        $end = \Carbon\Carbon::parse($task->end_time);

                                                        if ($now->greaterThanOrEqualTo($start)) {
                                                            if ($now->greaterThanOrEqualTo($end)) {
                                                                $progressPercentage = 100;
                                                            } else {
                                                                $totalDuration = $end->diffInSeconds($start);
                                                                $elapsed = $now->diffInSeconds($start);
                                                                $progressPercentage = min(
                                                                    100,
                                                                    round(($elapsed / $totalDuration) * 100),
                                                                );
                                                            }
                                                        }

                                                        // تحديد لون الشريط حسب النسبة
                                                        if ($progressPercentage >= 80) {
                                                            $progressColor = 'danger';
                                                        } elseif ($progressPercentage >= 50) {
                                                            $progressColor = 'warning';
                                                        } else {
                                                            $progressColor = 'success';
                                                        }
                                                    }
                                                @endphp

                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span><i class="far fa-calendar-alt me-2"></i> بداية:
                                                            {{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('Y-m-d') : 'غير محدد' }}
                                                        </span>
                                                        <span class="text-muted">{{ $progressPercentage }}%</span>
                                                    </div>

                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $progressColor }}"
                                                            role="progressbar" style="width: {{ $progressPercentage }}%"
                                                            aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>

                                                    <div class=" mt-1">
                                                        <span><i class="far fa-calendar-alt me-2"></i> نهاية:
                                                            {{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('Y-m-d') : 'غير محدد' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.projects.tasks.edit', ['project' => $project->id, 'task' => $task->id]) }}">
                                                            <i class="fas fa-edit me-2"></i> تعديل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="dropdown-item text-danger delete-task-btn">
                                                            <i class="fas fa-trash me-2 text-danger"></i> <span
                                                                class="text-danger">حذف</span>
                                                        </a>
                                                        <form class="d-none delete-task-form"
                                                            action="{{ route('admin.projects.tasks.destroy', ['project' => $project->id, 'task' => $task->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </li>
                                                </ul>

                                                <style>
                                                    .dropdown-item.text-danger:hover {
                                                        background-color: rgba(220, 53, 69, 0.1) !important;
                                                    }
                                                </style>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <!-- في قسم Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        @if ($tasks->hasPages())
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($tasks->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $tasks->previousPageUrl() }}"
                                                rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($tasks->getUrlRange(1, $tasks->lastPage()) as $page => $url)
                                        @if ($page == $tasks->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($tasks->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $tasks->nextPageUrl() }}"
                                                rel="next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">&raquo;</span>
                                        </li>
                                    @endif

                                </ul>
                            </nav>
                        @endif
                        <div class="d-flex justify-end-end mb-3">
                            <select class="form-select form-select-sm" id="per-page-select" style="width: 80px">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // فلترة المهام حسب الحالة
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const status = this.dataset.status;
                const rows = document.querySelectorAll('.task-row');

                rows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // تحديث حالة المهمة
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                const newStatus = this.value;
                const originalStatus = this.dataset.originalStatus || this.value;

                // إظهار مؤتحميل
                const spinner =
                    '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
                this.disabled = true;
                this.nextElementSibling?.remove();
                this.insertAdjacentHTML('afterend', spinner);

                fetch(`/admin/projects/{{ $project->id }}/tasks/${taskId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: newStatus,
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const row = document.querySelector(
                                `.task-row[data-task-id="${taskId}"]`);
                            if (row) {
                                row.dataset.status = newStatus;
                                updateRowStyle(row, newStatus);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'تمت العملية',
                                text: 'تم تحديث حالة المهمة بنجاح',
                                toast: true,
                                position: 'top-start',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            throw new Error(data.message || 'حدث خطأ أثناء التحديث');
                        }
                    })
                    .catch(error => {
                        this.value = originalStatus;
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        this.disabled = false;
                        document.querySelector('.spinner-border')?.remove();
                    });
            });

            // حفظ الحالة الأصلية
            select.dataset.originalStatus = select.value;
        });

        // دالة لتحديث نمط الصف حسب الحالة
        function updateRowStyle(row, status) {
            row.classList.remove(
                'task-not-started',
                'task-in-progress',
                'task-completed'
            );

            switch (status) {
                case 'not_started':
                    row.classList.add('task-not-started');
                    break;
                case 'in_progress':
                    row.classList.add('task-in-progress');
                    break;
                case 'completed':
                    row.classList.add('task-completed');
                    break;
            }
        }
        // تأكيد الحذف مع SweetAlert
        document.querySelectorAll('.delete-task-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('li').querySelector('.delete-task-form');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن هذا الإجراء!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        // تأكيد الحذف
        const deleteForms = document.querySelectorAll('.delete-task-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع عن هذا الإجراء!",
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

        // السحب والإفلات مع Pagination
        const tbody = document.getElementById('sortable-tasks');
        if (tbody) {
            new Sortable(tbody, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    updateTasksOrder();
                }
            });
        }
        document.getElementById('per-page-select').addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
        // دالة لتحديث ترتيب المهام
        function updateTasksOrder() {
            const taskIds = [];
            document.querySelectorAll('#sortable-tasks .task-row').forEach(row => {
                taskIds.push(row.dataset.taskId);
            });

            fetch('{{ route('admin.projects.tasks.reorder', $project->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        taskIds: taskIds,
                        page: {{ $tasks->currentPage() }} // إرسال رقم الصفحة الحالية
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تمت العملية',
                            text: 'تم تحديث ترتيب المهام بنجاح',
                            toast: true,
                            position: 'top-start',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        // تحديث أرقام الصفوف
                        document.querySelectorAll('#sortable-tasks tr').forEach((row, index) => {
                            const currentPage = {{ $tasks->currentPage() }};
                            const perPage = {{ $tasks->perPage() }};
                            row.cells[0].textContent = (currentPage - 1) * perPage + index + 1;
                        });
                    } else {
                        toastr.error('حدث خطأ أثناء تحديث الترتيب');
                    }
                });
        }
    });
</script>

<style>
    .task-row[data-status="completed"] {
        background-color: #f8fff8;
    }

    .task-row[data-status="pending"] {
        background-color: #fff8f8;
    }

    .filter-btn.active {
        border-color: #696cff;
        background-color: rgba(105, 108, 255, 0.1);
    }

    .swal2-popup .swal2-deny {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    /* تخصيص Pagination */
    .pagination {
        justify-content: center;
    }

    .pagination .page-item.active .page-link {
        background-color: #696cff;
        border-color: #696cff;
    }

    .pagination .page-link {
        color: #696cff;
    }
</style>
