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
            <li><span>{{ __('General Report') }}</span></li>
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
                    <h4 class="header-title">{{ __('General Report')}}
                        <a href="{{ route('disputes.list', app()->getLocale()) }}"
                            class="btn btn-sm text-white light-custom-color pull-right text-white">
                            {{ __('Disputes list') }}
                        </a>
                    </h4>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <form action="{{ route('reports.general.filter', app()->getLocale()) }}" method="GET" role="search">
                        <div class="row">
                        <div class="col-md-3">
                            <label for="filterBy" class="font-weight-bold">{{ __('Filter By') }}<sup class="text-danger">*</sup></label>
                            <select  id="filterBy" class="select2 form-control border-prepend-primary py-2 @error('filterBy') is-invalid @enderror" name="filterBy"
                                required autocomplete="filterBy" autofocus style="width: 100%;">
                                <option hidden disabled selected value>{{ __('Choose a filter') }}</option>
                                <option value="allFilter">{{ __('All Disputes (No filters)') }}</option>
                                <option value="legalAidProviderFilter">{{ __('Legal Aid Providers') }}</option>
                                <option value="beneficiaryFilter">{{ __('Beneficiaries') }}</option>
                                <option value="tosFilter">{{ __('Types of Services') }}</option>
                                <option value="tocFilter">{{ __('Types of Cases') }}</option>
                                <option value="statusFilter">{{ __('Dispute Statuses') }}</option>
                            </select>
                            @error('filterBy')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-3" id="allFilter">
                            <label for="all" class="font-weight-bold">{{ __('All Disputes') }}<sup class="text-danger">*</sup></label>
                            <select id="all"
                                class="select2 select2-container--default border-input-primary  @error('all') is-invalid @enderror"
                                name="all" disabled required autocomplete="all" style="width: 100%;">
                                <option hidden disabled value>{{ __('All') }}</option>
                            </select>
                            @error('all')
                                <span class="invalid-feedback" role="alert">allLegalAidProviders
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-3" id="legalAidProviderFilter">
                            <label for="legal_aid_provider" class="font-weight-bold">{{ __('Legal Aid Provider') }}<sup class="text-danger">*</sup></label>
                            <select id="legal_aid_provider" aria-describedby="selectLAP"
                                class="select2 select2-container--default border-input-primary @error('legal_aid_provider') is-invalid @enderror"
                                name="legal_aid_provider" required autocomplete="legal_aid_provider" style="width: 100%;">
                                <option hidden disabled selected value>{{ __('Choose legal aid provider') }}</option>
                                    @if ($staff->count())
                                        @foreach ($staff as $staf)
                                            <option value="{{ $staf->id }}">
                                                {{ $staf->user->first_name.' '
                                                    .$staf->user->middle_name.' '
                                                    .$staf->user->last_name.' | '
                                                    .$staf->center->location
                                                }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No legal aid provider found') }}</option>
                                    @endif
                            </select>
                            @error('legal_aid_provider')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-3" id="beneficiaryFilter">
                            <label for="beneficiary" class="font-weight-bold">{{ __('Beneficiary') }}<sup class="text-danger">*</sup></label>
                            <select id="beneficiary" aria-describedby="selectBeneficiary"
                                class="select2 select2-container--default  border-input-primary @error('beneficiary') is-invalid @enderror"
                                name="beneficiary" required autocomplete="beneficiary"  style="width: 100%;">
                                <option hidden disabled selected value>{{ __('Choose beneficiary') }}</option>
                                @if ($beneficiaries->count())
                                    @foreach ($beneficiaries as $beneficiary)
                                        <option value="{{ $beneficiary->id }}">
                                            {{ $beneficiary->user->user_no.' | '
                                                .$beneficiary->user->first_name.' '
                                                .$beneficiary->user->middle_name.' '
                                                .$beneficiary->user->last_name
                                            }}
                                        </option>
                                    @endforeach
                                @else
                                    <option>{{ __('No beneficiaries found') }}</option>
                                @endif
                            </select>
                            @error('beneficiary')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-3" id="tosFilter">
                            <label for="type_of_service" class="font-weight-bold">{{ __('Types of Services') }}<sup class="text-danger">*</sup></label>
                            <select id="type_of_service" aria-describedby="selectTos"
                                class="select2 select2-container--default border-input-primary @error('type_of_service') is-invalid @enderror"
                                name="type_of_service" required autocomplete="type_of_service" style="width: 100%;">
                                <option hidden disabled selected value>{{ __('Choose type of service') }}</option>
                                @if ($type_of_services->count())
                                    @foreach ($type_of_services as $type_of_service)
                                        <option value="{{ $type_of_service->id }}">
                                            {{ $type_of_service->type_of_service }}
                                        </option>
                                    @endforeach
                                @else
                                    <option>{{ __('No type of services found') }}</option>
                                @endif
                            </select>
                            @error('type_of_service')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-3" id="tocFilter">
                            <label for="type_of_case" class="font-weight-bold">{{ __('Type of Case') }}<sup class="text-danger">*</sup></label>
                            <select id="type_of_case" aria-describedby="selectToc"
                                class="select2 select2-container--default border-input-primary @error('type_of_case') is-invalid @enderror"
                                name="type_of_case" required autocomplete="type_of_case" style="width: 100%;">
                                <option hidden disabled selected value>{{ __('Choose type of case') }}</option>
                                @if ($type_of_cases->count())
                                    @foreach ($type_of_cases as $type_of_case)
                                        <option value="{{ $type_of_case->id }}">
                                            {{ $type_of_case->type_of_case }}
                                        </option>
                                    @endforeach
                                @else
                                    <option>{{ __('No type of case found') }}</option>
                                @endif
                            </select>
                            @error('type_of_case')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-3" id="statusFilter">
                            <label for="status" class="font-weight-bold">{{ __('Status') }}<sup class="text-danger">*</sup></label>
                            <select id="dispute_status" aria-describedby="selectStatus"
                                class="select2 select2-container--default border-input-primary @error('dispute_status') is-invalid @enderror"
                                name="dispute_status" required autocomplete="dispute_status" style="width: 100%;">
                                <option hidden disabled selected value>{{ __('Choose dispute status') }}</option>
                                @if ($dispute_statuses->count())
                                    @foreach ($dispute_statuses as $dispute_status)
                                        <option value="{{ $dispute_status->id }}">
                                            {{ $dispute_status->dispute_status }}
                                        </option>
                                    @endforeach
                                @else
                                    <option>{{ __('No dispute statuses found') }}</option>
                                @endif
                            </select>
                            @error('dispute_status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4" id="date">
                            <div class="form-group">
                                <label>{{ __('Date range') }}<sup class="text-danger">*</sup></label>
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
                @if ($disputes->count())
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <h4 class="header-title dark-custom-color p-2 w-100 text-white">
                                {{ __('Results Summary')}}
                            </h4>
                        </div>
                        <div class="col-md-4">
                            <div class="row px-3 mb-2">
                                <div class="h6">
                                    <span class="font-weight-bold">{{ __('Filter') }}</span> :
                                    @php
                                        // filter info to be displayed
                                        $filter_by = 'N/A';
                                        $filter_val = 'All';
                                    @endphp
                                    {{ __('N/A') }}
                                </div>
                            </div>
                            <div class="row px-3 mb-2">
                                <div class="h6">
                                    @php
                                        $date_raw = 'All time';
                                    @endphp
                                    <span class="font-weight-bold">{{ __('Dates') }}</span> :
                                    {{ __('All time') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row px-3 mb-2">
                                <div class="h6">
                                    <span class="font-weight-bold">{{ __('Disputes Found') }}</span>
                                    <span class="text-primary">{{ ': '.$disputes->count() }}</span>
                                </div>
                            </div>
                            <div class="row px-3 mb-2">
                                <div class="h6">
                                    <span class="font-weight-bold">{{ __('Resolved Disputes') }}</span>
                                    <span class="text-success">{{ ': '.$resolved_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dropdown">
                                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-download fa-fw"></i>
                                    {{ __('Export') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <form action="{{ route('reports.export.pdf', app()->getLocale()) }}" method="post">
                                        @csrf
                                        @foreach ($disputes as $dispute)
                                            <input type="hidden" name="dispute[]" value="{{ $dispute->id }}">
                                        @endforeach
                                        <input type="hidden" name="filter_by" value="{{ $filter_by }}">
                                        <input type="hidden" name="filter_val" value="{{ $filter_val }}">
                                        <input type="hidden" name="date_raw" value="{{ $date_raw }}">
                                        <input type="hidden" name="resolved_count" value="{{ $resolved_count }}">
                                        <button class="dropdown-item" type="submit">
                                            <i class="fas fa-file-pdf-o text-danger"></i>
                                            {{ __('as pdf') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('reports.export.excel', app()->getLocale()) }}" method="post">
                                        @csrf
                                        @foreach ($disputes as $dispute)
                                            <input type="hidden" name="dispute[]" value="{{ $dispute->id }}">
                                        @endforeach
                                        <input type="hidden" name="filter_by" value="{{ $filter_by }}">
                                        <input type="hidden" name="filter_val" value="{{ $filter_val }}">
                                        <input type="hidden" name="date_raw" value="{{ $date_raw }}">
                                        <input type="hidden" name="resolved_count" value="{{ $resolved_count }}">
                                        <button class="dropdown-item" role="button" type="submit">
                                            <i class="fas fa-file-excel text-success"></i>
                                            {{ __('as excel') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('reports.export.csv', app()->getLocale()) }}" method="post">
                                        @csrf
                                        @foreach ($disputes as $dispute)
                                            <input type="hidden" name="dispute[]" value="{{ $dispute->id }}">
                                        @endforeach
                                        <input type="hidden" name="filter_by" value="{{ $filter_by }}">
                                        <input type="hidden" name="filter_val" value="{{ $filter_val }}">
                                        <input type="hidden" name="date_raw" value="{{ $date_raw }}">
                                        <input type="hidden" name="resolved_count" value="{{ $resolved_count }}">
                                        <button class="dropdown-item" role="button" type="submit">
                                            <i class="fas fa-file-csv text-warning"></i>
                                            {{ __('as csv') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-striped">
                        <table class="table progress-table text-center" id="dataTable">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Case Type') }}</th>
                                    <th>{{ __('Beneficiary') }}</th>
                                    <th>{{ __('Legal Aid Provider') }}</th>
                                    <th>{{ __('Reported') }}</th>
                                    <th>{{ __('Dispute Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disputes as $dispute)
                                <tr>
                                    <td>{{ '#'.$dispute->id }}</td>
                                    <td>
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" class="text-secondary" title="{{  __('Click to view dispute') }}">
                                            {{ $dispute->dispute_no }}
                                        </a>
                                    </td>
                                    <td>{{ $dispute->typeOfCase->type_of_case }}</td>
                                    <td>
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                                            {{ $dispute->reportedBy->first_name.' '
                                                .$dispute->reportedBy->middle_name.' '.
                                                $dispute->reportedBy->last_name
                                            }}
                                        </a>
                                    </td>
                                    <td>
                                        @if (is_null($dispute->staff_id))
                                            <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                {{ __('Unassigned') }}
                                            </a>
                                        @else
                                            <a href="{{ route('staff.show', [app()->getLocale(), $dispute->staff_id]) }}" title="{{  __('Click to view assigned legal aid provider') }}">
                                                {{ $dispute->assignedTo->first_name.' '
                                                    .$dispute->assignedTo->middle_name.' '
                                                    .$dispute->assignedTo->last_name
                                                }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') }}</td>
                                    <td>
                                        {{-- TODO:Add a column color scheme in status table and compare here--}}
                                        <span class="
                                            @if ( $dispute->disputeStatus->dispute_status  === 'resolved')
                                                text-success
                                            @elseif ( $dispute->disputeStatus->dispute_status  === 'pending')
                                                text-warning font-italic
                                            @elseif ( $dispute->disputeStatus->dispute_status  === 'proceeding')
                                                text-primary
                                            @elseif ( $dispute->disputeStatus->dispute_status  === 'continue')
                                                text-info
                                            @elseif ( $dispute->disputeStatus->dispute_status  === 'referred')
                                                text-secondary
                                            @else
                                                text-danger
                                            @endif
                                        ">
                                        {{ $dispute->disputeStatus->dispute_status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $disputes->count() ? $disputes->links() : ''}}
                </div>
                @endif
            </div>
        </div>
        <!-- dispute list area end -->
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

    {{-- Date range picker --}}
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

    {{-- Report filters --}}
    <script>
        $(function () {

            $('#legalAidProviderFilter').prop("hidden", true);$('#legal_aid_provider').prop("disabled", true);
            $('#beneficiaryFilter').prop("hidden", true);$('#beneficiary').prop("disabled", true);
            $('#tosFilter').prop("hidden", true);$('#type_of_service').prop("disabled", true);
            $('#tocFilter').prop("hidden", true);$('#type_of_case').prop("disabled", true);
            $('#statusFilter').prop("hidden", true);$('#dispute_status').prop("disabled", true);

            $('select#filterBy').on('input', function (){

            var filter = $(this).find(":selected").val();

            if (filter === 'allFilter') {

                $('#allFilter').prop("hidden", false);
                $('#legalAidProviderFilter').prop("hidden", true);
                $('#beneficiaryFilter').prop("hidden", true);
                $('#tosFilter').prop("hidden", true);
                $('#tocFilter').prop("hidden", true);
                $('#statusFilter').prop("hidden", true);

            }else if (filter === 'legalAidProviderFilter') {

                $('#legalAidProviderFilter').prop("hidden", false);
                $('#legal_aid_provider').prop("disabled", false);
                $('#allFilter').prop("hidden", true);
                $('#beneficiaryFilter').prop("hidden", true);
                $('#tosFilter').prop("hidden", true);
                $('#tocFilter').prop("hidden", true);
                $('#statusFilter').prop("hidden", true);

            }else if (filter === 'beneficiaryFilter'){

                $('#beneficiaryFilter').prop("hidden", false);
                $('#beneficiary').prop("disabled", false);
                $('#legalAidProviderFilter').prop("hidden", true);
                $('#allFilter').prop("hidden", true);
                $('#tosFilter').prop("hidden", true);
                $('#tocFilter').prop("hidden", true);
                $('#statusFilter').prop("hidden", true);

            }if (filter === 'tosFilter') {

                $('#tosFilter').prop("hidden", false);
                $('#type_of_service').prop("disabled", false);
                $('#legalAidProviderFilter').prop("hidden", true);
                $('#beneficiaryFilter').prop("hidden", true);
                $('#allFilter').prop("hidden", true);
                $('#tocFilter').prop("hidden", true);
                $('#statusFilter').prop("hidden", true);

            }else if (filter === 'tocFilter'){

                $('#tocFilter').prop("hidden", false);
                $('#type_of_case').prop("disabled", false);
                $('#legalAidProviderFilter').prop("hidden", true);
                $('#beneficiaryFilter').prop("hidden", true);
                $('#tosFilter').prop("hidden", true);
                $('#allFilter').prop("hidden", true);
                $('#statusFilter').prop("hidden", true);

            }else if (filter === 'statusFilter'){

                $('#statusFilter').prop("hidden", false);
                $('#dispute_status').prop("disabled", false);
                $('#legalAidProviderFilter').prop("hidden", true);
                $('#beneficiaryFilter').prop("hidden", true);
                $('#tosFilter').prop("hidden", true);
                $('#tocFilter').prop("hidden", true);
                $('#allFilter').prop("hidden", true);

            }else{
                exit;
            }

            });
        });

    </script>
@endpush
