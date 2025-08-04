@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-6">
            <div class="col-md">
                <div class="card">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        قائمة المشاريع
                        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">إضافة مشروع جديد</a>
                    </h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم المشروع</th>
                                    <th>لون المشروع</th>
                                    <th>منشئ المشروع</th>
                                    <th>فريق العمل</th>
                                    <th>حالة المشروع</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($projects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $project->color }}; color: white">
                                                {{ $project->color }}
                                            </span>
                                        </td>
                                        <td>{{ $project->creator->name ?? 'غير معروف' }}</td>
                                        <td>
                                            @foreach ($project->team as $member)
                                                <span class="badge bg-label-info me-1 mb-1">{{ $member->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @switch($project->status)
                                                @case('NOT_STARTED')
                                                    <span class="badge bg-label-secondary">لم يبدأ بعد</span>
                                                @break

                                                @case('IN_PROGRESS')
                                                    <span class="badge bg-label-primary">قيد التنفيذ</span>
                                                @break

                                                @case('COMPLETED')
                                                    <span class="badge bg-label-success">مكتمل</span>
                                                @break

                                                @case('ON_HOLD')
                                                    <span class="badge bg-label-warning">معلق</span>
                                                @break

                                                @case('DELAYED')
                                                    <span class="badge bg-label-danger">متأخر</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td class="small-date">
                                            {{ \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') }}</td>
                                        <td class="small-date">
                                            {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '--' }}
                                        </td>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.projects.tasks.index', $project->id) }}">
                                                        <i class="fas fa-tasks"></i> عرض المهام
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.projects.edit', $project->id) }}">
                                                        <i class="ti ti-pencil me-1"></i> تعديل
                                                    </a>
                                                    <form class="delete-project-form" method="POST"
                                                        action="{{ route('admin.projects.destroy', $project->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ti ti-trash me-1"></i> حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- روابط الترقيم -->
                    <!-- في قسم Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        @if ($projects->hasPages())
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($projects->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $projects->previousPageUrl() }}"
                                                rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                                        @if ($page == $projects->currentPage())
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
                                    @if ($projects->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $projects->nextPageUrl() }}"
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
</style>
