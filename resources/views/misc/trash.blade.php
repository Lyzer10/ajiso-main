@extends('layouts.base')

@php
    $title = __('System') 
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('System') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Trash') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
            <div class="row">
                <div class="col-md-3 mt-md-5 mb-2">
                    <div class="single-report mb-xs-30 light-custom-color">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon bg-light">
                                <i class="fas fa-fw fa-user-cog dark-text"></i>
                            </div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title text-white mb-0">{{  __('Users') }}</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2 class="text-white">{{ !is_null($users_count) ? $users_count : 0 }}</h2>
                                <a href="{{ route('system.trash.users', app()->getLocale()) }}">
                                    <i class="fas fa-fw fa-2x fa-arrow-circle-right text-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-md-5 mb-2">
                    <div class="single-report mb-xs-30 light-custom-color">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon bg-light"><i class="fas  fa-user-friends dark-text"></i></div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title text-white mb-0">{{  __('LAPs') }}</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2 class="text-white">{{ !is_null($staff_count) ? $staff_count : 0 }}</h2>
                                <a href="{{ route('system.trash.staff', app()->getLocale()) }}">
                                    <i class="fas fa-fw fa-2x fa-arrow-circle-right text-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-md-5 mb-2">
                    <div class="single-report mb-xs-30 light-custom-color">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon bg-light">
                                <i class="fas fa-users dark-text"></i>
                            </div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title text-white mb-0">{{  __('Beneficiaries') }}</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2 class="text-white">{{ !is_null($beneficiaries_count) ? $beneficiaries_count : 0 }}</h2>
                                <a href="{{ route('system.trash.beneficiaries', app()->getLocale()) }}">
                                    <i class="fas fa-fw fa-2x fa-arrow-circle-right text-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-md-5 mb-2">
                    <div class="single-report mb-xs-30 light-custom-color">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon bg-light">
                                <i class="fas fa-balance-scale dark-text"></i>
                            </div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title text-white mb-0">{{  __('Disputes') }}</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2 class="text-white">{{ !is_null($disputes_count) ? $disputes_count : 0 }} </h2>
                                <a href="{{ route('system.trash.disputes', app()->getLocale()) }}">
                                    <i class="fas fa-fw fa-2x fa-arrow-circle-right text-white"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
