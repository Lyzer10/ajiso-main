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
                        {{ __('Beneficiaries Trash') }} 
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table progress-table text-center table-striped">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('User No') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Registered On') }}</th>
                                    <th>{{ __('Original Location') }}</th>
                                    <th>{{ __('Trashed On') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($beneficiaries as $beneficiary)
                                <tr>
                                    <td>{{ $beneficiary->id }}</td>
                                    <td>{{ $beneficiary->user_id }}</td>
                                    <td>{{ $beneficiary->address ?? 'N/A' }}</td>
                                    <td>{{ Carbon\Carbon::parse($beneficiary->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ __('Beneficiaries') }}</td>
                                    <td>{{ Carbon\Carbon::parse($beneficiary->deleted_at)->format('d-m-Y H:I:s') }}</td>
                                    <td class="d-flex">
                                        <form method="POST" action="{{ route('beneficiary.restore', [app()->getLocale(), $beneficiary->id]) }}">
                                            @csrf
                                            @METHOD('PUT')
                                                <i class="fas fa-trash-restore-alt fa-fw text-success show_restore" data-toggle="tooltip" title="{{  __('Restore User') }}"></i>
                                        </form>
                                        <form method="POST" action="{{ route('beneficiary.delete', [app()->getLocale(), $beneficiary->id]) }}">
                                            @csrf
                                            @METHOD('DELETE')
                                                <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete User') }}"></i>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="p-1">{{ __('No trashed beneficiary found') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
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