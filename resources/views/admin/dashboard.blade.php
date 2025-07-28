@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- فلتر التاريخ -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body">
                        <form id="dateFilterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="startDate" class="form-label">من تاريخ</label>
                                    <input type="date" class="form-control" id="startDate" name="startDate">
                                </div>
                                <div class="col-md-3">
                                    <label for="endDate" class="form-label">إلى تاريخ</label>
                                    <input type="date" class="form-control" id="endDate" name="endDate">
                                </div>
                                <div class="col-md-6 d-flex align-items-end justify-content-between flex-wrap">
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="ti ti-filter ti-xs me-1"></i> تصفية
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary">
                                            <i class="ti ti-refresh ti-xs me-1"></i> إعادة تعيين
                                        </button>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                            id="quickFilter" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-clock ti-xs me-1"></i> فلتر سريع
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="quickFilter">
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-days="7">آخر 7
                                                    أيام</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-days="30">آخر 30
                                                    يوم</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-days="90">آخر 3
                                                    أشهر</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-month="this">هذا
                                                    الشهر</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" data-month="last">الشهر
                                                    الماضي</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

      <div class="row g-6">
    <!-- بطاقة عدد المشاريع -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-primary mb-3 rounded">
                    <i class="ti ti-folders ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">إجمالي المشاريع</h5>
                <p class="card-subtitle">جميع المشاريع في النظام</p>
                <p class="text-heading mb-3 mt-1">42 مشروع</p>
                <div>
                    <span class="badge bg-label-primary">+12.5%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة المشاريع المنتهية -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-success mb-3 rounded">
                    <i class="ti ti-checkbox ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">المشاريع المنتهية</h5>
                <p class="card-subtitle">تم تسليمها بنجاح</p>
                <p class="text-heading mb-3 mt-1">18 مشروع</p>
                <div>
                    <span class="badge bg-label-success">+8.3%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة المشاريع قيد التنفيذ -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-warning mb-3 rounded">
                    <i class="ti ti-progress-check ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">قيد التنفيذ</h5>
                <p class="card-subtitle">المشاريع النشطة حالياً</p>
                <p class="text-heading mb-3 mt-1">15 مشروع</p>
                <div>
                    <span class="badge bg-label-warning">+5.7%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة عدد المهام -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-info mb-3 rounded">
                    <i class="ti ti-list-check ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">إجمالي المهام</h5>
                <p class="card-subtitle">جميع المهام في النظام</p>
                <p class="text-heading mb-3 mt-1">245 مهمة</p>
                <div>
                    <span class="badge bg-label-info">+15.2%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة عدد المستخدمين -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-secondary mb-3 rounded">
                    <i class="ti ti-users ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">المستخدمون</h5>
                <p class="card-subtitle">إجمالي المستخدمين في النظام</p>
                <p class="text-heading mb-3 mt-1">12 مستخدم</p>
                <div>
                    <span class="badge bg-label-secondary">+2.1%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة المهام المكتملة -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-success mb-3 rounded">
                    <i class="ti ti-circle-check ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">المهام المكتملة</h5>
                <p class="card-subtitle">تم إنهاؤها بنجاح</p>
                <p class="text-heading mb-3 mt-1">187 مهمة</p>
                <div>
                    <span class="badge bg-label-success">+10.5%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة المهام المتأخرة -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-danger mb-3 rounded">
                    <i class="ti ti-alert-circle ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">المهام المتأخرة</h5>
                <p class="card-subtitle">تجاوزت الموعد النهائي</p>
                <p class="text-heading mb-3 mt-1">23 مهمة</p>
                <div>
                    <span class="badge bg-label-danger">+3.2%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقة المشاريع المتعثرة -->
    <div class="col-xxl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="badge p-2 bg-label-danger mb-3 rounded">
                    <i class="ti ti-alert-triangle ti-28px"></i>
                </div>
                <h5 class="card-title mb-1">المشاريع المتعثرة</h5>
                <p class="card-subtitle">تواجه صعوبات في التنفيذ</p>
                <p class="text-heading mb-3 mt-1">4 مشاريع</p>
                <div>
                    <span class="badge bg-label-danger">+1.5%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- قسم المشاريع القريبة من الانتهاء -->
<div class="col-xxl-6 col-md-12 mt-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0">المشاريع القريبة من الانتهاء</h5>
            <div class="dropdown">
                <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1"
                    type="button" id="expiringProjectsMenu" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-md text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="expiringProjectsMenu">
                    <li><a class="dropdown-item" href="javascript:void(0);">عرض الكل</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);">إنشاء تقرير</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>اسم المشروع</th>
                        <th>الفريق</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الأيام المتبقية</th>
                        <th>حالة التقدم</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar-initial rounded bg-label-primary me-2">T</span>
                                <strong>تطوير النظام الجديد</strong>
                            </div>
                        </td>
                        <td>5 أعضاء</td>
                        <td>15/06/2023</td>
                        <td><span class="badge bg-label-warning">5 أيام</span></td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 85%"></div>
                            </div>
                            <small>85%</small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar-initial rounded bg-label-success me-2">M</span>
                                <strong>تطبيق الموبايل</strong>
                            </div>
                        </td>
                        <td>3 أعضاء</td>
                        <td>20/06/2023</td>
                        <td><span class="badge bg-label-warning">10 أيام</span></td>
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 72%"></div>
                            </div>
                            <small>72%</small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- قسم المهام الأخيرة -->
<div class="col-xxl-6 col-md-12 mt-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0">آخر المهام المضافة</h5>
            <div class="dropdown">
                <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1"
                    type="button" id="recentTasksMenu" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-md text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="recentTasksMenu">
                    <li><a class="dropdown-item" href="javascript:void(0);">عرض الكل</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);">إضافة مهمة</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>المهمة</th>
                        <th>المشروع</th>
                        <th>المسؤول</th>
                        <th>الحالة</th>
                        <th>الموعد النهائي</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <tr>
                        <td>تصميم واجهة المستخدم</td>
                        <td>تطبيق الموبايل</td>
                        <td>
                            <div class="avatar avatar-xs">
                                <span class="avatar-initial rounded-circle bg-label-primary">أ</span>
                            </div>
                            أحمد
                        </td>
                        <td><span class="badge bg-label-success">في التقدم</span></td>
                        <td>10/06/2023</td>
                    </tr>
                    <tr>
                        <td>اختبار النظام</td>
                        <td>تطوير النظام الجديد</td>
                        <td>
                            <div class="avatar avatar-xs">
                                <span class="avatar-initial rounded-circle bg-label-info">م</span>
                            </div>
                            محمد
                        </td>
                        <td><span class="badge bg-label-warning">معلقة</span></td>
                        <td>12/06/2023</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
@endsection
