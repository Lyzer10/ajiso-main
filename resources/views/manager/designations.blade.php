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
            <li><span>{{ __('Designations - Titles Manager') }}</span></li>
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
                        <form action="{{ route('manager.designation.store', app()->getLocale()) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="designationTitle" class="font-weight-bold">{{ __('Designation / Title') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="designationTitle" placeholder="{{ __('designation / title') }}"
                                        class="form-control  border-input-primary @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="designation">
                                    @error('designation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">

                                </div>
                                <div class="col-md-4 mt-4">
                                    <button class="btn text-white light-custom-color float-right" type="submit ">{{ __('Add Title') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <!-- desgnations area start -->
            <div class=" col-lg-12 mt-3">
                <div class="card ">
                    <div class="card-header ">
                        <div class="header-title clearfix ">
                            <div class="header-title clearfix">
                                {{ __('Designation / Titles') }}
                                <a href="{{ route('settings.manager', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                                    {{ __('Back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <div class="table-responsive">
                            <table class="table table-sm text-center table-striped">
                                <thead class="text-capitalize text-white light-custom-color">
                                    <tr>
                                        <th>ID</th>
                                        <th>{{ __('Title Type')}}</th>
                                        <th colspan="2">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($designations as $designation)
                                    <tr>
                                        <form method="POST" action="{{ route('manager.designation.update', [app()->getLocale(), $designation->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <td>{{ '#'.$designation->id }}</td>
                                            <td>
                                                <input type="text" id="designation" placeholder="designation"
                                                    class="form-control-sm border-0 @error('name') is-invalid @enderror"
                                                    name="name" value="{{ $designation->name}}" required autocomplete="designation">
                                                @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                @enderror
                                            </td>
                                            <td>
                                                <button role="button" class="btn btn-light border-0" title="Update Designation / Title">
                                                    <i class="fas fa-upload fa-fw text-success"></i>
                                                </button>
                                            </td>
                                        </form>
                                        <td class="d-flex justify-content-between">
                                            @can('isSuperAdmin')
                                            /
                                            <form method="POST" action="{{ route('manager.designation.trash', [app()->getLocale(), $designation->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                    <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Designation / Title') }}"></i>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td class="p-1">{{ __('No titles found')}}</td>
                                </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- desgnations  area end -->
        </div>
    </div>
@endsection

@push('scripts')

    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

@endpush
