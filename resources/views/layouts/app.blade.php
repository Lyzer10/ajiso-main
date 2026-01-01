<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ __('AJISO Legal Aid System') }}</title>
    <title>@yield('title')</title>
    <meta content="Promoting human rights, access to justice, and empowerment for women and vulnerable children." name="description">
    <meta content="ajiso, legal aid, human rights, justice, women, children" name="keywords">
    <meta content="Hamasa Media Group" name="author">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/icon/faviconx64.ico') }}">
    <link rel="apple-touch-icon" type="image/png" href="{{ asset('assets/images/icon/faviconx64.ico') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link rel="stylesheet"  href="{{ asset('landing/vendor/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/vendor/bootstrap/css/bootstrap.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/vendor/boxicons/css/boxicons.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('landing/vendor/glightbox/css/glightbox.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('landing/vendor/swiper/swiper-bundle.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}">

</head>

<body>
    <!-- ======= Organization info ======  -->
     <div id="org" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <ul>
                <li class="d-flex align-items-center justify-content-center gap-2">
                    <img src="/assets/images/icon/phone.png" alt="phone">
                    <p class="m-0 p-0">+255 000 000 000</p>
                </li>
                <li class="d-flex align-items-center justify-content-center gap-2">
                    <img src="/assets/images/icon/email.png" alt="phone">
                    <p class="m-0 p-0">info@ajiso.org</p>
                </li>
                <li class="d-flex align-items-center justify-content-center gap-2">
                    <img src="/assets/images/icon/location.png" alt="phone">
                    <p class="m-0 p-0">Tanzania</p>
                </li>
            </ul>
            
            <ul>
                <li class="d-flex align-items-center justify-content-center gap-2">
                    <a href="https://www.facebook.com/ajiso" target="_blank">
                        <img src="/assets/images/icon/facebook.png" alt="facebook">
                    </a>
                </li>
                <li class="d-flex align-items-center justify-content-center gap-2">
                    <a href="https://www.instagram.com/ajiso" target="_blank">
                        <img src="/assets/images/icon/instagram.png" alt="instagram">
                    </a>
                </li>
                <li class="d-flex align-items-center justify-content-center gap-2">
                   <a href="https://x.com/ajiso" target="_blank">
                     <img src="/assets/images/icon/x.png" alt="x">
                   </a>
                </li>
            </ul>
        </div>
    </div><!-- End Organization info -->

    <!-- ======= Header ======= -->
    <header id="header" class="d-flex align-items-center header-transparent">
        <div class="container d-flex align-items-center justify-content-between">

        <div class="logo">
            <a href="https://ajiso.org">
                <img src="/assets/images/logo-ajiso.svg" alt="AJISO logo"/>
            </a>
        </div>

        <nav id="navbar" class="navbar">
            <ul>
                @yield('nav-links')
                <li class="dropdown">
                    <a href="#">
                        <span>
                            @if (app()->isLocale('en'))
                                {{ __('English') }}
                            @else
                                {{ __('Swahili') }}
                            @endif
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route(Route::currentRouteName(), 'en') }}">{{ __('English') }}</a></li>
                        <li>
                            <a href="{{ route(Route::currentRouteName(), 'sw') }}">{{ __('Swahili') }}</a></li>
                    </ul>
                </li>
            </ul>
            <i class="fas fa-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

        </div>
    </header><!-- End Header -->

    <!-- ======= Hero Section ======= -->
    <section id="hero">

        <div class="">
            @yield('hero')
        </div>

        <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28 " preserveAspectRatio="none">
        <defs>
            <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
        </defs>
        <g class="wave1">
            <use xlink:href="#wave-path" x="50" y="3" fill="rgba(255,255,255, .1)">
        </g>
        <g class="wave2">
            <use xlink:href="#wave-path" x="50" y="0" fill="rgba(255,255,255, .2)">
        </g>
        <g class="wave3">
            <use xlink:href="#wave-path" x="50" y="9" fill="#fff">
        </g>
        </svg>

    </section><!-- End Hero -->

    <main id="main">
        @yield('content')
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
        <div class="copyright">
            &copy; {{ __('Copyright') }} © {{ Carbon\Carbon::today()->format('Y') }} 
            <strong>
                <span>
                    <a href="{{ env('ORGANIZATION_URL') }}" target="_blank">{{ env('ORGANIZATION') }}</a>
                </span>
            </strong>
            . {{ __('All Rights Reserved') }}
        </div>
        <div class="credits">
            {{ __('Designed by') }} <a href="{{ env('DEVELOPER_URL') }}" target="_blank">{{ ("Hamasa Media Group") }}</a>
            <a href="https://bootstrapmade.com/" target="_blank" style="display: none;">BootstrapMade</a>
        </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></a>
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('landing/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('landing/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('landing/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <!-- jQuery -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Template Main JS File -->
    <script src="{{ asset('landing/js/main.js') }}"></script>

</body>

</html>
