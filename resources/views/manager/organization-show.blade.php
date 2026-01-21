@extends('layouts.base')

@php
    $title = __('Settings')
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Settings') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Organization Details') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <div class="row">
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title clearfix">
                            {{ __('Organization Details') }}
                            <a href="{{ route('manager.organizations.list', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right">
                                {{ __('Back') }}
                            </a>
                            <a href="{{ route('manager.organizations.edit', [app()->getLocale(), $organization]) }}" class="btn btn-sm text-white light-custom-color pull-right mr-2">
                                {{ __('Edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">{{ __('Organization') }}</div>
                            <div class="col-md-9">{{ $organization->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">{{ __('Region') }}</div>
                            <div class="col-md-9">{{ optional($organization->region)->region ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">{{ __('District') }}</div>
                            <div class="col-md-9">{{ optional($organization->district)->district ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 font-weight-bold">{{ __('Ward') }}</div>
                            <div class="col-md-9">{{ $organization->ward ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title clearfix">
                            {{ __('Organization Members') }}
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <div class="table-responsive">
                            <table class="table progress-table text-center table-striped">
                                <thead class="text-capitalize text-white light-custom-color">
                                    <tr>
                                        <th>{{ __('S/N') }}</th>
                                        <th>{{ __('Full Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Telephone No') }}</th>
                                        <th>{{ __('Role') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $users->firstItem() + $loop->index }}</td>
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
                                            <td>{{ $user->tel_no ?? 'N/A' }}</td>
                                            <td>{{ Str::ucfirst(optional($user->role)->role_abbreviation) }}</td>
                                            <td>
                                                @if ((bool) $user->is_active === true)
                                                    <span class="p-1 badge badge-success">{{ __("Active") }}</span>
                                                @else
                                                    <span class="p-1 badge badge-secondary">{{ __("Inactive") }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('user.show', [app()->getLocale(), $user->name]) }}" title="{{ __('View User') }}">
                                                    <i class="fas fa-eye fa-fw text-success"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">{{ __('No users found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $users->count() ? $users->links() : ''}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('modals.confirm-trash')
@endpush
