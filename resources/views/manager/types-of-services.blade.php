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
            <li><span>{{ __('Services Types Manager') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <div class="row">
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('manager.services.type.store', app()->getLocale()) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="service_abbr" class="font-weight-bold">{{ __('Service Type Abbreviation') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="serviceAbbr" placeholder="{{ __('Service Abbreviation') }}"
                                        class="form-control  border-input-primary @error('service_abbreviation') is-invalid @enderror"
                                        name="service_abbreviation" value="{{ old('service_abbreviation') }}" required autocomplete="service_abbreviation">
                                    @error('service_abbreviation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="service_name" class="font-weight-bold">{{ __('Service Name') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="serviceName" placeholder="{{ __('Service Name') }}"
                                        class="form-control  border-input-primary @error('service_name') is-invalid @enderror"
                                        name="service_name" value="{{ old('service_name') }}" required autocomplete="service_name">
                                    @error('service_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-4">
                                    <button class="btn text-white light-custom-color float-right" type="submit ">{{ __('Add Service') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <!-- TOS area start -->
            <div class=" col-lg-12 mt-3">
                <div class="card ">
                    <div class="card-header ">
                        <div class="header-title clearfix ">
                            <div class="header-title clearfix">
                                {{ __('Types of Services') }}
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
                                        <th>{{ __('Service Abbreviation') }}</th>
                                        <th>{{ __('Service Name') }}</th>
                                        <th colspan="2">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($types_of_services->count())
                                        @foreach ($types_of_services as $types_of_service)
                                        <tr>
                                            <form action="{{ route('manager.services.type.update', [app()->getLocale(), $types_of_service->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <td>{{ '#'.$types_of_service->id }}</td>
                                                <td>
                                                    <input type="text" id="serviceAbbr" placeholder="Service abbreviation"
                                                        class="form-control-sm border-0 @error('service_abbreviation') is-invalid @enderror"
                                                        name="service_abbreviation" value="{{ $types_of_service->service_abbreviation }}" required autocomplete="service_abbreviation">
                                                    @error('service_abbreviation')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" id="serviceName" placeholder="Service name"
                                                        class="form-control-sm border-0 @error('service_name') is-invalid @enderror"
                                                        name="service_name" value="{{ $types_of_service->type_of_service }}" required autocomplete="service_name">
                                                    @error('service_name')
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
                                                <form method="POST" action="{{ route('manager.services.type.trash', [app()->getLocale(), $types_of_service->id]) }}">
                                                    @csrf
                                                    @METHOD('PUT')
                                                        <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Service') }}"></i>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <td>{{ __('No services found')}}</td>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TOS  area end -->
        </div>
    </div>
@endsection

@push('scripts')

    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

@endpush
