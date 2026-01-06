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
                    <div class="row mt-3">
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Gender') }}</div>
                                <div class="viz-card__body">
                                    @if ($group_by_gender->count())
                                        @foreach ($group_by_gender as $gender)
                                            @php
                                                $percent = $total > 0 ? floor($gender->total * 100 / $total) : 0;
                                            @endphp
                                            <div class="viz-row">
                                                <div class="viz-label">{{ $gender->gender }}</div>
                                                <div class="viz-bar">
                                                    <span style="width: {{ $percent }}%;"></span>
                                                </div>
                                                <div class="viz-value">{{ $percent }}%</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="viz-row">
                                            <div class="viz-label">{{ __('No data') }}</div>
                                            <div class="viz-bar">
                                                <span style="width: 0%;"></span>
                                            </div>
                                            <div class="viz-value">0%</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Age Group') }}</div>
                                <div class="viz-card__body">
                                    @if ($age_groups->count())
                                        @foreach ($age_groups as $age_group)
                                            @php
                                                $percent = 0;
                                            @endphp
                                            @if ($group_by_age->count())
                                                @foreach ($group_by_age as $age)
                                                    @if ($age_group->id === $age->age)
                                                        @php
                                                            $percent = $total > 0 ? floor($age->total * 100 / $total) : 0;
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                            <div class="viz-row">
                                                <div class="viz-label">{{ $age_group->age_group }}</div>
                                                <div class="viz-bar">
                                                    <span style="width: {{ $percent }}%;"></span>
                                                </div>
                                                <div class="viz-value">{{ $percent }}%</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="viz-row">
                                            <div class="viz-label">{{ __('No data') }}</div>
                                            <div class="viz-bar">
                                                <span style="width: 0%;"></span>
                                            </div>
                                            <div class="viz-value">0%</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Monthly Income') }}</div>
                                <div class="viz-card__body">
                                    @if ($income_groups->count())
                                        @foreach ($income_groups as $income_group)
                                            @php
                                                $percent = 0;
                                            @endphp
                                            @if ($group_by_income->count())
                                                @foreach ($group_by_income as $income)
                                                    @if ($income_group->id === $income->income_id)
                                                        @php
                                                            $percent = $total > 0 ? floor($income->total * 100 / $total) : 0;
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                            <div class="viz-row">
                                                <div class="viz-label">{{ $income_group->income }}</div>
                                                <div class="viz-bar">
                                                    <span style="width: {{ $percent }}%;"></span>
                                                </div>
                                                <div class="viz-value">{{ $percent }}%</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="viz-row">
                                            <div class="viz-label">{{ __('No data') }}</div>
                                            <div class="viz-bar">
                                                <span style="width: 0%;"></span>
                                            </div>
                                            <div class="viz-value">0%</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">
                                    <span>{{ __('Employment Status') }}</span>
                                    <span class="viz-card__meta">{{ __('All time') }}</span>
                                </div>
                                <div class="viz-card__body">
                                    @if ($employment_statuses->count())
                                        @foreach ($employment_statuses as $employment_status)
                                            @php
                                                $percent = 0;
                                            @endphp
                                            @if ($group_by_occupation->count())
                                                @foreach ($group_by_occupation as $occupation)
                                                    @if ($employment_status->id === $occupation->employment_status_id)
                                                        @php
                                                            $percent = $total > 0 ? floor($occupation->total * 100 / $total) : 0;
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                            <div class="viz-row">
                                                <div class="viz-label">{{ $employment_status->employment_status }}</div>
                                                <div class="viz-bar">
                                                    <span style="width: {{ $percent }}%;"></span>
                                                </div>
                                                <div class="viz-value">{{ $percent }}%</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="viz-row">
                                            <div class="viz-label">{{ __('No data') }}</div>
                                            <div class="viz-bar">
                                                <span style="width: 0%;"></span>
                                            </div>
                                            <div class="viz-value">0%</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Education Level') }}</div>
                                <div class="viz-card__body">
                                    @if ($education_levels->count())
                                        @foreach ($education_levels as $education_level)
                                            @php
                                                $percent = 0;
                                            @endphp
                                            @if ($group_by_education->count())
                                                @foreach ($group_by_education as $education)
                                                    @if ($education_level->id === $education->education_level_id)
                                                        @php
                                                            $percent = $total > 0 ? floor($education->total * 100 / $total) : 0;
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                            <div class="viz-row">
                                                <div class="viz-label">{{ $education_level->education_level }}</div>
                                                <div class="viz-bar">
                                                    <span style="width: {{ $percent }}%;"></span>
                                                </div>
                                                <div class="viz-value">{{ $percent }}%</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="viz-row">
                                            <div class="viz-label">{{ __('No data') }}</div>
                                            <div class="viz-bar">
                                                <span style="width: 0%;"></span>
                                            </div>
                                            <div class="viz-value">0%</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Marital Status') }}</div>
                                <div class="viz-card__body">
                                    @if ($marital_statuses->count())
                                        @foreach ($marital_statuses as $marital_status)
                                            @php
                                                $percent = 0;
                                            @endphp
                                            @if ($group_by_marital->count())
                                                @foreach ($group_by_marital as $marital)
                                                    @if ($marital_status->id === $marital->marital_status_id)
                                                        @php
                                                            $percent = $total > 0 ? floor($marital->total * 100 / $total) : 0;
                                                        @endphp
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                            <div class="viz-row">
                                                <div class="viz-label">{{ $marital_status->marital_status }}</div>
                                                <div class="viz-bar">
                                                    <span style="width: {{ $percent }}%;"></span>
                                                </div>
                                                <div class="viz-value">{{ $percent }}%</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="viz-row">
                                            <div class="viz-label">{{ __('No data') }}</div>
                                            <div class="viz-bar">
                                                <span style="width: 0%;"></span>
                                            </div>
                                            <div class="viz-value">0%</div>
                                        </div>
                                    @endif
                                </div>
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

        $("#daterange-btn").on("click", function () {
            var picker = $("#daterange").data("daterangepicker");
            if (!picker) {
                return;
            }
            if (picker.isShowing) {
                picker.hide();
            } else {
                picker.show();
            }
        });

        $("#daterange").on("apply.daterangepicker cancel.daterangepicker", function (ev, picker) {
            picker.hide();
        });
    </script>
@endpush
