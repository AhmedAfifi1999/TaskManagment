@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row mb-6">
        <div class="col-md">

            <div class="card">

                <h5 class="card-header">
                    تفاصيل المهمة
                </h5>

                <div class="card-body">

                    <div class="row mb-3">

                        <!-- اسم المهمة -->
                        <div class="col-md-6">
                            <label class="form-label">اسم المهمة</label>
                            <input type="text" class="form-control"
                                   value="{{ $task->name }}" readonly>
                        </div>


                        <!-- الحالة -->
                        <div class="col-md-6">
                            <label class="form-label">الحالة</label>

                            <input type="text" class="form-control"
                            value="
                            @switch($task->status)
                                @case('not_started')
                                    لم تبدأ
                                    @break

                                @case('in_progress')
                                    قيد التنفيذ
                                    @break

                                @case('completed')
                                    مكتملة
                                    @break
                            @endswitch
                            "
                            readonly>
                        </div>

                    </div>


                    <div class="row mb-3">

                        <!-- الأولوية -->
                        <div class="col-md-6">
                            <label class="form-label">الأولوية</label>

                            <input type="text" class="form-control"
                            value="{{ $task->priority }}"
                            readonly>

                        </div>


                        <!-- الشخص المسؤول -->
                        <div class="col-md-6">
                            <label class="form-label">المسؤول عن المهمة</label>

                            <input type="text" class="form-control"
                            value="{{ $task->user?->name ?? '-' }}"
                            readonly>

                        </div>

                    </div>



                    <!-- الوصف -->
                    <div class="mb-3">

                        <label class="form-label">
                            وصف المهمة
                        </label>

                        <textarea class="form-control"
                                  rows="5"
                                  readonly>{{ $task->description }}</textarea>

                    </div>



                    <div class="row mb-3">

                        <!-- تاريخ البداية -->
                        <div class="col-md-6">

                            <label class="form-label">
                                تاريخ البداية
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="{{ $task->start_time }}"
                                   readonly>

                        </div>


                        <!-- تاريخ النهاية -->
                        <div class="col-md-6">

                            <label class="form-label">
                                تاريخ النهاية
                            </label>

                            <input type="text"
                                   class="form-control"
                                   value="{{ $task->end_time }}"
                                   readonly>

                        </div>

                    </div>



                    <!-- المشروع -->
                    <div class="mb-3">

                        <label class="form-label">
                            المشروع
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $task->projectTask->name ?? '-' }}"
                               readonly>

                    </div>



                    <!-- منشئ المشروع -->
                    <div class="mb-3">

                        <label class="form-label">
                            صاحب المشروع
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $task->projectTask->creator->name ?? '-' }}"
                               readonly>

                    </div>


                    <div class="text-end">

                        <a href="{{ route('admin.projects.tasks.index',$task->project) }}"
                           class="btn btn-secondary">
                            رجوع
                        </a>

                    </div>


                </div>

            </div>

        </div>
    </div>

</div>
@endsection