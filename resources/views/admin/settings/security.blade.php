@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-6">
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.settings.account') }}">
                            <i class="ti-sm ti ti-users me-1_5"></i> الإعدادات العامة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.settings.security') }}">
                            <i class="ti-sm ti ti-lock me-1_5"></i> الحماية
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card mb-6">
                <h5 class="card-header">تغيير كلمة المرور</h5>
                <div class="card-body pt-1">
                    <form id="formAccountSettings" method="POST" action="{{ route('admin.settings.changePassword') }}"
                        class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate>
                        @csrf
                        <div class="row">
                            <div class="mb-6 col-md-6 form-password-toggle fv-plugins-icon-container">
                                <label class="form-label" for="currentPassword">كلمة المرور الحالية</label>
                                <div class="input-group input-group-merge has-validation">
                                    <input class="form-control" type="password" name="current_password" id="currentPassword"
                                        placeholder="············" required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-6 col-md-6 form-password-toggle fv-plugins-icon-container">
                                <label class="form-label" for="newPassword">كلمة المرور الجديدة</label>
                                <div class="input-group input-group-merge has-validation">
                                    <input class="form-control" type="password" id="newPassword" name="new_password"
                                        placeholder="············" required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>

                            <div class="mb-6 col-md-6 form-password-toggle fv-plugins-icon-container">
                                <label class="form-label" for="confirmPassword">تأكيد كلمة المرور الجديدة</label>
                                <div class="input-group input-group-merge has-validation">
                                    <input class="form-control" type="password" name="new_password_confirmation"
                                        id="confirmPassword" placeholder="············" required>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback"></div>
                            </div>
                        </div>
                        <h6 class="text-body">متطلبات كلمة المرور:</h6>
                        <ul class="ps-4 mb-0">
                            <li class="mb-4">8 أحرف على الأقل - كلما زادت الأحرف كان أفضل</li>
                            <li class="mb-4">حرف صغير واحد على الأقل</li>
                            <li>رقم أو رمز أو مسافة واحدة على الأقل</li>
                        </ul>
                        <div class="mt-6">
                            <button type="submit" class="btn btn-primary me-3 waves-effect waves-light">حفظ
                                التغييرات</button>
                            <button type="reset" class="btn btn-label-secondary waves-effect">إعادة تعيين</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
