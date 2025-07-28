@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-6">
            <div class="col-md">
                <div class="card">
                    <h5 class="card-header">تعديل المشروع</h5>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.projects.update', $project->id) }}" enctype="multipart/form-data"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- اسم المشروع ولونه -->
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label" for="name">اسم المشروع</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="اسم المشروع" value="{{ old('name', $project->name) }}" required>
                                    <div class="invalid-feedback">يرجى إدخال اسم المشروع</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="color">لون المشروع</label>
                                    <input type="color" class="form-control form-control-color" id="color"
                                        name="color" value="{{ old('color', $project->color) }}" title="اختر لون المشروع" style="width:95%">
                                </div>
                            </div>

                            <!-- وصف المشروع -->
                            <div class="mb-3">
                                <label class="form-label" for="description">وصف المشروع</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="وصف المشروع (اختياري)">{{ old('description', $project->description) }}</textarea>
                            </div>

                            <!-- تواريخ المشروع -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="start_date">تاريخ البدء</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ old('start_date', $project->start_date) }}" required>
                                    <div class="invalid-feedback">يرجى تحديد تاريخ البدء</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="end_date">تاريخ الانتهاء</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ old('end_date', $project->end_date) }}">
                                </div>
                            </div>

                            <!-- حالة المشروع -->
                            <div class="mb-3">
                                <label class="form-label" for="status">حالة المشروع</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="NOT_STARTED" {{ old('status', $project->status) == 'NOT_STARTED' ? 'selected' : '' }}>لم يبدأ بعد</option>
                                    <option value="IN_PROGRESS" {{ old('status', $project->status) == 'IN_PROGRESS' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="COMPLETED" {{ old('status', $project->status) == 'COMPLETED' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="ON_HOLD" {{ old('status', $project->status) == 'ON_HOLD' ? 'selected' : '' }}>معلق</option>
                                    <option value="DELAYED" {{ old('status', $project->status) == 'DELAYED' ? 'selected' : '' }}>متأخر</option>
                                </select>
                                <div class="invalid-feedback">يرجى تحديد حالة المشروع</div>
                            </div>

                            <!-- فريق العمل -->
                            <div class="mb-3">
                                <label for="team" class="form-label">فريق العمل</label>
                                <select id="team" name="team[]" class="select2 form-select" multiple required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ in_array($user->id, old('team', $project->team->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">يرجى تحديد فريق العمل</div>
                            </div>

                            <!-- زر الإرسال -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // التحقق من أن تاريخ الانتهاء بعد تاريخ البدء
            $('#start_date, #end_date').change(function() {
                const startDate = new Date($('#start_date').val());
                const endDate = new Date($('#end_date').val());

                if (startDate && endDate && endDate < startDate) {
                    alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
                    $('#end_date').val('');
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
@endsection