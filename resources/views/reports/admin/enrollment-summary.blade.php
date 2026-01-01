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
            <li><span>{{ __('Enrollment Summary') }}</span></li>
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
                    <h4 class="header-title col-md-6">{{ __('Beneficiary Enrollment Summary') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('summaries.beneficiaries.filter', app()->getLocale()) }}" method="GET" role="search">
                        <div class="row">
                            <div class="col-md-2 mt-4">
                                <h6>
                                    <b>{{ __('Total') }} : </b>
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
                    <h6 class="font-weight-light">{{ __('Beneficiary enrollment is summarized based on demographic categories as follows;') }}</h6>
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <div class="mt-3">
                                <ul class="list-group">
                                    <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                        {{ __('Gender') }}
                                    </li>
                                    @if ($group_by_gender->count())
                                        @foreach ($group_by_gender as $gender)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $gender->gender }}
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                {{ ($gender->total > 0) ? (floor($gender->total*100/$total)).'%' : '0%'}}
                                            </span>
                                        </li>
                                        @endforeach
                                    @else
                                        <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                            {{ '0%' }}
                                        </span>
                                    @endif
                                </ul>
                            </div>
                            <div class="mt-3">
                                <ul class="list-group">
                                    <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                        {{ __('Age Group') }}
                                    </li>
                                    @if ($age_groups->count())
                                        @foreach ($age_groups as $age_group)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $age_group->age_group }}
                                            @php
                                                $grp = '0%';
                                            @endphp
                                            @if ($group_by_age->count())
                                                @foreach ($group_by_age as $age)
                                                    @if ($age_group->id === $age->age)
                                                        @php
                                                            $grp = floor($age->total*100/$total).'%' ?? '0%';
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                {{ $grp ?? '0%'}}
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
                            <div class="mt-3">
                                <ul class="list-group">
                                    <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                        {{ __('Monthly Income') }}
                                    </li>
                                    @if ($income_groups->count())
                                        @foreach ($income_groups as $income_group)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $income_group->income }}
                                            @php
                                                $gri = '0%';
                                            @endphp
                                            @if ($group_by_income->count())
                                                @foreach ($group_by_income as $income)
                                                    @if ($income_group->id === $income->income_id)
                                                        @php
                                                            $gri = floor($income->total*100/$total).'%' ?? '0%';
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                {{ $gri ?? '0%'}}
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
                        <div class="col-md-6 mt-3">
                            <div class="mt-3">
                            <ul class="list-group">
                                <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                    {{ __('Employment Status') }}
                                    <span class="float-right">{{ __('All time') }}</span>
                                </li>
                                @if ($employment_statuses->count())
                                    @foreach ($employment_statuses as $employment_status)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $employment_status->employment_status }}
                                        @php
                                            $occ = '0%';
                                        @endphp
                                        @if ($group_by_occupation->count())
                                            @foreach ($group_by_occupation as $occupation)
                                                @if ($employment_status->id === $occupation->employment_status_id)
                                                    @php
                                                        $occ = floor($occupation->total*100/$total).'%' ?? '0%';
                                                    @endphp
                                                    @break
                                                @endif
                                            @endforeach
                                        <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                            {{ $occ ?? '0%'}}
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
                            <div class="mt-3">
                                <ul class="list-group">
                                    <li class="list-group-item  font-weight-bold text-white dark-custom-color">
                                        {{ __('Education Level') }}
                                    </li>
                                    @if ($education_levels->count())
                                        @foreach ($education_levels as $education_level)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $education_level->education_level }}
                                                @php
                                                    $edl = '0%';
                                                @endphp
                                                @if ($group_by_education->count())
                                                    @foreach ($group_by_education as $education)
                                                        @if ($education_level->id === $education->education_level_id)
                                                            @php
                                                                $edl = floor($education->total*100/$total).'%' ?? '0%';
                                                            @endphp
                                                            @break
                                                        @endif
                                                    @endforeach
                                                <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                    {{ $edl ?? '0%'}}
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
                            <div class="mt-3">
                                <ul class="list-group">
                                    <li class="list-group-item font-weight-bold text-white dark-custom-color">
                                        {{ __('Marital Status') }}
                                    </li>
                                    @if ($marital_statuses->count())
                                        @foreach ($marital_statuses as $marital_status)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $marital_status->marital_status }}
                                            @php
                                                $gbm = '0%';
                                            @endphp
                                            @if ($group_by_marital->count())
                                                @foreach ($group_by_marital as $marital)
                                                    @if ($marital_status->id === $marital->marital_status_id)
                                                        @php
                                                            $gbm = floor($marital->total*100/$total).'%' ?? '0%';
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            <span class="badge text-white light-custom-color badge-pill p-2 lead">
                                                {{ $gbm ?? '0%'}}
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
