@extends('layouts.base')

@php
    $title = __('Settings') 
@endphp
@section('title', 'LAIS | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Settings') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Dispute Status Manager') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
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
        <div class="row">
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('manager.disputes.status.store', app()->getLocale()) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="dispute_status" class="font-weight-bold">{{ __('Status Name') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="dispute" placeholder="Status name"
                                        class="form-control  border-input-primary @error('dispute_status') is-invalid @enderror"
                                        name="dispute_status" value="{{ old('dispute_status') }}" required autocomplete="dispute_status">
                                    @error('dispute_status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">

                                </div>
                                <div class="col-md-4 mt-4">
                                    <button class="btn text-white light-custom-color float-right" type="submit ">{{ __('Add Status') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <!-- Dispute status area start -->
            <div class=" col-lg-12 mt-3">
                <div class="card ">
                    <div class="card-header ">
                        <div class="header-title clearfix ">
                            <div class="header-title clearfix">
                                {{ __('Disputes Statuses') }}
                                <a href="{{ route('settings.manager', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                                    {{ __('Back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <div class="table-responsive">
                            <table class="table progress-table text-center table-striped">
                                <thead class="text-capitalize text-white light-custom-color">
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('Status Type') }}</th>
                                        <th colspan="2">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($dispute_statuses->count())
                                        @foreach ($dispute_statuses as $dispute_status)
                                        <tr>
                                            <form action="{{ route('manager.disputes.status.update', [app()->getLocale(), $dispute_status->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <td>{{ '#'.$dispute_status->id }}</td>
                                                <td>
                                                    <input type="text" id="case" placeholder="dispute status"
                                                        class="form-control-sm border-0 @error('dispute_status') is-invalid @enderror"
                                                        name="dispute_status" value="{{ $dispute_status->dispute_status}}" required autocomplete="dispute_status">
                                                    @error('dispute_status')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <button role="button" class="btn btn-light border-0" title="Update">
                                                        <i class="fas fa-upload fa-fw text-success"></i>
                                                    </button>
                                                </td>
                                            </form>
                                            <td class="d-flex justify-content-between">
                                                @can('isSuperAdmin')
                                                /
                                                <form method="POST" action="{{ route('manager.disputes.status.trash', [app()->getLocale(), $dispute_status->id]) }}">
                                                    @csrf
                                                    @METHOD('PUT')
                                                        <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Dispute Status') }}"></i>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <td>{{ __('No status found')}}</td>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dispute status  area end -->
        </div>
    </div>
@endsection

@push('scripts')

    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

@endpush

