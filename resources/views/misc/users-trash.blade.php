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
                <!-- trash list area start -->
                <div class="col-md-12 mt-5">
                    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <i class='fas fa-exclamation-triangle fa-fw text-danger'></i>
                        {{ __("Records in trash, will be permanently deleted after ") }}
                        <code class="text-danger">{{ __('60 days.') }}</code>
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                </div>
                <!-- trash list area end -->
            </div>
        <!-- beneficiary list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        {{ __('Users Trash') }}
                        <a href="{{ route('system.trash', app()->getLocale())}}" class="btn btn-sm text-white light-custom-color pull-right">
                            {{ __('Back')}}
                        </a>
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table progress-table text-center table-striped">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('Username') }}</th>
                                    <th>{{ __('Full Name') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Original Location') }}</th>
                                    <th>{{ __('Trashed On') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
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
                                    <td>{{ $user->role->role_name }}</td>
                                    <td>{{ __('Users') }}</td>
                                    <td>{{ Carbon\Carbon::parse($user->deleted_at)->format('d-m-Y H:I:s') }}</td>
                                    <td class="d-flex">
                                        <form method="POST" action="{{ route('user.restore', [app()->getLocale(), $user->id]) }}">
                                            @csrf
                                            @method('PUT')
                                                <i class="fas fa-trash-restore-alt fa-fw text-success show_restore" data-toggle="tooltip" title="{{  __('Restore User') }}"></i>
                                        </form>
                                        /
                                        <form method="POST" action="{{ route('user.delete', [app()->getLocale(), $user->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                                <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete User') }}"></i>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-1">{{ __('No trashed user found') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $users->count() ? $users->links() : ''}}
                    </div>
                </div>
                <div class="card-footer">
                    {{-- <div class="header-title clearfix">
                        <a href="" class="btn btn-sm btn-secondary pull-right">{{ __('Empty') }}</a>
                        <a href="" class="btn btn-sm btn-outline-secondary pull-right mr-2">{{ __('Restore') }}</a>
                    </div> --}}
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection

@push('scripts')
    {{-- Include the sweetalert --}}
    @include('modals.confirm-restore-delete')

@endpush