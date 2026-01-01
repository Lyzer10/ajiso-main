@extends('layouts.base')

@php
    $title = __('System') 
@endphp
@section('title', 'LAIS | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('System') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('System Backup') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="container mt-4">
        @include('includes.errors-statuses')
        <div class="row">
            <!-- View backup area start -->
            <div class="col-12">
                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="header-title">{{  __('System Backup') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <div class="col-md-6">
                                    <div class="grid-col">
                                        <form action="{{ route('system.backup.now', app()->getLocale()) }}" method="post" autocomplete="off">
                                            @csrf
                                            <input type="hidden" name="backup_type" value="db_only">
                                            <a type="submit" role="button" class="nav-link">
                                                [<i class="fas fa-database fa-fw text-success"></i>]
                                                <button class="btn btn-light">{{ __('Backup database only') }}</button>
                                            </a>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="grid-col">
                                        <form action="{{ route('system.backup.now', app()->getLocale()) }}" method="post" autocomplete="off">
                                            @csrf
                                            <input type="hidden" name="backup_type" value="all">
                                            <a type="submit" class="nav-link" >
                                                [<i class="fas fa-archive fa-fw text-brown"></i> + <i class="fas fa-database fa-fw text-success"></i>]
                                                <button class="btn btn-light">{{ __('Backup files and database') }}</button>
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- View backup  area end -->
        </div>
    </div>
</div>
@endsection
