@extends('admin.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-6">
        <div class="col-md">
            <div class="card">
                <h5 class="card-header d-flex justify-content-between align-items-center">
                    قائمة المستخدمين
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">إضافة مستخدم جديد</a>
                </h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>اسم المستخدم</th>
                                <th>الصلاحيات</th>
                                <th>الحالة</th>
                                <th>البريد الإلكتروني</th>
                                <th>صورة المستخدم</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>مدير</td> {{-- عدل هذا لاحقًا حسب الصلاحيات --}}
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-label-success">فعال</span>
                                    @else
                                        <span class="badge bg-label-danger">غير فعال</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->image)
                                        <img src="{{ route('secure.image', $user->image) }}"
                                             alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px;" />
                                    @else
                                        <img src="{{ asset('cp/assets/img/avatars/1.png') }}"
                                             alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px;" />
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                <i class="ti ti-pencil me-1"></i> تعديل
                                            </a>
                                            <form class="delete-user-form" method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
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

                <!-- ✅ روابط الترقيم -->
                <div class="m-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-user-form');
    
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // إلغاء الإرسال العادي للفورم
    
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من التراجع بعد الحذف!",
                    icon: 'warning',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    showDenyButton: false,     // تأكد أنها false
                    denyButtonText: null,      // تجنب ظهور الزر
                    showCloseButton: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // إذا وافق، يتم إرسال الفورم
                    }
                });
            });
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
