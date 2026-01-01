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
            <li><span>{{ __('Metric Measures Manager') }}</span></li>
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
                        <form action="{{ route('manager.metric.measure.store', app()->getLocale()) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="metric_measureName" class="font-weight-bold">{{ __('Metric Measure') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="metric_measureName" placeholder="Metric measure"
                                        class="form-control  border-input-primary @error('metric_measure') is-invalid @enderror"
                                        name="metric_measure" value="{{ old('metric_measure') }}" required autocomplete="metric_measure">
                                    @error('metric_measure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">

                                </div>
                                <div class="col-md-4 mt-4">
                                    <button class="btn text-white light-custom-color float-right" type="submit ">{{ __('Add Measure') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <!-- Metric Measuresarea start -->
            <div class=" col-lg-12 mt-3">
                <div class="card ">
                    <div class="card-header ">
                        <div class="header-title clearfix ">
                            <div class="header-title clearfix">
                                {{ __('Metric Measures') }}
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
                                        <th>{{ __('Measure Name') }}</th>
                                        <th colspan="2">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($metric_measures->count())
                                        @foreach ($metric_measures as $metric_measure)
                                        <tr>
                                            <form action="{{ route('manager.metric.measure.update', [app()->getLocale(), $metric_measure->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <td>{{ '#'.$metric_measure->id }}</td>
                                                <td>
                                                    <input type="text" id="metric_measure" placeholder="metric measure"
                                                        class="form-control-sm border-0 @error('metric_measure') is-invalid @enderror"
                                                        name="metric_measure" value="{{ $metric_measure->metric_measure }}" required autocomplete="metric_measure">
                                                    @error('metric_measure')
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
                                                <form method="POST" action="{{ route('manager.metric.measure.trash', [app()->getLocale(), $metric_measure->id]) }}">
                                                    @csrf
                                                    @METHOD('PUT')
                                                        <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Metric Measure') }}"></i>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <td>{{ __('No metric measures found')}}</td>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Metric Measures area end -->
        </div>
    </div>
@endsection

@push('scripts')

    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

@endpush
