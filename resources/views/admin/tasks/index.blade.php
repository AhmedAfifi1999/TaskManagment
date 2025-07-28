@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
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
                                    @foreach ($project->tasks->sortBy('order') as $task)
                                        <tr class="task-row" data-task-id="{{ $task->id }}"
                                            data-status="{{ $task->status }}">
                                            <!-- باقي محتوى الصف -->
                                            <td>{{ $loop->iteration }}</td>
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
                                                                role="progressbar"
                                                                style="width: {{ $progressPercentage }}%"
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
                                                            <form class="dropdown-item delete-task-form"
                                                                action="{{ route('admin.projects.tasks.destroy', ['project' => $project->id, 'task' => $task->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-link p-0 text-danger">
                                                                    <i class="fas fa-trash me-2"></i> حذف
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        // تحديث حالة المهمة
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                console.log('change status');
                const taskId = this.dataset.taskId;
                console.log('taskId :' + taskId);

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
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // تحديث واجهة المستخدم
                            const row = document.querySelector(
                                `.task-row[data-task-id="${taskId}"]`);
                            if (row) {
                                console.log('change row style')
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
                        // toastr.error(error.message);
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
            // إزالة جميع كلاسات الحالة
            console.log('change row style' + row)
            console.log('change row style' + status)

            row.classList.remove(
                'task-not-started',
                'task-in-progress',
                'task-completed'
            );

            // إضافة الكلاس المناسب
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


        const tbody = document.getElementById('sortable-tasks');

        new Sortable(tbody, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                console.log('change order');

                updateTasksOrder();
            }
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
                        taskIds: taskIds
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
                            row.cells[0].textContent = index + 1;
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
</style>
