<!doctype html>

<html lang="{{ app()->getLocale() }}" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    data-theme="theme-default" data-assets-path="/cp/assets/"

 data-template="vertical-menu-template"
    data-style="light">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>PLUTO IT</title>

    <meta name="description" content="" />
    @vite('resources/css/app.css')

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('cp/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    {{-- <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/fonts/flag-icons.css') }}" />
 --}}
    <!-- Core CSS -->

    <!-- Core CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('cp/assets/vendor/css/rtl/core.css') }}" --}}
        {{-- class="template-customizer-core-css" /> --}}
    {{-- <link rel="stylesheet" href="{{ asset('cp/assets/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" /> --}}

    <!-- Demo CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('cp/assets/css/demo.css') }}" /> --}}

    <!-- Vendors CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/apex-charts/apex-charts.css') }}" /> --}}



    {{-- <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />

    <link rel="stylesheet" href="{{ asset('cp/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
 --}}




    <!-- Page CSS -->


<!-- Helpers -->
<script src="{{ asset('cp/assets/vendor/js/helpers.js') }}"></script>

<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

<!--? Template customizer: To hide customizer set displayCustomizer value false in config.js. -->
<script src="{{ asset('cp/assets/vendor/js/template-customizer.js') }}"></script>

<!--? Config: Mandatory theme config file containing global vars & default theme options. -->
<script src="{{ asset('cp/assets/js/config.js') }}"></script>
</head>

<body>
 @yield('session_notice')

    @if(session()->has('success'))
    @foreach((array) session()->get('success') as $msg)
        <div style="position: fixed; top: {{ 20 + ($loop->index * 60) }}px; left: 20px; z-index: 9999; min-width: 300px;" class="alert alert-success alert-dismissible fade show shadow">
            <i class=""></i>
            {{ $msg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif
@if($errors->any())
    @foreach($errors->all() as $error)
        <div style="position: fixed; top: {{ 100 + ($loop->index * 60) }}px; left: 20px; z-index: 9999; min-width: 300px;" class="alert alert-danger alert-dismissible fade show shadow">
            <i class=""></i>
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <x-admin.aside />
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <x-admin.nav />
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
            @yield('content')
                
                    <!-- / Content -->

                    <!-- Footer -->
                    <x-admin.footer />

                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('cp/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('cp/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/js/menu.js') }}"></script>
        <!-- endbuild -->

        <script src="{{ asset('cp/js/pages-account-settings-account.js') }}"></script>
        {{-- <script src="{{ asset('cp/assets/js/pages-account-settings-account.js') }}"></script> --}}


        <script src="{{ asset('cp/assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/tagify/tagify.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/@form-validation/popular.js') }}"></script>
        <script src="{{ asset('cp/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
        <script src{{ asset('cp/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
        <script src="{{ asset('cp/assets/js/forms-selects.js') }}"></script>

        <script src="{{ asset('cp/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
        <script src="{{ asset('cp/assets/js/extended-ui-sweetalert2.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('cp/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('cp/assets/vendor/libs/sortablejs/sortable.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('cp/assets/js/main.js') }}"></script>


    <!-- Page JS -->
    <script src="{{ asset('cp/assets/js/dashboards-crm.js') }}"></script>
    <script src="{{ asset('cp/assets/js/forms-extras.js') }}"></script>
    <script src="{{ asset('cp/assets/js/extended-ui-drag-and-drop.js') }}"></script>


</body>

</html>
