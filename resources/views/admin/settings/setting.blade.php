@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-md-12">
                <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0);">
                                <i class="ti-sm ti ti-users me-1_5"></i> الإعدادات العامة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="ti-sm ti ti-lock me-1_5"></i> الحماية
                            </a>
                        </li>

                    </ul>
                </div>

                <div class="card mb-6">
                    <div class="card-body pt-4">
                        <form id="formAccountSettings" method="POST" action="{{ route('admin.settings.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="d-flex align-items-start align-items-sm-center gap-6">
                                    <img src="{{ $settings->site_logo ? asset('storage/' . $settings->site_logo) : asset('cp/assets/img/avatars/1.png') }}"
                                        alt="site-logo" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                                    <div class="button-wrapper">
                                        <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                                            <span class="d-none d-sm-block">رفع شعار الموقع</span>
                                            <i class="ti ti-upload d-block d-sm-none"></i>
                                            <input type="file" id="upload" name="site_logo" class="account-file-input"
                                                hidden accept="image/png, image/jpeg" />
                                        </label>
                                        <button type="button" class="btn btn-label-secondary account-image-reset mb-4">
                                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">اعادة تعيين</span>
                                        </button>
                                        <div>مسموح فقط بصيغ JPG أو GIF أو PNG. الحد الأقصى للحجم هو 800 كيلوبايت.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label for="siteName" class="form-label">اسم الموقع</label>
                                    <input class="form-control" type="text" id="siteName" name="site_name"
                                        value="{{ $settings->site_name ?? 'موقع ويب خاص بي' }}" autofocus />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="siteDescription" class="form-label">وصف الموقع</label>
                                    <textarea class="form-control" name="site_description" id="siteDescription" rows="4">{{ $settings->site_description ?? 'وصف الموقع' }}</textarea>
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="siteEmail" class="form-label">البريد الإلكتروني للموقع</label>
                                    <input class="form-control" type="email" id="siteEmail" name="site_email"
                                        value="{{ $settings->site_email ?? 'info@example.com' }}"
                                        placeholder="info@example.com" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="sitePhone" class="form-label">رقم الهاتف للموقع</label>
                                    <input class="form-control" type="text" id="sitePhone" name="site_phone"
                                        value="{{ $settings->site_phone }}" placeholder="رقم الهاتف" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="siteAddress" class="form-label">عنوان الموقع</label>
                                    <input class="form-control" type="text" id="siteAddress" name="site_address"
                                        value="{{ $settings->site_address }}"
                                        placeholder="غزة الرمال الباب الشمالي للسرايا" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="facebookLink" class="form-label">رابط الفيسبوك</label>
                                    <input class="form-control" type="text" id="facebookLink" name="facebook_link"
                                        value="{{ $settings->facebook_link ?? 'https://facebook.com/yourpage' }}"
                                        placeholder="https://facebook.com/yourpage" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="twitterLink" class="form-label">رابط تويتر</label>
                                    <input class="form-control" type="text" id="twitterLink" name="twitter_link"
                                        value="{{ $settings->twitter_link ?? 'https://twitter.com/yourprofile' }}"
                                        placeholder="https://twitter.com/yourprofile" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="instagramLink" class="form-label">رابط إنستغرام</label>
                                    <input class="form-control" type="text" id="instagramLink" name="instagram_link"
                                        value="{{ $settings->instagram_link ?? 'https://instagram.com/yourprofile' }}"
                                        placeholder="https://instagram.com/yourprofile" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="linkedinLink" class="form-label">رابط لينكدإن</label>
                                    <input class="form-control" type="text" id="linkedinLink" name="linkedin_link"
                                        value="{{ $settings->linkedin_link ?? 'https://linkedin.com/yourprofile' }}"
                                        placeholder="https://linkedin.com/yourprofile" />
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="youtubeLink" class="form-label">رابط يوتيوب</label>
                                    <input class="form-control" type="text" id="youtubeLink" name="youtube_link"
                                        value="{{ $settings->youtube_link ?? 'https://youtube.com/yourchannel' }}"
                                        placeholder="https://youtube.com/yourchannel" />
                                </div>

                                {{-- حالة الموقع (صيانة/نشط) --}}
                                <div class="mb-4 col-md-6">
                                    <label for="is_maintenance_mode" class="form-label">حالة الموقع</label>
                                    <div class="form-check form-switch">
                                        <!-- Hidden input لضمان إرسال 0 إذا كان غير محدد -->
                                        <input type="hidden" name="is_maintenance_mode" value="0">

                                        <!-- Checkbox يرسل 1 إذا تم تفعيله -->
                                        <input class="form-check-input" type="checkbox" id="is_maintenance_mode"
                                            name="is_maintenance_mode" value="1"
                                            {{ old('is_maintenance_mode', $settings->is_maintenance_mode ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_maintenance_mode">وضع الصيانة</label>
                                    </div>
                                    <small class="form-text text-muted">تفعيل هذا الخيار سيضع الموقع في وضع
                                        الصيانة.</small>
                                </div>


                                {{-- رسالة الصيانة --}}
                                <div class="mb-4 col-md-12">
                                    <label for="maintenance_message" class="form-label">رسالة الصيانة</label>
                                    <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3">
        {{ old('maintenance_message', $settings->maintenance_message ?? 'الموقع قيد الصيانة حالياً، نرجو العودة لاحقاً') }}
    </textarea> <small class="form-text text-muted">هذه الرسالة ستظهر
                                        للمستخدمين عندما يكون الموقع في
                                        وضع الصيانة.</small>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-3">حفظ التغييرات</button>
                                <button type="reset" class="btn btn-label-secondary">إلغاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
