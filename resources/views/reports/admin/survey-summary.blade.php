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
            <li><span>{{ __('Survey Summary') }}</span></li>
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
                    <h4 class="header-title col-md-6">{{ __('Enrollment Survey Summary') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('summaries.survey.filter', app()->getLocale()) }}" method="GET" role="search">
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
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="font-weight-light">{{ __("Client feedback 'How did you hear about us?' survey summarized;") }}</h6>
                    <div class="row">
                        <div class="col-md-5 mt-3">
                            <ul class="list-group">
                                <li class="list-group-item  font-weight-bold text-white dark-custom-color">{{ __('Statistics') }}</li>
                                @if ($survey_choices->count())
                                    @foreach ($survey_choices as $survey_choice)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $survey_choice->survey_choice }}
                                            @php
                                                $gbm = '0%';
                                            @endphp
                                            @if ($group_by_survey->count())
                                                @foreach ($group_by_survey as $survey)
                                                    @if ($survey_choice->id === $survey->survey_choice_id)
                                                        @php
                                                            $gbm = floor($survey->total*100/$total).'%' ?? '0%';
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
                        <div class="col-md-7 mt-3">
                            <div id="surveyChart" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- survey summary area end -->
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

@push('scripts')
    <!-- Amcharts Resources -->
    <script src="{{ asset('plugins/amcharts/4/core.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/charts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/themes/animated.js') }}"></script>

    {{-- Survey chart  --}}
    <script>

        //Initialize data from controller and encode
        var data_arr = @php echo json_encode($data_arr) @endphp

        var date_ranges = @php echo json_encode($date_ranges ?? 'All Time') @endphp

        am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("surveyChart", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.numberFormatter.numberFormat = "#,###";

        chart.exporting.menu = new am4core.ExportMenu();
        chart.exporting.formatOptions.getKey("json").disabled = true;
        chart.exporting.formatOptions.getKey("html").disabled = true;

        chart.exporting.filePrefix = "surveyChart";

        var topContainer = chart.chartContainer.createChild(am4core.Container);
        topContainer.layout = "absolute";
        topContainer.toBack();
        topContainer.paddingBottom = 15;
        topContainer.width = am4core.percent(100);

        var axisTitle = topContainer.createChild(am4core.Label);
        axisTitle.text = "{{ __('Responses') }}";
        axisTitle.fontWeight = 600;
        axisTitle.align = "left";
        axisTitle.paddingLeft = 10;

        var summaryTitle = topContainer.createChild(am4core.Label);
        summaryTitle.text = "{{ __('Survey Summary') }}";
        summaryTitle.fontWeight = 600;
        summaryTitle.align = "center";
        axisTitle.paddingLeft = 10;

        var dateTitle = topContainer.createChild(am4core.Label);
        dateTitle.text = date_ranges;
        dateTitle.fontWeight = 600;
        dateTitle.align = "right";

        var label = chart.chartContainer.createChild(am4core.Label);
        label.text = "{{ __('Survey choices') }}";
        label.align = "center";

        chart.exporting.title = "{{ __('Survey Chart') }}";

        // Add watermark
        var watermark = chart.createChild(am4core.Label);

        $date = new Date().getFullYear();

        watermark.text = "{{ __('Copyright (C)') }} "+$date;
        watermark.disabled = true;

        // Add watermark to validated sprites
        chart.exporting.validateSprites.push(watermark);

        // Enable watermark on export
        chart.exporting.events.on("exportstarted", function(ev) {
        watermark.disabled = false;
        });

        // Disable watermark when export finishes
        chart.exporting.events.on("exportfinished", function(ev) {
        watermark.disabled = true;
        });

        // Pass data to chart
        chart.data = data_arr;

        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.dataFields.category = "choice";
        categoryAxis.renderer.minGridDistance = 40;
        categoryAxis.fontSize = 11;
        categoryAxis.renderer.labels.template.dy = 5;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.renderer.minGridDistance = 30;
        valueAxis.renderer.baseGrid.disabled = true;
        valueAxis.numberFormatter.numberFormat = "#,###";

        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.categoryX = "choice";
        series.dataFields.valueY = "frequency";
        series.columns.template.tooltipText = "{categoryX}: {valueY} {{ __('responses') }}";
        series.columns.template.tooltipY = 0;
        series.columns.template.strokeOpacity = 0;

        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        });

        }); // end am4core.ready()
        </script>
    @endpush
