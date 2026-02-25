@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-6">
        <div class="col-md">
            <div class="card">

                <h5 class="card-header d-flex justify-content-between align-items-center">
                    قائمة الأدوار
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        إضافة دور جديد
                    </button>
                </h5>

                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>اسم الدور</th>
                                <th>عدد الصلاحيات</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $role->permissions->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu">

                                                <button class="dropdown-item edit-role-btn"
                                                    data-id="{{ $role->id }}"
                                                    data-name="{{ $role->name }}"
                                                    data-permissions='@json($role->permissions->pluck("name"))'
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editRoleModal">
                                                    <i class="ti ti-pencil me-1"></i> تعديل
                                                </button>

                                                <form method="POST"
                                                    action="{{ route('admin.roles.destroy', $role->id) }}"
                                                    class="delete-role-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="dropdown-item text-danger">
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

                <div class="mt-4 d-flex justify-content-center">
                    {{ $roles->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ======================= CREATE ROLE MODAL ======================= --}}
<div class="modal fade" id="createRoleModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>إضافة دور جديد</h5>
                </div>
                <div class="modal-body">

                    <div class="mb-4">
                        <label class="form-label">اسم الدور</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    @foreach ($permissions as $module => $modulePermissions)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-primary mb-0">{{ ucfirst($module) }}</h6>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary select-all-btn"
                                    data-module="{{ $module }}">
                                    تحديد الكل
                                </button>
                            </div>

                            <div class="row">
                                @foreach ($modulePermissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->name }}"
                                                class="form-check-input module-{{ $module }}">
                                            <label class="form-check-label">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- ======================= EDIT ROLE MODAL ======================= --}}
<div class="modal fade" id="editRoleModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editRoleForm">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5>تعديل الدور</h5>
                </div>
                <div class="modal-body">

                    <div class="mb-4">
                        <label class="form-label">اسم الدور</label>
                        <input type="text" name="name" id="editRoleName" class="form-control" required>
                    </div>

                    @foreach ($permissions as $module => $modulePermissions)
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary">{{ ucfirst($module) }}</h6>
                            <div class="row">
                                @foreach ($modulePermissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->name }}"
                                                class="form-check-input edit-permission-checkbox module-{{ $module }}">
                                            <label class="form-check-label">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection


{{-- ======================= JAVASCRIPT ======================= --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Edit Role
    document.querySelectorAll('.edit-role-btn').forEach(button => {
        button.addEventListener('click', function() {

            let id = this.dataset.id;
            let name = this.dataset.name;
            let permissions = JSON.parse(this.dataset.permissions);

            document.getElementById('editRoleName').value = name;

            let form = document.getElementById('editRoleForm');
            form.action = `/admin/roles/${id}`;

            document.querySelectorAll('.edit-permission-checkbox').forEach(cb => {
                cb.checked = permissions.includes(cb.value);
            });
        });
    });

    // Delete confirmation
    document.querySelectorAll('.delete-role-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع بعد الحذف!",
                icon: 'warning',
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

    // Select All per Module
    document.querySelectorAll('.select-all-btn').forEach(button => {
        button.addEventListener('click', function() {
            let module = this.dataset.module;
            document.querySelectorAll('.module-' + module).forEach(cb => {
                cb.checked = true;
            });
        });
    });

});
</script>