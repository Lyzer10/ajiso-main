@extends('layouts.base')

@php
    $title = __('Settings') 
@endphp
@section('title', 'LAIS | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Settings') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Metric Manager') }}</span></li>
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
                        <form action="{{ route('manager.metric.store', app()->getLocale()) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="metricName" class="font-weight-bold">{{ __('Metric Name') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="metricName" placeholder="{{ __('Metric Name') }}"
                                        class="form-control  border-input-primary @error('metric') is-invalid @enderror"
                                        name="metric" value="{{ old('metric') }}" required autocomplete="metric">
                                    @error('metric')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="metricMeasure" class="font-weight-bold">{{ __('Metric Measure') }}<sup class="text-danger">*</sup></label>
                                    <select id="metricMeasure" aria-describedby="metricMeasure"
                                        class="select2 select2-container--default   border-input-primary @error('metric_measure') is-invalid @enderror"
                                        name="metric_measure" required autocomplete="metric_measure" style="width: 100%;">
                                        <option hidden disabled selected value>{{ __('Choose metric measure') }}</option>
                                        @if ($metric_measures->count())
                                            @foreach ($metric_measures as $metric_measure)
                                                <option value="{{ $metric_measure->id }}">
                                                    {{ $metric_measure->metric_measure }}
                                                </option>
                                            @endforeach
                                        @else
                                            <span>{{ __('No measures found') }}</span>
                                        @endif
                                    </select>
                                    @error('metric_measure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="MetricLimit" class="font-weight-bold">{{ __('Metric Limit') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="MetricLimit" placeholder="{{ __('Metric Limit') }}"
                                        class="form-control  border-input-primary @error('metric_limit') is-invalid @enderror"
                                        name="metric_limit" value="{{ old('metric_limit') }}" required autocomplete="metric_limit">
                                    @error('metric_limit')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 mt-4">
                                    <button class="btn text-white light-custom-color float-right" type="submit ">{{ __('Add Metric') }}</button>
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
                                {{ __('Metrics') }}
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
                                        <th>{{ __('Metric Name') }}</th>
                                        <th>{{ __('Metric Measure') }}</th>
                                        <th>{{ __('Metric Limit') }}</th>
                                        <th colspan="2">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($metrics->count())
                                        @foreach ($metrics as $metric)
                                        <tr>
                                            <form action="{{ route('manager.metric.update', [app()->getLocale(), $metric->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <td>{{ '#'.$metric->id }}</td>
                                                <td>
                                                    <input type="text" id="metric" placeholder="metric name"
                                                        class="form-control-sm border-0 @error('metric') is-invalid @enderror"
                                                        name="metric" value="{{ $metric->metric }}" required autocomplete="metric">
                                                    @error('metric')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <select id="metric_measure" aria-describedby="metricMeasure"
                                                        class="select2 select2-container--default border-0 @error('metric_measure') is-invalid @enderror"
                                                        name="metric_measure" required autocomplete="metric_measure" style="width: 100%;">
                                                        <option hidden disabled selected value>{{ __('Choose metric measure') }}</option>
                                                        @if ($metric_measures->count())
                                                            @foreach ($metric_measures as $metric_measure)
                                                                <option value="{{ $metric_measure->id }}" @if ($metric_measure->id === $metric->metricMeasure->id)
                                                                    selected = 'selected'
                                                                @endif>
                                                                    {{ $metric_measure->metric_measure }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            <span>{{ __('No measures found') }}</span>
                                                        @endif
                                                    </select>
                                                    @error('metric_measure')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" id="metric_limit" placeholder="metric limit"
                                                        class="form-control-sm border-0 @error('metric_limit') is-invalid @enderror"
                                                        name="metric_limit" value="{{ $metric->metric_limit }}" required autocomplete="metric_limit">
                                                    @error('metric_limit')
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
                                                <form method="POST" action="{{ route('manager.metric.trash', [app()->getLocale(), $metric->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                        <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Metric') }}"></i>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <td>{{ __('No metrics found')}}</td>
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
    @include('dates.js')

    {{-- Select2 --}}
    <script type="text/javascript">
        $(function() {
            $('.select2').select2();
        });
    </script>

    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

@endpush
