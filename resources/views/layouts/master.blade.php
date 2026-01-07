<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>
        <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/icon/faviconx64.ico') }}">

        @stack('styles')
    </head>
    <body>
        <!--[if lt IE 8]>
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->
        <!-- preloader area start -->
        <div id="preloader">
            <div class="loader"></div>
        </div>
        <!-- preloader area end -->
        <!-- page container area start -->
        <div class="page-container">
            <!-- sidebar menu area start -->
            <div class="sidebar-menu">
                <div class="sidebar-header">
                    <div class="logo">
                        @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                            <h1>
                                <a href="{{ route('admin.home', app()->getLocale()) }}">
                                   AJISO
                                </a>
                            </h1>
                            <p>{{ __('Legal Aid Digital System') }}</p>
                        @elsecanany(['isStaff'])
                            <h1>
                                <a href="{{ route('staff.home', app()->getLocale()) }}">
                                   AJISO
                                </a>
                            </h1>
                            <p>{{ __('Legal Aid Digital System') }}</p>
                        @endcanany
                    </div>
                </div>
                <div class="main-menu">
                    <div class="menu-inner">
                        <nav>
                            <ul class="metismenu" id="menu">
    {{-- Dashboard --}}
  <li class="{{ request()->routeIs('admin.super.home') || request()->routeIs('admin.home') || request()->routeIs('staff.home') || request()->routeIs('clerk.home') || request()->routeIs('beneficiary.home') ? 'active' : '' }}">
    @canany(['isSuperAdmin'])
        <a href="{{ route('admin.super.home', app()->getLocale()) }}" aria-expanded="true">
    @elsecanany(['isAdmin','isClerk'])
        <a href="{{ route('admin.home', app()->getLocale()) }}" aria-expanded="true">
    @elsecanany(['isStaff'])
        <a href="{{ route('staff.home', app()->getLocale()) }}" aria-expanded="true">
    @endcanany
        <i class="fas fa-shield-alt"></i>
        <span>{{ __('Dashboard') }}</span>
    </a>
