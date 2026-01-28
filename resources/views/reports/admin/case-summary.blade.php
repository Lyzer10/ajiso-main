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
                    <h4 class="header-title col-md-6">{{ __('Case Registration Summary') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('summaries.disputes.filter', app()->getLocale()) }}" method="GET" role="search">
                        <div class="row">
                            <div class="col-md-2 mt-4">
                                <h6>
                                    <b>{{ __('Total Cases') }} : </b>
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
                    <h6 class="font-weight-light">{{ _('Case registration is summarized as follows;') }}</h6>
                    <div class="row mt-3">
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Type of Case') }}</div>
                                <div class="viz-card__body">
                                    <div id="caseTypeChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Type of Service') }}</div>
                                <div class="viz-card__body">
                                    <div id="serviceTypeChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Dispute Status') }}</div>
                                <div class="viz-card__body">
                                    <div id="disputeStatusChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $case_demographics = $case_demographics ?? [];
                        $age_group_distribution = $age_group_distribution ?? [];
                    @endphp
                    <div class="case-demographics mt-4">
                        <div class="case-demographics__header">
                            <span>{{ __('Case Demographics Breakdown') }}</span>
                        </div>
                        <div class="case-demographics__grid">
                            @forelse ($case_demographics as $case)
                                <div class="case-demographics__card">
                                    <div class="case-demographics__name">{{ __($case['label']) }}</div>
                                    <div class="case-demographics__stats">
                                        <div class="case-demographics__stat">
                                            <span class="stat-icon stat-icon--male">
                                                <i class="fas fa-mars"></i>
                                            </span>
                                            <span class="case-demographics__label">{{ __('Male') }}</span>
                                            <span class="case-demographics__value">{{ $case['male'] }}</span>
                                        </div>
                                        <div class="case-demographics__stat">
                                            <span class="stat-icon stat-icon--female">
                                                <i class="fas fa-venus"></i>
                                            </span>
                                            <span class="case-demographics__label">{{ __('Female') }}</span>
                                            <span class="case-demographics__value">{{ $case['female'] }}</span>
                                        </div>
                                        <div class="case-demographics__stat">
                                            <span class="stat-icon stat-icon--age">
                                                <i class="fas fa-users"></i>
                                            </span>
                                            <span class="case-demographics__label">{{ __($case['top_age_group_label']) }}</span>
                                            <span class="case-demographics__value">{{ $case['top_age_group_count'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="case-demographics__empty">{{ __('No demographics available') }}</div>
                            @endforelse
                        </div>
                        <div class="age-group-strip">
                            <div class="age-group-strip__title">{{ __('Age Group Distribution') }}</div>
                            <div class="age-group-strip__grid">
                                @foreach ($age_group_distribution as $age_group)
                                    <div class="age-group-chip">
                                        <div class="age-group-chip__label">{{ __($age_group['label']) }}</div>
                                        <div class="age-group-chip__values">
                                            <span class="age-group-chip__value age-group-chip__value--male">
                                                {{ $age_group['male'] }} {{ __('Male') }}
                                            </span>
                                            <span class="age-group-chip__value age-group-chip__value--female">
                                                {{ $age_group['female'] }} {{ __('Female') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
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

    <!-- AmCharts Resources -->
    <script src="{{ asset('plugins/amcharts/4/core.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/charts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/themes/animated.js') }}"></script>

    @php
        $caseTypeData = [];
        foreach ($type_of_cases as $type_of_case) {
            $count = 0;
            foreach ($group_by_case as $case) {
                if ($type_of_case->id === $case->type_of_case_id) {
                    $count = (int) $case->total;
                    break;
                }
            }
            $caseTypeData[] = ['category' => __($type_of_case->type_of_case), 'value' => $count];
        }

        $serviceTypeData = [];
        foreach ($type_of_services as $type_of_service) {
            $count = 0;
            foreach ($group_by_service as $service) {
                if ($type_of_service->id === $service->type_of_service_id) {
                    $count = (int) $service->total;
                    break;
                }
            }
            $serviceTypeData[] = ['category' => __($type_of_service->type_of_service), 'value' => $count];
        }

        $isParalegalView = auth()->user() && auth()->user()->can('isClerk');
        $excludedStatusSlugs = $isParalegalView
            ? ['judged', 'discontinued', 'discontinue', 'pending']
            : [];
        $filteredDisputeStatuses = $dispute_statuses;
        if ($excludedStatusSlugs) {
            $filteredDisputeStatuses = $dispute_statuses->reject(function ($status) use ($excludedStatusSlugs) {
                return in_array(\Illuminate\Support\Str::slug($status->dispute_status), $excludedStatusSlugs, true);
            })->values();
        }

        $statusData = [];
        foreach ($filteredDisputeStatuses as $dispute_status) {
            $count = 0;
            foreach ($group_by_status as $status) {
                if ($dispute_status->id === $status->dispute_status_id) {
                    $count = (int) $status->total;
                    break;
                }
            }
            $statusData[] = ['category' => __($dispute_status->dispute_status), 'value' => $count];
        }
    @endphp

    <script>
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);

            function buildDonutChart(containerId, data, prefix, unitLabel) {
                var chart = am4core.create(containerId, am4charts.PieChart);
                chart.logo.disabled = true;
                chart.hiddenState.properties.opacity = 0;
                chart.data = data;
                chart.innerRadius = am4core.percent(45);
                chart.numberFormatter.numberFormat = "#,###";

                var series = chart.series.push(new am4charts.PieSeries());
                series.dataFields.value = "value";
                series.dataFields.category = "category";
                series.slices.template.stroke = am4core.color("#fff");
                series.slices.template.strokeWidth = 2;
                series.slices.template.tooltipText = "{category}: {value.value} " + unitLabel + " ({value.percent.formatNumber('#.0')}%)";
                series.labels.template.fontSize = 12;
                series.labels.template.text = "{category}: {value.value}";
                series.labels.template.wrap = true;
                series.labels.template.maxWidth = 140;
                series.ticks.template.strokeOpacity = 0.2;

                chart.legend = new am4charts.Legend();
                chart.legend.fontSize = 12;
                chart.legend.position = "right";
                chart.legend.labels.template.wrap = true;
                chart.legend.labels.template.maxWidth = 160;
                chart.legend.labels.template.truncate = false;
                chart.legend.valueLabels.template.text = "{value.value}";

                chart.exporting.menu = new am4core.ExportMenu();
                chart.exporting.filePrefix = prefix;

                return chart;
            }

            var caseChart = buildDonutChart("caseTypeChart", @json($caseTypeData), "case-type-summary", @json(__('cases')));
            var serviceChart = buildDonutChart("serviceTypeChart", @json($serviceTypeData), "service-type-summary", @json(__('services')));
            var statusChart = buildDonutChart("disputeStatusChart", @json($statusData), "dispute-status-summary", @json(__('cases')));

            if (serviceChart.series.values.length) {
                serviceChart.series.values[0].labels.template.disabled = true;
                serviceChart.series.values[0].ticks.template.disabled = true;
                serviceChart.legend.labels.template.maxWidth = 190;
            }
        });
    </script>
@endpush
