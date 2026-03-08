@extends('admin.layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    {{-- اختيار المشروع --}}
    <div class="mb-4">
        <form method="GET" id="projectSelectForm">
            <select name="project_id" class="form-select w-25"
                onchange="document.getElementById('projectSelectForm').action='/admin/projects/' + this.value + '/tasks/display'; this.form.submit();">
                @foreach ($projects as $projectItem)
                    <option value="{{ $projectItem->id }}" {{ $project->id == $projectItem->id ? 'selected' : '' }}>
                        {{ $projectItem->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Kanban Board --}}
    <div class="row g-3">
        @foreach (['not_started' => 'لم تبدأ', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتملة'] as $key => $label)
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header text-center fw-bold text-white" 
                         style="background-color: {{ $key == 'not_started' ? 'var(--bs-danger)' : ($key == 'in_progress' ? 'var(--bs-warning)' : 'var(--bs-success)') }}">
                        {{ $label }}
                    </div>

                    {{-- Tasks --}}
                    <div class="card-body kanban-column p-2" data-status="{{ $key }}" style="min-height: 350px; background-color: var(--bs-body-bg); border-radius: 5px;">
                        @foreach ($tasks->where('status', $key) as $task)
                            <div class="card mb-3 task-card shadow" data-id="{{ $task->id }}" style="cursor: grab;">
                                <div class="card-body p-3 bg-body-tertiary rounded text-body">
                                    {{-- Task Name --}}
                                    <h6 class="fw-bold">{{ $task->name }}</h6>

                                    {{-- Project --}}
                                    <div class="d-flex align-items-center mb-1 small text-muted">
                                        <i class="bi bi-diagram-3 me-2"></i> {{ $task->projectTask->name }}
                                    </div>

                                    {{-- Dates --}}
                                    <div class="d-flex justify-content-between mb-1 small text-muted">
                                        <div><i class="bi bi-calendar-event me-1"></i> {{ $task->start_date ? $task->start_date->format('Y-m-d') : '-' }}</div>
                                        <div><i class="bi bi-calendar-event-fill me-1"></i> {{ $task->end_date ? $task->end_date->format('Y-m-d') : '-' }}</div>
                                    </div>

                                    {{-- Responsible --}}
                                    <div class="d-flex align-items-center mb-2 small text-muted">
                                        <i class="bi bi-person-circle me-2"></i> {{ $task->user->name ?? '-' }}
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="d-flex justify-content-between mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill me-1" data-bs-toggle="modal" data-bs-target="#taskModal{{ $task->id }}">
                                            عرض
                                        </button>
                                        <a href="" class="btn btn-sm btn-outline-warning flex-fill me-1">
                                            تعديل
                                        </a>
                                        <form action="" method="POST" class="flex-fill m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                حذف
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Modal التفاصيل --}}
                                    <div class="modal fade" id="taskModal{{ $task->id }}" tabindex="-1" aria-labelledby="taskModalLabel{{ $task->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title" id="taskModalLabel{{ $task->id }}">{{ $task->name }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-body text-body">
                                                    <div class="row mb-2">
                                                        <div class="col-md-6"><strong>المشروع:</strong> {{ $task->projectTask->name }}</div>
                                                        <div class="col-md-6"><strong>الحالة:</strong> {{ $task->status }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-6"><strong>تاريخ البداية:</strong> {{ $task->start_date ? $task->start_date->format('Y-m-d') : '-' }}</div>
                                                        <div class="col-md-6"><strong>تاريخ النهاية:</strong> {{ $task->end_date ? $task->end_date->format('Y-m-d') : '-' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-6"><strong>المسؤول:</strong> {{ $task->user->name ?? '-' }}</div>
                                                    </div>
                                                    <hr>
                                                    <p><strong>الوصف:</strong></p>
                                                    <p>{{ $task->description ?? '-' }}</p>
                                                </div>
                                                <div class="modal-footer bg-body text-body">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- نهاية Modal --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- SortableJS --}}
<script src="{{ asset('cp/assets/vendor/libs/sortablejs/sortable.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        new Sortable(column, {
            group: 'tasks',
            animation: 150,
            onAdd: function(evt) {
                const taskId = evt.item.dataset.id;
                const newStatus = evt.to.dataset.status;

                fetch(`/admin/projects/{{ $project->id }}/tasks/${taskId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                }).then(res => res.json()).then(data => {
                    if (data.success) console.log('Task status updated');
                }).catch(err => console.error('Error updating task status:', err));
            }
        });
    });
});
</script>

@endsection