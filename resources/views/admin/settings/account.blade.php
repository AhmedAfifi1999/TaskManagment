@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-6">

            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);">
                            <i class="ti-sm ti ti-users me-1_5"></i> الإعدادات العامة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('admin.settings.security') }}">

                            <i class="ti-sm ti ti-lock me-1_5"></i> الحماية
                        </a>
                    </li>

                </ul>
            </div>

            <div class="col-md">
                <div class="card">
                    <h5 class="card-header">تعديل بيانات المستخدم</h5>
                    <div class="card-body">

                        <form method="POST" action="{{ route('admin.settings.accountUpdate') }}"
                            enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf

                            <div class="card-body">
                                <div class="d-flex align-items-start align-items-sm-center gap-6">
                                    <img src="{{ $user->image ? route('secure.image', ['path' => $user->image]) : asset('cp/assets/img/avatars/1.png') }}"
                                        alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                                    <div class="button-wrapper">
                                        <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                                            <span class="d-none d-sm-block">رفع صورة جديدة</span>
                                            <i class="ti ti-upload d-block d-sm-none"></i>
                                            <input type="file" id="upload" name="avatar" class="account-file-input"
                                                hidden accept="image/png, image/jpeg" />
                                        </label>
                                        <button type="button" class="btn btn-label-secondary account-image-reset mb-4">
                                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">إعادة تعيين</span>
                                        </button>
                                        <div>الملفات المسموحة: JPG, GIF, PNG. الحد الأقصى للحجم: 800 كيلوبايت</div>
                                    </div>
                                </div>
                            </div>

                            <!-- الاسم الكامل واسم المستخدم -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="full_name">الاسم الكامل</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        value="{{ old('full_name', $user->name) }}" required>
                                    <div class="invalid-feedback">يرجى إدخال الاسم الكامل</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="username">اسم المستخدم</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username', $user->username) }}" required>
                                    <div class="invalid-feedback">يرجى إدخال اسم المستخدم</div>
                                </div>
                            </div>

                            <!-- البريد الإلكتروني ورقم الجوال -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="email">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                    <div class="invalid-feedback">يرجى إدخال بريد إلكتروني صحيح</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">رقم الجوال</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $user->phone) }}" required>
                                    <div class="invalid-feedback">يرجى إدخال رقم الجوال</div>
                                </div>
                            </div>

                            <!-- العنوان -->
                            <div class="mb-3">
                                <label class="form-label" for="address">العنوان</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ old('address', $user->address) }}">
                            </div>

                            <!-- زر الإرسال -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">تحديث المستخدم</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
