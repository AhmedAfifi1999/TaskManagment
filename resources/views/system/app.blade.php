<!--
=========================================================
* Argon Dashboard 2 - v2.0.4
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <title>Laravel 11 Task Management System - Project</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css?v=2.0.4') }}" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @yield('extraStyle')

    @livewireStyles

</head>
<style>
    /* Custom Navbar Styles */
    .navbar {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    .nav-link {
        color: #525f7f;
        font-weight: 600;
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background-color: rgba(94, 114, 228, 0.1);
        color: #5e72e4;
        transform: translateY(-2px);
    }

    .nav-link.active {
        background-color: rgba(94, 114, 228, 0.1);
        color: #5e72e4 !important;
    }

    .dropdown-item {
        font-weight: 600;
        padding: 0.5rem 1.5rem;
    }

    .dropdown-item:hover {
        background-color: #f6f9fc;
        color: #5e72e4;
    }
</style>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>

    <main class="main-content position-relative border-radius-lg ">
        <div class="container-fluid py-4">
            <!-- Navbar -->
            <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl card h-100"
                id="navbarBlur" navbar-scroll="true" dir="ltr">
                <div class="container-fluid py-1 px-3">
                    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                        <ul class="navbar-nav me-auto"> <!-- Changed to me-auto for left alignment -->
                            <li class="nav-item">
                                <a class="nav-link active fw-bold" href="#" style="color: #5e72e4;">
                                    <i class="fas fa-home me-2"></i>
                                    <span class="d-sm-inline d-none">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="#">
                                    <i class="fas fa-tasks me-2"></i>
                                    <span class="d-sm-inline d-none">Projects</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="#">
                                    <i class="fas fa-users me-2"></i>
                                    <span class="d-sm-inline d-none">Users</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto"> <!-- Right-aligned items -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold" href="#" id="navbarDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <span class="d-sm-inline d-none">Account</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item fw-bold" href="#">Profile</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="#">
                                            @csrf
                                            <button type="submit" class="dropdown-item fw-bold">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->


            @yield('content')

            {{ view('system.footer') }}
        </div>
    </main>

    <!--   Core JS Files   -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/argon-dashboard.min.js?v=2.0.4') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    @yield('extraScript')

    @livewireScripts

</body>

</html>
