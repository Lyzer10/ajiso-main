@extends('layouts.base')

@php
    $title = __('Reports') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Reports') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Disputes Summary') }}</span></li>
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
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="header-title col-md-6">{{ __('Case Assignment Summary') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('summaries.disputes.filter.staff', app()->getLocale()) }}" method="GET" role="search">
                        <div class="row">
                            <div class="col-md-2 mt-4">
                                <h6>
                                    <b>{{ __('Total cases') }} : </b>
                                    <span class="badge text-white light-custom-color">{{ $total ?? '0'}}</span>
                                </h6>
                            </div>
                            <div class="col-md-4 mt-4">
                                <h6><b>{{ __('Dates') }} : </b><a class="text-primary"> {{ $date_ranges ?? __('All Time') }} </a></h6>
                            </div>
                            <div class="col-md-4" id="date">
                                <div class="form-group">
                                    <label>{{ __('Filter By Dates') }}<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control border-input-primary float-right" id="daterange" name="dateRange">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary float-right" id="daterange-btn" required>
                                                <i class="far fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mt-4 pt-1">
                                <button class="btn btn-primary float-right" type="submit">
                                    <i class="fas fa-filter fa-fw"></i>
                                    {{ __('Filter') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="font-weight-light">{{ __('Case assignment is summarized as follows;') }}</h6>
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <ul class="list-group">
                                <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                    {{ __('Type of Case') }}
                                </li>
                                @if ($type_of_cases->count())
                                    @foreach ($type_of_cases as $type_of_case)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ __($type_of_case->type_of_case) }}
                                        @php
                                            $toc = '0%';
                                        @endphp
                                        @if ($group_by_case->count())
                                            @foreach ($group_by_case as $case)
                                                @if ($type_of_case->id === $case->type_of_case_id)
                                                    @php
                                                        $toc = floor($case->total*100/$total).'%' ?? '0%';
                                                    @endphp
                                                    @break
                                                @endif
                                            @endforeach
                                        <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                            {{ $toc ?? '0%'}}
                                        </span>
                                        @else
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                            {{ '0%' }}
                                        </span>
                                        @endif
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="">
                                <ul class="list-group">
                                    <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                        {{ __('Type of Service') }}
                                    </li>
                                    @if ($type_of_services->count())
                                        @foreach ($type_of_services as $type_of_service)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ __($type_of_service->type_of_service) }}
                                            @php
                                                $tos = '0%';
                                            @endphp
                                            @if ($group_by_service->count())
                                                @foreach ($group_by_service as $service)
                                                    @if ($type_of_service->id === $service->type_of_service_id)
                                                        @php
                                                            $tos = floor($service->total*100/$total).'%' ?? '0%';
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                {{ $tos ?? '0%'}}
                                            </span>
                                            @else
                                                <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                            {{ '0%' }}
                                        </span>
                                            @endif
                                        </li>
                                        @endforeach
                                    @endif
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-3">
                                <ul class="list-group">
                                    <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                        {{ __('Dispute Status') }}
                                    </li>
                                    @if ($dispute_statuses->count())
                                    @foreach ($dispute_statuses as $dispute_status)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ __($dispute_status->dispute_status) }}
                                        @php
                                            $ds = '0%';
                                        @endphp
                                        @if ($group_by_status->count())
                                            @foreach ($group_by_status as $status)
                                                @if ($dispute_status->id === $status->dispute_status_id)
                                                    @php
                                                        $ds = floor($status->total*100/$total).'%' ?? '0%';
                                                    @endphp
                                                    @break
                                                @endif
                                            @endforeach
                                        <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                            {{ $ds ?? '0%'}}
                                        </span>
                                        @else
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                {{ '0%' }}
                                            </span>
                                        @endif
                                    </li>
                                    @endforeach
                                @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection

@push('scripts')
    @include('dates.js')

    <script>
        //Date range as a button
        $("#daterange").daterangepicker(
            {
            format: "L",
            timePicker: false,
            autoApply: true,
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
                ],
            },
            startDate: moment().subtract(29, "days"),
            endDate: moment(),
            },
            function (start, end) {
            $("#daterange").val(
                start.format("MM/DD/YYYY") + " - " + end.format("MM/DD/YYYY")
            );
            }
        );
    </script>
@endpush
