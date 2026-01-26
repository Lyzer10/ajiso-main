@extends('layouts.base')

@php
    $title = __('Reports');
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Reports') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Paralegal Reports') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title col-md-6">{{ __('Paralegal Reports') }}</h4>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-light">
                        {{ __('Paralegal activity is summarized by case, location, gender, and services as follows;') }}
                    </h6>
                    <div class="row mt-3">
                        <div class="col-md-12 mb-2">
                            <h6>
                                <b>{{ __('Total Cases') }} : </b>
                                <span class="badge text-white light-custom-color">{{ $total_cases ?? 0 }}</span>
                            </h6>
                        </div>
                        <div class="col-md-12 mb-2">
                            <h6>
                                <b>{{ __('Completed Cases') }} : </b>
                                <span class="badge text-white light-custom-color">{{ $completedCases ?? 0 }}</span>
                            </h6>
                        </div>
                        <div class="col-md-12 mb-2">
                            <h6>
                                <b>{{ __('Average Case Duration (Days)') }} : </b>
                                <span class="badge text-white light-custom-color">{{ $averageCaseDuration ?? 0 }}</span>
                            </h6>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Case Types') }}</div>
                                <div class="viz-card__body">
                                    <div id="paralegalCaseChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Services') }}</div>
                                <div class="viz-card__body">
                                    <div id="paralegalServiceChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Gender') }}</div>
                                <div class="viz-card__body">
                                    <div id="paralegalGenderChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('District') }}</div>
                                <div class="viz-card__body">
                                    <div id="paralegalDistrictChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-3">
                            <div class="viz-card">
                                <div class="viz-card__header">{{ __('Ward') }}</div>
                                <div class="viz-card__body">
                                    <div id="paralegalWardChart" class="viz-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/amcharts/4/core.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/charts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/themes/animated.js') }}"></script>

    @php
        $caseTypeData = [];
        foreach ($case_types as $case_type) {
            $caseTypeData[] = [
                'category' => __($case_type->type_of_case),
                'value' => (int) ($case_type_counts[$case_type->id] ?? 0),
            ];
        }

        $serviceData = [];
        foreach ($service_types as $service_type) {
            $serviceData[] = [
                'category' => __($service_type->type_of_service),
                'value' => (int) ($service_counts[$service_type->id] ?? 0),
            ];
        }

        $genderData = [];
        foreach ($gender_counts as $gender => $total) {
            $genderData[] = [
                'category' => __($gender),
                'value' => (int) $total,
            ];
        }

        $districtData = [];
        foreach ($districts as $district) {
            $districtData[] = [
                'category' => __($district->district),
                'value' => (int) ($district_counts[$district->id] ?? 0),
            ];
        }

        $wardData = [];
        foreach ($ward_counts as $ward) {
            $wardData[] = [
                'category' => __($ward->ward),
                'value' => (int) $ward->total,
            ];
        }
    @endphp

    <script>
        am4core.ready(function () {
            am4core.useTheme(am4themes_animated);

            function renderPieChart(target, data) {
                var chart = am4core.create(target, am4charts.PieChart);
                chart.data = data;

                var pieSeries = chart.series.push(new am4charts.PieSeries());
                pieSeries.dataFields.value = "value";
                pieSeries.dataFields.category = "category";
                pieSeries.slices.template.tooltipText = "{category}: [bold]{value}[/]";
                chart.legend = new am4charts.Legend();
            }

            function renderColumnChart(target, data) {
                var chart = am4core.create(target, am4charts.XYChart);
                chart.data = data;

                var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                categoryAxis.dataFields.category = "category";
                categoryAxis.renderer.labels.template.rotation = 315;
                categoryAxis.renderer.labels.template.horizontalCenter = "right";
                categoryAxis.renderer.labels.template.verticalCenter = "middle";
                categoryAxis.renderer.minGridDistance = 30;

                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                valueAxis.min = 0;

                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueY = "value";
                series.dataFields.categoryX = "category";
                series.columns.template.tooltipText = "{category}: [bold]{value}[/]";
                series.columns.template.fillOpacity = 0.9;

                chart.cursor = new am4charts.XYCursor();
            }

            renderPieChart("paralegalCaseChart", @json($caseTypeData));
            renderPieChart("paralegalServiceChart", @json($serviceData));
            renderPieChart("paralegalGenderChart", @json($genderData));
            renderColumnChart("paralegalDistrictChart", @json($districtData));
            renderColumnChart("paralegalWardChart", @json($wardData));
        });
    </script>
@endpush
