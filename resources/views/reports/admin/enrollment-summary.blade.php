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
                                    <div id="genderChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Age Group') }}</div>
                                <div class="viz-card__body">
                                    <div id="ageGroupChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Monthly Income') }}</div>
                                <div class="viz-card__body">
                                    <div id="incomeChart" class="viz-chart"></div>
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
                                    <div id="employmentChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Education Level') }}</div>
                                <div class="viz-card__body">
                                    <div id="educationChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Marital Status') }}</div>
                                <div class="viz-card__body">
                                    <div id="maritalChart" class="viz-chart"></div>
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

    <!-- AmCharts Resources -->
    <script src="{{ asset('plugins/amcharts/4/core.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/charts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/themes/animated.js') }}"></script>

    @php
        $genderData = [];
        foreach ($group_by_gender as $gender) {
            $percent = $total > 0 ? floor($gender->total * 100 / $total) : 0;
            $genderData[] = ['category' => __($gender->gender), 'value' => $percent];
        }

        $ageData = [];
        foreach ($age_groups as $age_group) {
            $percent = 0;
            foreach ($group_by_age as $age) {
                if ($age_group->id === $age->age) {
                    $percent = $total > 0 ? floor($age->total * 100 / $total) : 0;
                    break;
                }
            }
            $ageData[] = ['category' => __($age_group->age_group), 'value' => $percent];
        }

        $incomeData = [];
        foreach ($income_groups as $income_group) {
            $percent = 0;
            foreach ($group_by_income as $income) {
                if ($income_group->id === $income->income_id) {
                    $percent = $total > 0 ? floor($income->total * 100 / $total) : 0;
                    break;
                }
            }
            $incomeData[] = ['category' => __($income_group->income), 'value' => $percent];
        }

        $employmentData = [];
        foreach ($employment_statuses as $employment_status) {
            $percent = 0;
            foreach ($group_by_occupation as $occupation) {
                if ($employment_status->id === $occupation->employment_status_id) {
                    $percent = $total > 0 ? floor($occupation->total * 100 / $total) : 0;
                    break;
                }
            }
            $employmentData[] = ['category' => __($employment_status->employment_status), 'value' => $percent];
        }

        $educationData = [];
        foreach ($education_levels as $education_level) {
            $percent = 0;
            foreach ($group_by_education as $education) {
                if ($education_level->id === $education->education_level_id) {
                    $percent = $total > 0 ? floor($education->total * 100 / $total) : 0;
                    break;
                }
            }
            $educationData[] = ['category' => __($education_level->education_level), 'value' => $percent];
        }

        $maritalData = [];
        foreach ($marital_statuses as $marital_status) {
            $percent = 0;
            foreach ($group_by_marital as $marital) {
                if ($marital_status->id === $marital->marital_status_id) {
                    $percent = $total > 0 ? floor($marital->total * 100 / $total) : 0;
                    break;
                }
            }
            $maritalData[] = ['category' => __($marital_status->marital_status), 'value' => $percent];
        }
    @endphp

    <script>
        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);

            function buildBarChart(containerId, data, prefix) {
                var chart = am4core.create(containerId, am4charts.XYChart);
                chart.logo.disabled = true;
                chart.hiddenState.properties.opacity = 0;
                chart.data = data;
                chart.numberFormatter.numberFormat = "#";
                chart.fontFamily = "Arial, sans-serif";
                chart.fontSize = 12;

                var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "category";
                categoryAxis.renderer.inversed = true;
                categoryAxis.renderer.grid.template.disabled = true;
                categoryAxis.renderer.minGridDistance = 12;
                categoryAxis.fontSize = 12;

                var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
                valueAxis.min = 0;
                valueAxis.max = 100;
                valueAxis.strictMinMax = true;
                valueAxis.renderer.grid.template.strokeOpacity = 0.1;
                valueAxis.renderer.labels.template.fontSize = 11;
                valueAxis.numberFormatter.numberFormat = "#";
                valueAxis.renderer.labels.template.adapter.add("text", function (text) {
                    return text + "%";
                });
                valueAxis.title.text = "{{ __('Percent') }}";

                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueX = "value";
                series.dataFields.categoryY = "category";
                series.columns.template.strokeOpacity = 0;
                series.columns.template.tooltipText = "{category}: {value}% {{ __('of beneficiaries') }}";
                series.columns.template.height = am4core.percent(60);
                series.columns.template.fill = am4core.color("#0099ff");
                series.columns.template.cornerRadiusTopRight = 8;
                series.columns.template.cornerRadiusBottomRight = 8;

                var labelBullet = series.bullets.push(new am4charts.LabelBullet());
                labelBullet.label.text = "{value}%";
                labelBullet.label.horizontalCenter = "left";
                labelBullet.label.dx = 10;
                labelBullet.label.fontSize = 11;
                labelBullet.label.fill = am4core.color("#0f172a");
                labelBullet.label.adapter.add("text", function (text, target) {
                    if (!target.dataItem) {
                        return "";
                    }
                    var value = target.dataItem.valueX;
                    if (!value) {
                        return "";
                    }
                    return text;
                });

                chart.exporting.enabled = false;

                return chart;
            }

            buildBarChart("genderChart", @json($genderData), "beneficiary-gender");
            buildBarChart("ageGroupChart", @json($ageData), "beneficiary-age-group");
            buildBarChart("incomeChart", @json($incomeData), "beneficiary-income");
            buildBarChart("employmentChart", @json($employmentData), "beneficiary-employment");
            buildBarChart("educationChart", @json($educationData), "beneficiary-education");
            buildBarChart("maritalChart", @json($maritalData), "beneficiary-marital");
        });
    </script>
@endpush
