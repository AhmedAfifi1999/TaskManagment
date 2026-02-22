@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-6">
        <div class="col-md">
            <div class="card">
                <h5 class="card-header">إضافة مشروع جديد</h5>

                <div class="card-body">

                    {{-- عرض أخطاء التحقق --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('admin.projects.store') }}"
                          enctype="multipart/form-data"
                          class="needs-validation"
                          novalidate>

                        @csrf

                        <!-- اسم المشروع ولونه -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">اسم المشروع</label>
                                <input type="text"
                                       class="form-control"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">لون المشروع</label>
                                <input type="color"
                                       class="form-control form-control-color"
                                       name="color"
                                       value="{{ old('color', '#7367f0') }}"
                                       style="width:95%">
                            </div>
                        </div>

                        <!-- وصف المشروع -->
                        <div class="mb-3">
                            <label class="form-label">وصف المشروع</label>
                            <textarea class="form-control"
                                      name="description"
                                      rows="3">{{ old('description') }}</textarea>
                        </div>

                        <!-- تواريخ المشروع -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date"
                                       class="form-control"
                                       name="start_date"
                                       value="{{ old('start_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="date"
                                       class="form-control"
                                       name="end_date"
                                       value="{{ old('end_date') }}">
                            </div>
                        </div>

                        <!-- حالة المشروع -->
                        <div class="mb-3">
                            <label class="form-label">حالة المشروع</label>
                            <select class="form-select" name="status" required>
                                @foreach([
                                    'NOT_STARTED'=>'لم يبدأ بعد',
                                    'IN_PROGRESS'=>'قيد التنفيذ',
                                    'COMPLETED'=>'مكتمل',
                                    'ON_HOLD'=>'معلق',
                                ] as $value => $label)

                                    <option value="{{ $value }}"
                                        {{ old('status', 'NOT_STARTED') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>

                                @endforeach
                            </select>
                        </div>

                        <!-- الميزانية -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ميزانية المشروع</label>
                                <input type="number"
                                       step="0.01"
                                       class="form-control"
                                       name="budget"
                                       value="{{ old('budget') }}"
                                       placeholder="مثال: 10000">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العملة</label>
                                <select class="form-select" name="currency">
                                    <option value="">اختر العملة</option>
                                    @foreach(['USD','EUR','ILS'] as $currency)
                                        <option value="{{ $currency }}"
                                            {{ old('currency') == $currency ? 'selected' : '' }}>
                                            {{ $currency }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- فريق العمل -->
                        <div class="mb-3">
                            <label class="form-label">فريق العمل</label>
                            <select name="team[]" class="select2 form-select" multiple required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ collect(old('team'))->contains($user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ملف المشروع -->
                        <div class="mb-3">
                            <label class="form-label">ملف المشروع (PDF / Word)</label>
                            <input type="file"
                                   class="form-control"
                                   name="attachment"
                                   accept=".pdf,.doc,.docx">
                            <small class="text-muted">
                                الحد الأقصى 2MB — PDF أو Word فقط
                            </small>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                إضافة المشروع
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection