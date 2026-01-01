{{-- “app” layout that extends this, sets up the app section, and adds the base styles and scripts. This is the layout most views will use --}}
@extends('layouts.master')

@prepend('styles')
        <!-- general styles -->
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/flag-icon.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/metisMenu.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/slicknav.min.css') }}">

        <!-- others css -->
        <link rel="stylesheet" href="{{ asset('assets/css/typography.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/default-css.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

        <!-- modernizr css -->
        <script src="{{ asset('assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

@endprepend

@section('base')
    <div>
        @yield('content')
    </div>
@endsection

@prepend('modals')
    @include('modals.logout')
@endprepend

@prepend('scripts')
    <!-- jquery version 3 -->
    <script src="{{ asset('assets/js/vendor/jquery-3.6.0.min.js') }}"></script>

    <!-- bootstrap 4 js 2-->
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slicknav.min.js') }}"></script>

    <!-- others plugins -->
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
@endprepend
