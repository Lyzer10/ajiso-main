@extends('layouts.base')

@php
    $title = __('Users') 
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Users') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Users List') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')

<div class="row">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-10">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Ooops!</strong> {{ __('Something went wrong!') }}<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Users roles dashboard -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-3 mt-md-5 mb-2">
                <div class="single-report">
                    <div class="s-report-inner pr--20 pt--30 mb-3">
                        <div class="icon"><i class="fas fa-fw fa-user-cog"></i></div>
                        <div class="s-report-title d-flex justify-content-between">
                            <h4 class="header-title mb-0">{{  __('Super Admins') }}</h4>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <h2>{{ !is_null($super_admin_count) ? $super_admin_count : 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-md-5 mb-2">
                <div class="single-report">
                    <div class="s-report-inner pr--20 pt--30 mb-3">
                        <div class="icon"><i class="fas fa-user-lock"></i></div>
                        <div class="s-report-title d-flex justify-content-between">
                            <h4 class="header-title mb-0">{{  __('Admins') }}</h4>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <h2>{{ !is_null($admin_count) ? $admin_count : 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-md-5 mb-2">
                <div class="single-report mb-xs-30">
                    <div class="s-report-inner pr--20 pt--30 mb-3">
                        <div class="icon"><i class="fas fa-user-friends"></i></div>
                        <div class="s-report-title d-flex justify-content-between">
                            <h4 class="header-title mb-0">{{  __('Paralegals') }}</h4>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <h2>{{ !is_null($paralegal_count) ? $paralegal_count : 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-md-5 mb-2">
                <div class="single-report mb-xs-30">
                    <div class="s-report-inner pr--20 pt--30 mb-3">
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <div class="s-report-title d-flex justify-content-between">
                            <h4 class="header-title mb-0">{{  __('Staff (Lawyers)') }}</h4>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <h2>{{ !is_null($lap_count) ? $lap_count : 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- user list area start -->
    <div class="col-md-12 mt-5 mb-3">
        <div class="card">
            <div class="card-header">
                <div class="header-title clearfix">
                    {{ _('Users list') }}
                    <a class="btn btn-sm text-white light-custom-color dropdown-toggle pull-right" href="#" id="bd-versions" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ _('Add User') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-md-right" aria-labelledby="bd-versions">
                        <a class="dropdown-item btn-link" href="{{ route('staff.create', app()->getLocale()) }}">
                            {{ _('Legal Aid Provider') }}
                        </a>
                        <a class="dropdown-item btn-link" href="{{ route('beneficiary.create', app()->getLocale()) }}">
                            {{ _('Beneficiary') }}
                        </a>
                        <a class="dropdown-item btn-link" href="{{ route('user.create', app()->getLocale()) }}">
                            {{ _('Admin') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="width: 100%;">
                <div class="table-responsive">
                    <table class="table progress-table text-center table-striped">
                        <thead class="text-capitalize text-white light-custom-color">
                            <tr>
                                <th>{{ __('Id') }}</th>
                                <th>{{ __('User No') }}</th>
                                <th>{{ __('Username') }}</th>
                                <th>{{ __('Full Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th colspan="2">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->user_no }}</td>
                                <td>{{ '@'.$user->name }}</td>
                                <td>
                                    {{ $user->first_name.' ' }}
                                    @if ($user->middle_name === '')
                                        {{ ' ' }}
                                    @else
                                        {{ Str::substr($user->middle_name, 0, 1).'.' }}
                                    @endif
                                    {{ ' '.$user->last_name }}
                                </td>
                                <td>{{ $user->email ?? 'N/A' }}</td>
                                <td>{{ Str::ucfirst($user->role->role_abbreviation)}}</td>
                                <td>
                                    @if ((bool) $user->is_active === true)
                                    <span class="p-1
                                        {{ 'badge badge-success' }}">
                                        {{ __("Active") }}
                                    </span>
                                    @else
                                    <span class="p-1
                                        {{ 'badge badge-secondary' }}">
                                        {{ __("Inactive") }}
                                    </span>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-between">
                                    <a href="{{ route('user.edit', [app()->getLocale(), $user->name]) }}" title="{{ __('Edit User') }}">
                                        <i class="fas fa-pencil-square-o fa-fw text-warning "></i>
                                    </a> /
                                    <a href="{{ route('user.show', [app()->getLocale(), $user->name]) }}" title="{{ __('User Profile') }}">
                                        <i class="fas fa-user-shield fa-fw text-secondary"></i>
                                    </a> /
                                    <form method="POST" action="{{ route('user.trash', [app()->getLocale(), $user->id]) }}">
                                        @csrf
                                        @METHOD('PUT')
                                            <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete User') }}"></i>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-1">{{ __('No users found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $users->count() ? $users->links() : ''}}
            </div>
        </div>
    </div>
    <!-- user list area end -->
</div>
@endsection

@push('scripts')
    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

@endpush
