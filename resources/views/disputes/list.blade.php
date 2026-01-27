@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    @include('dates.css')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}" />
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Disputes') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Disputes List') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container">
            @include('includes.errors-statuses')
        </div>
        <!-- beneficiary list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        @canany(['isStaff'])
                            {{ __('Disputes list') }}
                             <form method="GET" action="{{ route('disputes.list', [app()->getLocale()]) }}" class="d-flex align-items-center pull-right mb-2 dispute-filter">
                                <div class="d-flex align-items-center mr-4">
                                     <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by beneficiary') }}" class="form-control form-control-sm me-2 border-prepend-black p-2">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                                </div>

                                <select name="status" class="select2 select2-container--default border-input-primary" style="width: 180px;" onchange="this.form.submit()">
                                    <option value="">{{ __('All Cases') }}</option>
                                    <option value="proceeding" {{ request('status') == 'proceeding' ? 'selected' : '' }}>
                                        {{ __('Proceeding') }}
                                    </option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                        {{ __('Resolved') }}
                                    </option>
                                </select> 
                            </form>
                        @elsecanany(['isSuperAdmin', 'isAdmin', 'isClerk'])
                            {{ __('Disputes list') }}

                            @php
                                $exportQuery = request()->only(['search', 'status', 'case_type', 'period', 'dateRange']);
                            @endphp
                            <div class="btn-group pull-right ml-3">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-download fa-fw"></i>
                                    {{ __('Export') }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('disputes.export.pdf', array_merge([app()->getLocale()], $exportQuery)) }}">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        {{ __('as pdf') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('disputes.export.excel', array_merge([app()->getLocale()], $exportQuery)) }}">
                                        <i class="fas fa-file-excel text-success"></i>
                                        {{ __('as excel') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('disputes.export.csv', array_merge([app()->getLocale()], $exportQuery)) }}">
                                        <i class="fas fa-file-csv text-warning"></i>
                                        {{ __('as csv') }}
                                    </a>
                                </div>
                            </div>
                            <a class="btn btn-sm text-white light-custom-color dropdown-toggle pull-right ml-2" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Add Dispute') }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-md-right" aria-labelledby="bd-versions">
                                <a class="dropdown-item btn-link" href="{{ route('dispute.create.new', app()->getLocale()) }}">{{ __('New') }}</a>
                                <a class="dropdown-item btn-link" href="{{ route('dispute.select.archive', app()->getLocale()) }}">{{ __('Archived') }}</a>
                            </div>

                            <form method="GET" action="{{ route('disputes.list', [app()->getLocale()]) }}" class="mt-3 dispute-filter">
                                <div class="form-row align-items-end">
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label class="font-weight-bold">{{ __('Search by beneficiary') }}</label>
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by beneficiary') }}" class="form-control border-input-primary dispute-filter__control">
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label class="font-weight-bold">{{ __('Case Type') }}</label>
                                        <select name="case_type" class="select2 select2-container--default border-input-primary dispute-filter__control" style="width: 100%;">
                                            <option value="">{{ __('All Case Types') }}</option>
                                            @if (!empty($type_of_cases) && $type_of_cases->count())
                                                @foreach ($type_of_cases as $type_of_case)
                                                    <option value="{{ $type_of_case->id }}" {{ (string) request('case_type') === (string) $type_of_case->id ? 'selected' : '' }}>
                                                        {{ __($type_of_case->type_of_case) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label class="font-weight-bold">{{ __('Dispute Status') }}</label>
                                        <select name="status" class="select2 select2-container--default border-input-primary dispute-filter__control" style="width: 100%;">
                                            <option value="">{{ __('All Statuses') }}</option>
                                            @if (!empty($dispute_statuses) && $dispute_statuses->count())
                                                @foreach ($dispute_statuses as $dispute_status)
                                                    <option value="{{ $dispute_status->id }}" {{ (string) request('status') === (string) $dispute_status->id ? 'selected' : '' }}>
                                                        {{ __($dispute_status->dispute_status) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label class="font-weight-bold">{{ __('Period') }}</label>
                                        <select name="period" id="period" class="select2 select2-container--default border-input-primary dispute-filter__control" style="width: 100%;">
                                            <option value="">{{ __('All Time') }}</option>
                                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                                            <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>{{ __('This Week') }}</option>
                                            <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                                            <option value="last_three_months" {{ request('period') === 'last_three_months' ? 'selected' : '' }}>{{ __('Last 3 Months') }}</option>
                                            <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>{{ __('This Year') }}</option>
                                            <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>{{ __('Custom Range') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-3" id="customPeriod">
                                        <label class="font-weight-bold">{{ __('Date Range') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control border-input-primary float-right dispute-filter__control" id="disputes_daterange" name="dateRange" value="{{ request('dateRange') }}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary btn-sm" id="disputes_daterange_btn">
                                                    <i class="far fa-calendar-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter fa-fw"></i>
                                        {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('disputes.list', app()->getLocale()) }}" class="btn btn-light btn-sm">{{ __('Reset') }}</a>
                                </div>
                            </form>
                        @endcanany
                    </div>
                </div>
                <div class="card-body"style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table table-striped progress-table text-center">
                            @canany(['isSuperAdmin', 'isAdmin', 'isClerk', 'isStaff'])
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('S/N') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Beneficiary') }}</th>
                                    <th>{{ __('Legal Aid Provider / Paralegal') }}</th>
                                    <th>{{ __('Reported') }}</th>
                                    <th>{{ __('Dispute Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if ($disputes->count())
                                @foreach ($disputes as $dispute)
                                <tr>
                                    <td>{{ $disputes->firstItem() + $loop->index }}</td>
                                    <td>
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" class="text-secondary" title="{{ __('Click to view dispute') }}">
                                            {{ $dispute->dispute_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                                            {{ $dispute->reportedBy->first_name.' '
                                                .$dispute->reportedBy->middle_name.' '.
                                                $dispute->reportedBy->last_name
                                            }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $paralegalUser = $dispute->paralegalUser;
                                            $assignedUser = $paralegalUser ?: $dispute->assignedTo;
                                        @endphp
                                        @if (is_null($dispute->staff_id) && is_null($dispute->paralegal_user_id))
                                            @canany(['isAdmin', 'isSuperAdmin'])
                                                <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                {{ __('Unassigned') }}
                                                </a>
                                            @else
                                                <span class="text-danger">{{ __('Unassigned') }}</span>
                                            @endcanany
                                        @else
                                            @if ($paralegalUser)
                                                @canany(['isAdmin', 'isSuperAdmin'])
                                                    <a href="{{ route('user.show', [app()->getLocale(), $paralegalUser->id]) }}" title="{{ __('Click to view paralegal') }}">
                                                        {{ __('Paralegal') }}: {{ $paralegalUser->first_name.' '
                                                            .$paralegalUser->middle_name.' '
                                                            .$paralegalUser->last_name
                                                        }}
                                                    </a>
                                                @else
                                                    <span class="text-dark">
                                                        {{ __('Paralegal') }}: {{ $paralegalUser->first_name.' '
                                                            .$paralegalUser->middle_name.' '
                                                            .$paralegalUser->last_name
                                                        }}
                                                    </span>
                                                @endcanany
                                            @else
                                                @canany(['isAdmin', 'isSuperAdmin'])
                                                <a href="{{ route('staff.show', [app()->getLocale(), $dispute->staff_id, ]) }}" title="{{  __('Click to view assigned legal aid provider') }}">
                                                    {{ $dispute->assignedTo->first_name.' '
                                                        .$dispute->assignedTo->middle_name.' '
                                                        .$dispute->assignedTo->last_name
                                                    }}
                                                </a>
                                                @else
                                                    <span class="text-dark">
                                                        {{ $dispute->assignedTo->first_name.' '
                                                            .$dispute->assignedTo->middle_name.' '
                                                            .$dispute->assignedTo->last_name
                                                        }}
                                                    </span>
                                                @endcanany
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($dispute->reported_on)->diffForHumans() }}</td>
                                    <td>
                                        @php
                                            $statusSlug = \Illuminate\Support\Str::slug($dispute->disputeStatus->dispute_status);
                                        @endphp
                                        <span class="badge-status status-{{ $statusSlug }}">
                                            {{ __($dispute->disputeStatus->dispute_status) }}
                                        </span>
                                    </td>
                                    <td class="d-flex justify-content-between">
                                        <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" title="{{ __('Edit Dispute') }}">
                                            <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                        </a> /
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" title="{{ __('View Dispute') }}">
                                            <i class="fas fa-eye fa-fw text-success"></i>
                                        </a>
                                        @can(['isSuperAdmin'])
                                            /
                                            <form method="POST" action="{{ route('dispute.trash', [app()->getLocale(), $dispute->id]) }}">
                                                @csrf
                                                @METHOD('PUT')
                                                    <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Dispute"') }}"></i>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="p-1">{{ __('No disputes found') }}</td>
                            </tr>
                            @endif
                            </tbody>
                            @endcanany
                        </table>
                    </div>
                    {{ $disputes->count() ? $disputes->links() : ''}}
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection

@push('scripts')
    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')
    @include('dates.js')

    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $('.select2').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>

    <script>
        $(function () {
            function toggleDateRange() {
                if (!$('#period').length) {
                    return;
                }
                var period = $('#period').val();
                var isCustom = period === 'custom';
                $('#customPeriod').toggle(isCustom);
                $('#disputes_daterange').prop('disabled', !isCustom);
                $('#disputes_daterange_btn').prop('disabled', !isCustom);
            }

            toggleDateRange();
            $('#period').on('change', toggleDateRange);

            if ($("#disputes_daterange").length) {
                $("#disputes_daterange").daterangepicker(
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
                                moment().subtract(1, "month").endOf("month")
                            ],
                        },
                        startDate: moment().subtract(29, "days"),
                        endDate: moment(),
                    },
                    function (start, end) {
                        $("#disputes_daterange").val(
                            start.format("MM/DD/YYYY") + " - " + end.format("MM/DD/YYYY")
                        );
                    }
                );

                $("#disputes_daterange_btn").on("click", function () {
                    var picker = $("#disputes_daterange").data("daterangepicker");
                    if (!picker) {
                        return;
                    }
                    if (picker.isShowing) {
                        picker.hide();
                    } else {
                        picker.show();
                    }
                });

                $("#disputes_daterange").on("apply.daterangepicker cancel.daterangepicker", function (ev, picker) {
                    picker.hide();
                });
            }

            var searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    if (this.value === "") {
                        this.form.submit();
                    }
                });
            }
        });
    </script>

@endpush
