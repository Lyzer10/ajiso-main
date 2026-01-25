@extends('layouts.base')

@php
    $title = __('Dashboard') 
@endphp
@section('title', 'LAIS | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Dashboard') }}</h4>
        <ul class="breadcrumbs pull-left">
            <li><a href="{{ route('admin.home', app()->getLocale())}}" >{{ __('Home') }}</a></li>
            <li><span>{{ __('Admin') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-10">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-check-circle-o"></i></strong> {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span class="fa fa-times"></span>
                        </button>
                    </div>
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <!-- seo fact area start -->
        <div class="col-lg-12">
            <div class="row">
                @can('isParalegal')
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg1">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-balance-scale"></i>{{ __('Cases') }}</div>
                                    <h2>{{ $total_disputes ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer bg-dark-blue text-center">
                                    <a href="{{ route('disputes.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg4">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-hourglass-half"></i>{{ __('Pending Cases') }}</div>
                                    <h2>{{ $disputes_pending ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer bg-dark-yellow text-center">
                                    <a href="{{ route('disputes.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg3">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-check-circle"></i>{{ __('Resolved Cases') }}</div>
                                    <h2>{{ $disputes_resolved ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer  text-center">
                                    <a href="{{ route('disputes.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg2">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-users"></i>{{ __('Beneficiaries') }}</div>
                                    <h2>{{ $total_beneficiaries ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer bg-dark-green text-center">
                                    <a href="{{ route('beneficiaries.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg1">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-balance-scale"></i>{{ __('Cases') }}</div>
                                    <h2>{{ $total_disputes ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer bg-dark-blue text-center">
                                    <a href="{{ route('disputes.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg2">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon"><i class="fas fa-users"></i>{{ __('Beneficiaries') }}</div>
                                    <h2>{{ $total_beneficiaries ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer bg-dark-green text-center">
                                    <a href="{{ route('beneficiaries.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-md-5 mb-3">
                        <div class="card">
                            <div class="seo-fact sbg4">
                                <div class="p-4 d-flex justify-content-between align-items-center">
                                    <div class="seofct-icon">
                                        <i class="fas fa-user-shield"></i>
                                        {{ __('Legal Aid Providers') }}
                                    </div>
                                    <h2>{{ $total_staff ?? '0' }}</h2>
                                </div>
                                <canvas id="" height="20"></canvas>
                                <div class="card-footer bg-dark-yellow text-center">
                                    <a href="{{ route('staff.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                        {{ __('More info') }}
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @canany(['isSuperAdmin','isAdmin'])
                        <div class="col-md-3 mt-md-5 mb-3">
                            <div class="card">
                                <div class="seo-fact sbg3">
                                    <div class="p-4 d-flex justify-content-between align-items-center">
                                        <div class="seofct-icon">
                                            <i class="fas fa-user-friends"></i>
                                            {{ __('Paralegals') }}
                                        </div>
                                        <h2>{{ $total_paralegals ?? '0' }}</h2>
                                    </div>
                                    <canvas id="" height="20"></canvas>
                                    <div class="card-footer text-center">
                                        <a href="{{ route('paralegals.list', app()->getLocale()) }}" class="small-box-footer text-white">
                                            {{ __('More info') }}
                                            <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanany
                @endcan
            </div>
        </div>
        <!-- seo fact area end -->
        <!-- Case Types area start -->
        <div class="col-lg-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">{{ __('Cases Reported') }}</h4>
                    <div id="casesChart" style="width: 100%; height: 278px;"></div>
                </div>
            </div>
        </div>
        <!-- Case Types area end -->
        <!-- Despute Statuses area start -->
        <div class=" col-lg-4 mt-5">
            <div class="card h-full">
                <div class="card-body">
                    <h4 class="header-title">{{ __('Services Offered')}}</h4>
                    <div id="servicesChart" style="width: 90%; height: 278px;"></div>
                </div>
            </div>
        </div>
        <!-- Despute Statuses area end -->
        <!-- Services Provision start -->
        <div class="col-xl-12 col-ml-11 col-lg-11 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">{{ __('Dispute Status')}}</h4>
                    <div id="disputeStatusChart" style="width: 100%; height: 278px;"></div>
                </div>
            </div>
        </div>
        <!-- Services Provision end -->
        <!-- performance gauge area start -->
        @cannot('isClerk')
            <div class=" col-xl-12 col-ml-11 col-lg-11 mt-5">
                <div class="card h-full">
                    <div class="card-body">
                        <h4 class="header-title">{{ __('Performance Gauge')}}</h4>
                        <div class="text-center">
                            <div id="performanceChart" style="width: 100%; height: 278px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endcannot
        <!-- performance gauge area end -->
    </div>
@endsection

@push('scripts')
    <!-- Amcharts Resources -->
    <script src="{{ asset('plugins/amcharts/4/core.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/charts.js') }}"></script>
    <script src="{{ asset('plugins/amcharts/4/themes/animated.js') }}"></script>

    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>


    {{-- Graphs --}}

    {{-- Case Types --}}
    <script>

        //Initialize data from controller and encode
        var toc_data = @php echo json_encode($toc_data ?? '0') @endphp

        am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("casesChart", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.numberFormatter.numberFormat = "#,###";

            chart.exporting.menu = new am4core.ExportMenu();
            chart.exporting.formatOptions.getKey("json").disabled = true;
            chart.exporting.formatOptions.getKey("html").disabled = true;

            chart.exporting.filePrefix = "caseshart";

        // Add data
        chart.data = toc_data;

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "case";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 10;
        categoryAxis.renderer.labels.template.horizontalCenter = "right";
        categoryAxis.renderer.labels.template.verticalCenter = "middle";
        categoryAxis.renderer.labels.template.rotation = 325;
        categoryAxis.tooltip.disabled = true;
        categoryAxis.renderer.minHeight = 110;
        categoryAxis.renderer.grid.template.strokeOpacity = 0.1;
        categoryAxis.renderer.cellStartLocation = 0.1;
        categoryAxis.renderer.cellEndLocation = 0.9;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.minWidth = 50;
        valueAxis.title.text = "{{ __('Cases') }}";
        valueAxis.numberFormatter.numberFormat = "#,###";
        valueAxis.renderer.grid.template.strokeOpacity = 0.15;

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.sequencedInterpolation = true;
        series.dataFields.valueY = "frequency";
        series.dataFields.categoryX = "case";
        series.tooltipText = "[{categoryX}: bold]{valueY} {{ __('cases') }}[/]";
        series.columns.template.strokeWidth = 0;
        series.columns.template.width = am4core.percent(40);
        series.columns.template.maxWidth = 26;
        series.columns.template.column.cornerRadiusTopLeft = 6;
        series.columns.template.column.cornerRadiusTopRight = 6;

        series.tooltip.pointerOrientation = "vertical";


        // on hover, make corner radiuses bigger
        var hoverState = series.columns.template.column.states.create("hover");
        hoverState.properties.cornerRadiusTopLeft = 0;
        hoverState.properties.cornerRadiusTopRight = 0;
        hoverState.properties.fillOpacity = 1;

        series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        });

        // Cursor
        chart.cursor = new am4charts.XYCursor();

        }); // end am4core.ready()
    </script>

    {{-- Service Types --}}
    <script>

        //Initialize data from controller and encode
        var tos_data = @php echo json_encode($tos_data ?? '0') @endphp

        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("servicesChart", am4charts.PieChart);
        chart.logo.disabled = true;
        chart.numberFormatter.numberFormat = "#,###";

        chart.exporting.menu = new am4core.ExportMenu();
        chart.exporting.formatOptions.getKey("json").disabled = true;
        chart.exporting.formatOptions.getKey("html").disabled = true;

        chart.exporting.filePrefix = "servicesChart";

        // Add data
        chart.data = tos_data;

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "frequency";
        pieSeries.dataFields.category = "service";
        pieSeries.slices.template.tooltipText = "{category}: {value.value} {{ __('services') }} ({value.percent.formatNumber('#.0')}%)";
        pieSeries.labels.template.disabled = true;

        chart.radius = am4core.percent(95);

        // Create custom legend
        chart.events.on("ready", function(event) {
            // populate our custom legend when chart renders
            chart.customLegend = document.getElementById('legend');
            pieSeries.dataItems.each(function(row, i) {
                var color = chart.colors.getIndex(i);
                var percent = Math.round(row.values.value.percent * 100) / 100;
                var value = row.value;
            });
        });

        function toggleSlice(item) {
            var slice = pieSeries.dataItems.getIndex(item);
            if (slice.visible) {
                slice.hide();
            }
            else {
                slice.show();
            }
        }

        function hoverSlice(item) {
            var slice = pieSeries.slices.getIndex(item);
            slice.isHover = true;
        }

        function blurSlice(item) {
            var slice = pieSeries.slices.getIndex(item);
            slice.isHover = false;
        }
    </script>

    {{-- Dispute Statuses --}}
    <script>

        //Initialize data from controller and encode
        var dis_data = @php echo json_encode($dis_data ?? '0') @endphp

        am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("disputeStatusChart", am4charts.PieChart);
        chart.logo.disabled = true;
        chart.numberFormatter.numberFormat = "#,###";

        chart.exporting.menu = new am4core.ExportMenu();
        chart.exporting.formatOptions.getKey("json").disabled = true;
        chart.exporting.formatOptions.getKey("html").disabled = true;

        chart.exporting.filePrefix = "disputeStatusChart";

        // Add data
        chart.data = dis_data;

        // Set inner radius
        chart.innerRadius = am4core.percent(50);

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "frequency";
        pieSeries.dataFields.category = "status";
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;
        pieSeries.slices.template.tooltipText = "{category}: {value.value} {{ __('cases') }} ({value.percent.formatNumber('#.0')}%)";

        // This creates initial animation
        pieSeries.hiddenState.properties.opacity = 1;
        pieSeries.hiddenState.properties.endAngle = -90;
        pieSeries.hiddenState.properties.startAngle = -90;

        }); // end am4core.ready()
    </script>

    {{-- Performance --}}
    <script>

        //Initialize data from controller and encode
        var performance = @php echo json_encode($performance ?? '0') @endphp

        am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // create chart
        var chart = am4core.create("performanceChart", am4charts.GaugeChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this makes initial fade in effect
        chart.numberFormatter.numberFormat = "#,###";

        var title = chart.titles.create();
        title.text = {{ __('performance.title') }};
        title.fontSize = 16;
        title.marginBottom = 20;

        var label = chart.createChild(am4core.Label);
        var milestoneValue = performance.milestone ?? 100;
        var performanceValue = performance.value ?? 0;
        label.text = "{{ __('Value / Milestone') }}: " + performanceValue + " / " + milestoneValue;
        label.fontSize = 14;
        label.align = "center";
        label.marginTop = 10;

        chart.innerRadius = -25;

        chart.exporting.menu = new am4core.ExportMenu();

        chart.exporting.formatOptions.getKey("json").disabled = true;
        chart.exporting.formatOptions.getKey("html").disabled = true;

        chart.exporting.filePrefix = "disputeStatusChart";

        var axis = chart.xAxes.push(new am4charts.ValueAxis());
        axis.min = 0;
        axis.max = milestoneValue;
        axis.strictMinMax = true;

        var colorSet = new am4core.ColorSet();

        var gradient = new am4core.LinearGradient();
        gradient.stops.push({color:am4core.color("red")})
        gradient.stops.push({color:am4core.color("yellow")})
        gradient.stops.push({color:am4core.color("green")})

        axis.renderer.line.stroke = gradient;
        axis.renderer.line.strokeWidth = 15;
        axis.renderer.line.strokeOpacity = 1;

        axis.renderer.grid.template.disabled = true;

        var hand = chart.hands.push(new am4charts.ClockHand());
        hand.radius = am4core.percent(97);

        setInterval(function() {
            hand.showValue(performance.value, 1000, am4core.ease.cubicOut);
        }, 2000);


        }); // end am4core.ready()

    </script>
    @endpush
