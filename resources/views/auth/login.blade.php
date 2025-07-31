<!doctype html>
<html lang="ar" dir="rtl" class="light-style layout-wide customizer-hide" data-theme="theme-default"
    data-assets-path="/cp/assets/" data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>تسجيل الدخول | لوحة التحكم</title>
    <meta name="description" content="لوحة إدارة الموقع" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('cp/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('cp/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('cp/assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Login Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="#" class="app-brand-link">
                                <span class="app-brand-logo demo">
                                    <!-- شعار الموقع -->
                                    <img src="{{ $website->site_logo ? asset('storage/' . $website->site_logo) : asset('cp/assets/img/avatars/1.png') }}"
                                        alt="site-logo" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />

                                </span>
                                <span class="app-brand-text demo text-heading fw-bold">{{ $website->site_name }}</span>
                            </a>
                        </div>

                        <!-- Title -->
                        <h4 class="mb-1">مرحباً بعودتك 👋</h4>
                        <p class="mb-4">الرجاء تسجيل الدخول للمتابعة</p>

                        <!-- Error messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form id="formAuthentication" action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="أدخل بريدك الإلكتروني" value="{{ old('email') }}" required
                                    autofocus />
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4 form-password-toggle">
                                <label class="form-label" for="password">كلمة المرور</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="••••••••" required />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4 d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember-me" />
                                    <label class="form-check-label" for="remember-me"> تذكرني </label>
                                </div>
                                <a href="#">
                                    <small>نسيت كلمة المرور؟</small>
                                </a>
                            </div>

                            <div class="mb-4">
                                <button class="btn btn-primary d-grid w-100" type="submit">تسجيل الدخول</button>
                            </div>
                        </form>

                        {{-- <p class="text-center">
                            <span>مستخدم جديد؟</span>
                            <a href="#">
                                <span>إنشاء حساب</span>
                            </a>
                        </p>

                        <div class="divider my-4">
                            <div class="divider-text">أو</div>
                        </div>

                        <!-- Social login buttons (optional) -->
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-icon btn-facebook"><i
                                    class="ti ti-brand-facebook-filled"></i></a>
                            <a href="#" class="btn btn-icon btn-twitter"><i
                                    class="ti ti-brand-twitter-filled"></i></a>
                            <a href="#" class="btn btn-icon btn-google-plus"><i
                                    class="ti ti-brand-google-filled"></i></a>
                        </div> --}}
                    </div>
                </div>
                <!-- /Login Card -->
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('cp/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('cp/assets/js/main.js') }}"></script>
</body>

</html>
