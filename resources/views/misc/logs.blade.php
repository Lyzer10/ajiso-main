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
            <li><span>{{ __('System Logs') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- beneficiary list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        {{  __('System logs') }}
                        <a href="{{ route('system.logs.clean', app()->getLocale()) }}" class="btn btn-sm light-custom-color pull-right text-white">
                            {{ __('Clean Logs') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="text-capitalize">
                                    <tr>
                                        <th scope="col">{{ __('Timestamp') }}</th>
                                        <th scope="col">{{ __('User') }}</th>
                                        <th scope="col">{{ __('User Role') }}</th>
                                        <th scope="col">{{ __('Activity logged') }}</th>
                                    </tr>
                                </thead>
                                @forelse ($activity_logs as $log)
                                <tbody>
                                    @php
                                        $causer = $log['causer'];
                                        $name = $causer->first_name.' '.$causer->middle_name.' '.$causer->last_name;
                                    @endphp
                                        <tr>
                                            <td>
                                                <kbd>
                                                    {{ Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:I:s') }}
                                                </kbd>
                                            </td>
                                            <td>
                                                <code>
                                                    {{  $name }}
                                                </code>
                                            </td>
                                            <td>{{ $causer->role->role_name }}</td>
                                            <td>{{ $log->description }}</td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <tbody>
                                        <tr>
                                            <td class="p-1 mb-2" colspan="4">
                                                {{ __('No logs available.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforelse
                            </table>
                        </div>
                    </div>
                    {{ $activity_logs->count() ? $activity_logs->links() : ''}}
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection
