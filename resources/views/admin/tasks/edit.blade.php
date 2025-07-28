@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-6">
            <div class="col-md">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center"
                        style="border-right: 5px solid {{ $project->color }};">
                        تعديل المهمة: {{ $task->name }}
                        <a href="{{ route('admin.projects.tasks.index', $project->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
                        </a>
                    </h5>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.projects.tasks.update', [$project->id, $task->id]) }}"
                            enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- معلومات أساسية -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label" for="name">عنوان المهمة</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $task->name) }}" placeholder="عنوان المهمة" required>
                                    <div class="invalid-feedback">يرجى إدخال عنوان المهمة</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="priority">الأولوية</label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}"
                                                {{ $task->priority == $i ? 'selected' : '' }}>
                                                {{ $i }} -
                                                {{ $i == 1 ? 'أعلى أولوية' : ($i == 10 ? 'أقل أولوية' : '') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- وصف المهمة -->
                            <div class="mb-3">
                                <label class="form-label" for="description">وصف المهمة</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="وصف تفصيلي للمهمة (اختياري)">{{ old('description', $task->description) }}</textarea>
                            </div>

                            <!-- المسؤول عن المهمة -->
                            <div class="mb-3">
                                <label class="form-label" for="user_id">المسؤول عن المهمة</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">اختر المسؤول</option>
                                    @foreach ($project->team as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $task->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">يرجى تحديد المسؤول عن المهمة</div>
                            </div>

                            <!-- تواريخ المهمة -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="start_time">تاريخ البدء</label>
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time"
                                        value="{{ old('start_time', $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('Y-m-d\TH:i') : '') }}"
                                        required>

                                    <div class="invalid-feedback">يرجى تحديد تاريخ البدء</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="end_time">تاريخ الانتهاء</label>
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time"
                                        value="{{ old('end_time', $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>

                            <!-- حالة المهمة -->
                            <div class="mb-3">
                                <label class="form-label" for="status">حالة المهمة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="not_started" {{ $task->status == 'not_started' ? 'selected' : '' }}>لم
                                        تبدأ بعد</option>
                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد
                                        التنفيذ</option>
                                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة
                                    </option>
                                </select>
                            </div>

                            <!-- زر الإرسال -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>حفظ التعديلات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    $(document).ready(function() {
        // التحقق من أن تاريخ الانتهاء بعد تاريخ البدء
        $('#start_time, #end_time').change(function() {
            const startTime = new Date($('#start_time').val());
            const endTime = new Date($('#end_time').val());

            if (startTime && endTime && endTime < startTime) {
                alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
                $('#end_time').val('');
            }
        });

        // التحقق من الصحة
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })();
    });
</script>