</li>


    <li class="padding-bottom"></li>

    {{-- Users --}}
    @canany(['isSuperAdmin','isAdmin','isClerk'])
        <li class="{{ request()->routeIs('staff.*') || request()->routeIs('beneficiaries.*') || request()->routeIs('beneficiary.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-user-friends"></i>
                <span>{{ __('Users') }}</span>
            </a>
            <ul class="collapse">
                @cannot('isClerk')
                <li class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
                    <a href="{{ route('staff.list', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Legal Aid Providers') }}
                    </a>
                </li>
                @endcannot
                <li class="{{ request()->routeIs('beneficiaries.*') || request()->routeIs('beneficiary.*') ? 'active' : '' }}">
                    <a href="{{ route('beneficiaries.list', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Beneficiaries') }}
                    </a>
                </li>
            </ul>
        </li>
    @endcanany
    @canany(['isStaff'])
        <li class="{{ request()->routeIs('beneficiaries.*') || request()->routeIs('beneficiary.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-user-friends"></i>
                <span>{{ __('Users') }}</span>
            </a>
            <ul class="collapse">
                <li class="{{ request()->routeIs('beneficiaries.*') || request()->routeIs('beneficiary.*') ? 'active' : '' }}">
                    <a href="{{ route('beneficiaries.list', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Beneficiaries') }}
                    </a>
                </li>
            </ul>
        </li>
    @endcanany

    {{-- Cases --}}
    @canany(['isSuperAdmin','isAdmin','isClerk'])
        @cannot('isClerk')
            <li class="{{ request()->routeIs('dispute.*') || request()->routeIs('disputes.*') ? 'active' : '' }}">
                <a href="javascript:void(0)" aria-expanded="true">
                    <i class="fas fa-balance-scale"></i>
                    <span>{{ __('Cases') }}</span>
                </a>
                <ul class="collapse">
                    <li class="{{ request()->routeIs('dispute.create.new') ? 'active' : '' }}">
                        <a href="{{ route('dispute.create.new', app()->getLocale()) }}">
                            <i class="fas fa-caret-right"></i>
                            {{ __('New Dispute') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('dispute.select.archive') ? 'active' : '' }}">
                        <a href="{{ route('dispute.select.archive', app()->getLocale()) }}">
                            <i class="fas fa-caret-right"></i>
                            {{ __('Archived Dispute') }}
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('disputes.list') ? 'active' : '' }}">
                        <a href="{{ route('disputes.list', app()->getLocale()) }}">
                            <i class="fas fa-chevron-right"></i>
                            {{ __('Disputes List') }}
                        </a>
                    </li>
                </ul>
            </li>
        @endcannot
    @endcanany
    @canany(['isStaff'])
        <li class="{{ request()->routeIs('dispute.create.new') ? 'active' : '' }}">
            <a href="{{ route('dispute.create.new', app()->getLocale()) }}">
                <i class="fas fa-balance-scale"></i>
                <span>{{ __('New Dispute') }}</span>
            </a>
        </li>
    @endcanany

    {{-- My Cases (for staff only) --}}
    @unless(auth()->user()->can('isSuperAdmin') || auth()->user()->can('isClerk'))
        <li class="{{ request()->routeIs('disputes.my.list') ? 'active' : '' }}">
            <a href="{{ route('disputes.my.list', [app()->getLocale(), auth()->user()->staff->id]) }}">
                <i class="fas fa-user-shield"></i>
                <span>{{ __('My Cases') }}</span>
            </a>
        </li>
    @endunless

    {{-- Disputes Assignment --}}
    @cannot('isClerk')
        <li class="{{ request()->routeIs('dispute.assign') || request()->routeIs('dispute.request.*') || request()->routeIs('disputes.request.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-sync"></i>
                <span>{{ __('Disputes Assignment') }}</span>
            </a>
            <ul class="collapse">
                @canany(['isSuperAdmin','isAdmin'])
                <li class="{{ request()->routeIs('dispute.assign') ? 'active' : '' }}">
                    <a href="{{ route('dispute.assign', [app()->getLocale(), 'all']) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Assign Legal Aid Provider') }}
                    </a>
                </li>
                @endcanany
                @canany(['isStaff'])
                <li class="{{ request()->routeIs('dispute.request.create') ? 'active' : '' }}">
                    <a href="{{ route('dispute.request.create', [app()->getLocale(), 'all']) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Send Request') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('disputes.request.my-list') ? 'active' : '' }}">
                    <a href="{{ route('disputes.request.my-list', [app()->getLocale(), auth()->user()->staff->id]) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('My Requests') }}
                    </a>
                </li>
                @elsecanany(['isSuperAdmin', 'isAdmin'])
                <li class="{{ request()->routeIs('disputes.request.list') ? 'active' : '' }}">
                    <a href="{{ route('disputes.request.list', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Requests List') }}
                    </a>
                </li>
                @endcanany
            </ul>
        </li>
    @endcannot

    {{-- Reports --}}
    @canany(['isSuperAdmin', 'isAdmin'])
        <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-receipt"></i>
                <span>{{ __('Reports') }}</span>
            </a>
            <ul class="collapse">
                <li class="{{ request()->routeIs('reports.general') ? 'active' : '' }}">
                    <a href="{{ route('reports.general', app()->getLocale())}}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('General') }}
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Summaries') }}
                    </a>
                    <ul class="collapse">
                        <li class="{{ request()->routeIs('reports.summary.dispute') ? 'active' : '' }}">
                            <a href="{{ route('reports.summary.dispute', app()->getLocale()) }}">
                                <i class="fas fa-caret-right"></i>
                                {{ __('Case Summary') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('reports.summary.enrollment') ? 'active' : '' }}">
                            <a href="{{ route('reports.summary.enrollment', app()->getLocale()) }}">
                                <i class="fas fa-caret-right"></i>
                                {{ __('Client Enrollment') }}
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('reports.summary.survey') ? 'active' : '' }}">
                            <a href="{{ route('reports.summary.survey', app()->getLocale()) }}">
                                <i class="fas fa-caret-right"></i>
                                {{ __('Survey Summary') }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    @elsecanany('isStaff')
        <li class="{{ request()->routeIs('reports.general.staff') || request()->routeIs('reports.summary.dispute.staff') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-receipt"></i>
                <span>{{ __('Reports') }}</span>
            </a>
            <ul class="collapse">
                <li class="{{ request()->routeIs('reports.general.staff') ? 'active' : '' }}">
                    <a href="{{ route('reports.general.staff', app()->getLocale())}}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('General') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('reports.summary.dispute.staff') ? 'active' : '' }}">
                    <a href="{{ route('reports.summary.dispute.staff', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Case Summary') }}
                    </a>
                </li>
            </ul>
        </li>
    @endcanany

    {{-- Notifications --}}
    <li class="{{ request()->routeIs('notifications.*') || request()->routeIs('notification.*') ? 'active' : '' }}">
        <a href="javascript:void(0)" aria-expanded="true">
            <i class="fas fa-bell"></i>
            <span>{{ __('Notifications') }}</span>
        </a>
        <ul class="collapse">
            <li class="{{ request()->routeIs('notifications.list') ? 'active' : '' }}">
                <a href="{{ route('notifications.list', app()->getLocale()) }}">
                    <i class="fas fa-chevron-right"></i>
                    {{ __('My Notifications') }}
                </a>
            </li>
            <li class="{{ request()->routeIs('notification.create') ? 'active' : '' }}">
                <a href="{{ route('notification.create', app()->getLocale()) }}">
                    <i class="fas fa-chevron-right"></i>
                    {{ __('Publish Notification') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- System & Settings (SuperAdmin only) --}}
    @canany(['isSuperAdmin'])
        <li class="{{ request()->routeIs('system.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-laptop-house"></i>
                <span>{{ __('System') }}</span>
            </a>
            <ul class="collapse">
                <li class="{{ request()->routeIs('system.logs') ? 'active' : '' }}">
                    <a href="{{ route('system.logs', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('System Logs') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('system.trash') ? 'active' : '' }}">
                    <a href="{{ route('system.trash', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Trash') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('system.backup') ? 'active' : '' }}">
                    <a href="{{ route('system.backup', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Backup') }}
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" aria-expanded="true">
                <i class="fas fa-cogs"></i>
                <span>{{ __('Settings') }}</span>
            </a>
            <ul class="collapse">
                <li class="{{ request()->routeIs('settings.manager') ? 'active' : '' }}">
                    <a href="{{ route('settings.manager', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Entities Manager') }}
                    </a>
                </li>
                <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.list', app()->getLocale()) }}">
                        <i class="fas fa-chevron-right"></i>
                        {{ __('Users Manager') }}
                    </a>
                </li>
            </ul>
        </li>
    @endcanany

    {{-- Profile --}}
    <li class="{{ request()->routeIs('user.show') ? 'active' : '' }}">
        <a href="{{ route('user.show', [app()->getLocale(), auth()->user()->name]) }}">
            <i class="fas fa-user-circle"></i>
            <span>{{ __('Profile') }}</span>
        </a>
    </li>

    {{-- Logout --}}
    <li>
        <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#modalLogout">
            <i class="fas fa-power-off text-danger"></i>
            <span>{{ __('Logout') }}</span>
        </a>
    </li>
</ul>

                        </nav>
                    </div>
                </div>
            </div>
            <!-- sidebar menu area end -->
            <!-- main content area start -->
            <div class="main-content">
                <!-- header area start -->
                <div class="header-area">
                    <div class="row align-items-center">
                        <!-- nav -->
                        <div class="col-md-6 col-sm-8 clearfix">
                            <div class="nav-btn pull-left">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="pull-left font-weight-bold">
                                <ul>
                                <!-- Language Dropdown Menu -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link text-white" data-toggle="dropdown" href="#">
                                        @if (app()->isLocale('en'))
                                        <i class="flag-icon flag-icon-us"></i> {{ __('English') }}
                                        @else
                                        <i class="flag-icon flag-icon-tz mr-2"></i> {{ __('Swahili') }}
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-md-right p-0">
                                        @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                                            <a href="{{ route('admin.home', 'en') }}" class="dropdown-item active">
                                                <i class="flag-icon flag-icon-us mr-2"></i>{{ __('English') }}
                                            </a>
                                            <a href="{{ route('admin.home', 'sw') }}" class="dropdown-item">
                                                <i class="flag-icon flag-icon-tz mr-2"></i>{{ __('Swahili') }}
                                            </a>
                                        @elsecanany(['isStaff'])
                                            <a href="{{ route('staff.home', 'en') }}" class="dropdown-item active">
                                                <i class="flag-icon flag-icon-us mr-2"></i>{{ __('English') }}
                                            </a>
                                            <a href="{{ route('staff.home', 'sw') }}" class="dropdown-item">
                                                <i class="flag-icon flag-icon-tz mr-2"></i>{{ __('Swahili') }}
                                            </a>
                                        @endcanany
                                    </div>
                                </li>
                                </ul>
                            </div>
                        </div>
                        <!-- profile info & task notification -->
                        <div class="col-md-6 col-sm-4 clearfix">
                            <ul class="notification-area pull-right">
                                <li id="full-view"><i class="fas fa-expand-arrows-alt"></i></li>
                                <li id="full-view-exit"><i class="fas fa-compress-alt"></i></li>
                                <li class="dropdown">
                                    <i class="fas fa-bell dropdown-toggle" data-toggle="dropdown">
                                        @php
                                            $notifications = auth()->user()->unreadNotifications;
                                        @endphp
                                        <span>{{ $notifications->count() ?? 0 }}</span>
                                    </i>
                                    <div class="dropdown-menu bell-notify-box notify-box">
                                        <span class="notify-title d-block">
                                            @if (app()->isLocale('en'))
                                                {{ __('You have') }} {{ $notifications->count() }} {{ __('new notifications') }}
                                            @else
                                                {{ __('You have') }} {{ __('new notifications') }} {{ $notifications->count() }}
                                            @endif
                                            <a class="btn btn-sm btn-outline-white float-right" title="{{ __('view all') }}" href="{{ route('notifications.list', app()->getLocale()) }}">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </span>
                                        <div class="nofity-list">
                                            @forelse($notifications->take(5) as $notification)
                                                <a href="#" class="notify-item">
                                                    <div class="notify-thumb"><i class="fas fa-comments btn-info"></i></div>
                                                    <div class="notify-text">
                                                        <p>{{ $notification->data['message'] }}</p>
                                                        <span>
                                                            {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() ?? '0';}}
                                                        </span>
                                                    </div>
                                                </a>
                                            @empty
                                                <div class="nofity-list">
                                                    <a href="#" class="notify-item">
                                                        <div class="notify-thumb"><i class="fas fa-exclamation-triangle btn-danger"></i></div>
                                                        <div class="notify-text">
                                                            <p>{{ __('There are no new notifications') }}</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="">
                                        <div class="user-profile pull-right">
                                            <img class="avatar user-thumb"
                                                src="@if(File::exists('storage/uploads/images/profiles/'.auth()->user()->image)){{ asset('storage/uploads/images/profiles/thumbnails/'.auth()->user()->image) }}@else {{ asset('assets/images/avatar/avatar.png') }} @endif"
                                                alt="user image">
                                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
                                                {{ auth()->user()->name }}
                                                <i class="fa fa-angle-down"></i>
                                            </h4>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('user.show', [app()->getLocale(), auth()->user()->name]) }}">
                                                    <i class="fas fa-user-circle"></i>
                                                    {{ __('Profile') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('preferences.list', app()->getLocale()) }}">
                                                    <i class="fas fa-cogs"></i>
                                                    {{ __('Settings') }}</a>
                                                <a  href="javascript:void(0)" class="dropdown-item"
                                                    data-toggle="modal" data-target="#modalLogout">
                                                    <i class="fas fa-power-off text-danger"></i>
                                                    <span>{{ __('Log Out') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- header area end -->
                <!-- page title area start -->
                <div class="page-title-area">
                    <div class="row align-items-center">
                        <div class="col-sm-9">
                            @yield('breadcrumb')
                        </div>
                        {{-- <div class="col-sm-3 clearfix">
                            <div class="user-profile pull-right">
                                <img class="avatar user-thumb"
                                    src="@if(File::exists('storage/uploads/images/profiles/'.auth()->user()->image)){{ asset('storage/uploads/images/profiles/thumbnails/'.auth()->user()->image) }}@else {{ asset('assets/images/avatar/avatar.png') }} @endif"
                                    alt="user image">
                                <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
                                    {{ auth()->user()->name }}
                                    <i class="fa fa-angle-down"></i>
                                </h4>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('user.show', [app()->getLocale(), auth()->user()->name]) }}">
                                        <i class="fas fa-user-circle"></i>
                                        {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('preferences.list', app()->getLocale()) }}">
                                        <i class="fas fa-cogs"></i>
                                        {{ __('Settings') }}</a>
                                    <a  href="javascript:void(0)" class="dropdown-item"
                                        data-toggle="modal" data-target="#modalLogout">
                                        <i class="fas fa-power-off text-danger"></i>
                                        <span>{{ __('Log Out') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <!-- page title area end -->
                <!-- main-content-inner start -->
                <div class="main-content-inner">
                    @yield('base')
                </div>
                <!-- main-content-inner end -->
            </div>
            <!-- main content area end -->
            <!-- footer area start-->
            <footer>
                <div class="footer-area">
                    <p>
                        {{ __('Copyright') }} © {{ Carbon\Carbon::today()->format('Y') }} <code class="font-weight-bold">{{ env('ORGANIZATION') }}.</code>
                        {{ __('All Rights Reserved') }}.  {{ __('Developed by Hamasa Media Group') }} 
                        {{-- <a href="{{ ('Hamasa Media Group') }}" target="_blank">{{ env('DEVELOPER') }}</a>
                        . --}}
                    </p>
                </div>
            </footer>
            <!-- footer area end-->
        </div>
        <!-- page container area end -->
        <!-- offset area start -->
        @stack('modals')
        @stack('scripts')
    </body>
</html>
