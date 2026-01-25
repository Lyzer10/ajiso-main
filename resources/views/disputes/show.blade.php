@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'LAIS | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@push('styles')
    <style>
        .attachment-file-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .attachment-row {
            gap: 0.5rem;
        }
        .attachment-meta {
            min-width: 0;
        }
        .attachment-actions {
            white-space: nowrap;
        }
    </style>
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
            <li><span>{{ __('Dispute Profile') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- View Dispute area start -->
        <div class="col-12">
            <div class="card mt-5">
                @php
                    $currentStatus = optional($dispute->disputeStatus)->dispute_status ?? 'pending';
                    $statusSlug = \Illuminate\Support\Str::slug($currentStatus);
                @endphp
                <div class="card-header">
                    <div class="dispute-profile-header">
                        <h4 class="header-title mb-0">{{ __('Dispute Profile') }}</h4>
                        <div class="dispute-profile-actions">
                            @if (strtolower($currentStatus) === 'resolved' && !empty($continueStatusId))
                                <button type="button" class="btn btn-sm btn-info text-white mr-2" id="reopenCase"
                                    data-status-id="{{ $continueStatusId }}">
                                    {{ __('Reopen Case') }}
                                </button>
                            @endif
                            @if (!empty($canRequestReassignment))
                                <button type="button" class="btn btn-sm btn-secondary text-white mr-2"
                                    data-toggle="modal" data-target="#reassignmentRequestModal">
                                    @if ($isAdminUser)
                                        {{ __('Reassign Case') }}
                                    @elseif ($isParalegalUser)
                                        {{ __('Request Legal Aid Provider') }}
                                    @else
                                        {{ __('Request Legal Aid Assistance') }}
                                    @endif
                                </button>
                            @endif
                            @if ($isParalegalUser)
                                <a href="{{ route('disputes.request.my-paralegal-list', app()->getLocale()) }}"
                                    class="btn btn-sm btn-outline-secondary mr-2">
                                    {{ __('My Requests') }}
                                </a>
                            @endif
                            <a href="{{ route('disputes.list', app()->getLocale()) }}"
                                class="btn btn-sm btn-primary text-white">{{ __('Disputes list') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($dispute->count())
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card dispute-card dispute-info-card mb-3">
                                <div class="dispute-card-header">
                                    <div class="dispute-card-title">{{ __('Dispute Info') }}</div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $date = Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') ?? 'N/A'
                                    @endphp
                                    <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="disputeNo" class="font-weight-bold">{{ __('Dispute No') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="dispute">#</span>
                                                    </div>
                                                    <input type="text" class="form-control border-append-primary" id="disputeNo"
                                                        value="{{ $dispute->dispute_no }}" name="dispute_no" readonly aria-describedby="dispute">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="date" class="font-weight-bold">{{ __('Date Reported') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="date" value="{{ $date }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="problem_description" class="font-weight-bold">{{ __('Problem Description') }}</label>
                                                <textarea class="form-control border-text-primary" readonly
                                                    name="problem_description" required style="width: 100%;">{{ $dispute->problem_description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="beneficiary" class="font-weight-bold">{{ __('Beneficiary') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="beneficiary"
                                                    value="{{ $dispute->reportedBy->user_no.' | '
                                                    .$dispute->ReportedBy->designation->name.' '
                                                    .$dispute->reportedBy->first_name.' '
                                                    .$dispute->reportedBy->middle_name.' '
                                                    .$dispute->reportedBy->last_name
                                                }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="staff" class="font-weight-bold">{{ __('Legal Aid Provider') }}</label>
                                                <input type="text" readonly
                                                    class="form-control  border-input-primary" id="staff"
                                                    value="@if (is_null($dispute->staff_id)){{ __('Unassigned') }}
                                                            @else{{
                                                                $dispute->assignedTo->designation->name.' '
                                                                .$dispute->assignedTo->first_name.' '
                                                                .$dispute->assignedTo->middle_name.' '
                                                                .$dispute->assignedTo->last_name.' | '
                                                                .$dispute->staff->center->location
                                                            }}@endif">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="type_of_service" class="font-weight-bold">{{ __('Type of Service') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="type_of_service" value="{{ $dispute->typeOfService->type_of_service }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="type_of_case" class="font-weight-bold">{{ __('Type of Case') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="type_of_case" value="{{ $dispute->typeOfCase->type_of_case }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="where_reported" class="font-weight-bold">{{ __('Where did you report your problem?') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="where_reported" value="{{ $dispute->where_reported }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="how_did_they_help" class="font-weight-bold">{{ __('How did they help you?') }}</label>
                                                <textarea class="form-control border-text-primary" readonly
                                                name="how_did_they_help" id="how_did_they_help" style="width: 100%;">{{ $dispute->how_did_they_help }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="service_experience" class="font-weight-bold">{{ __('Did you experience any inconvinience?') }}</label>
                                                <textarea class="form-control border-text-primary" readonly
                                                    name="service_experience" id="service_experience" style="width: 100%;">{{ $dispute->service_experience }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="matter_to_court" class="font-weight-bold">{{ __('Have you taken the matter to court?') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="matter_to_court" value="{{ __($dispute->matter_to_court) }}">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="how_can_we_help" class="font-weight-bold">{{ __('What do you want help with?') }}</label>
                                                <textarea class="form-control border-text-primary" readonly
                                                    name="how_can_we_help" id="how_can_we_help" style="width: 100%;">{{ $dispute->how_can_we_help }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="defendant_names_addr" class="font-weight-bold">{{ __('Names of people who will be monitored or prosecuted, and addresses') }}</label>
                                                <textarea class="form-control border-text-primary" readonly
                                                    name="defendant_names_addr" id="defendant_names_addr" style="width: 100%;">{{ $dispute->defendant_names_addr }}</textarea>
                                            </div>
                                        </div>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" class=" btn btn-primary float-right">{{ __('Update Details') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card dispute-card mb-3">
                                <div class="dispute-card-header">
                                    <div>
                                        <div class="dispute-card-title">{{ __('Dispute History') }}</div>
                                        <div class="dispute-card-meta">
                                            {{ __('Reported') }} {{ $occurrences->count().'x' }}
                                        </div>
                                    </div>
                                    <span class="badge-status status-{{ $statusSlug }} dispute-status-pill">
                                        {{ __($currentStatus) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="single-table">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center">
                                                <thead class="text-capitalize">
                                                    <tr>
                                                        <th scope="col">{{ __('Id') }}</th>
                                                        <th scope="col">{{ __('Date Reported') }}</th>
                                                        <th scope="col">{{ __('Status') }}</th>
                                                        <th scope="col">{{ __('Legal Aid Provider') }}</th>
                                                    </tr>
                                                </thead>
                                                @if ($occurrences->count())
                                                <tbody>
                                                    @foreach ($occurrences as $occurrence)
                                                        <tr>
                                                            <td scope="row">
                                                                <a href="{{ route('dispute.show', [app()->getLocale(), $occurrence->id]) }}" title="{{ __('View Dispute') }}">
                                                                    {{ '#'.$occurrence->id }}
                                                                </a>
                                                            </td>
                                                            <td>{{ Carbon\Carbon::parse($occurrence->reported_on)->format('d-m-Y') }}</td>
                                                            <td>
                                                                @php
                                                                    $historyStatusSlug = \Illuminate\Support\Str::slug($occurrence->disputeStatus->dispute_status);
                                                                @endphp
                                                                <span class="badge-status status-{{ $historyStatusSlug }}">
                                                                    {{ __($occurrence->disputeStatus->dispute_status) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if (is_null($occurrence->staff_id))
                                                                    @canany(['isSuperAdmin', 'isAdmin'])
                                                                        <a href="{{ route('dispute.assign', [app()->getLocale(), $occurrence->id]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                                            {{ __('Unassigned') }}
                                                                        </a>
                                                                    @elsecanany(['isClerk', 'isStaff'])
                                                                    <a class="text-danger">
                                                                        {{ __('Unassigned') }}
                                                                    </a>
                                                                    @endcanany
                                                                @else
                                                                    <a href="{{ route('staff.show', [app()->getLocale(), $occurrence->staff_id]) }}" title="{{  __('Click to view assigned legal aid provider') }}">
                                                                        {{ $occurrence->assignedTo->first_name.' '
                                                                            .$occurrence->assignedTo->middle_name.' '
                                                                            .$occurrence->assignedTo->last_name
                                                                        }}
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                @else
                                                <tbody>
                                                    <tr>
                                                        <td class="p-1" colspan="4">
                                                            {{ __('No dispute occurrrences found') }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card dispute-card mb-3">
                                <div class="dispute-card-header">
                                    <div class="dispute-card-title">{{ __('LAAC Clinic Visits') }}</div>
                                    <span class="badge badge-success">
                                        {{  ($dispute->counselingSheets->count()) ??  0 }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="alert-items">
                                        @if ( $dispute->counselingSheets->count())
                                            @foreach ($dispute->counselingSheets as $sheet)
                                                <div class="alert alert-primary lead" role="alert">
                                                    <strong>{{ '#'.$sheet->id }}</strong> | 
                                                    {{ Carbon\Carbon::parse($sheet->attended_at)->format('d-m-Y') }} | 
                                                    @php
                                                        $time_in = Carbon\Carbon::parse($sheet->time_in);
                                                        $time_out = Carbon\Carbon::parse($sheet->time_out);

                                                        $duration = $time_out->diff($time_in)->format('%H:%I');
                                                    @endphp
                                                    {{ $duration.' Hrs' }} | 
                                                    <span @if ((bool) $sheet->is_open == true) class="text-success" @else class="text-secondary" @endif>
                                                        {{ (bool) $sheet->is_open == true ? 'Open' : __('Closed') }}
                                                    </span> | 
                                                    <a href="{{ route('disputes.activity.sheet', [app()->getLocale(), $sheet->id]) }}">
                                                        {{ __('View More') }}
                                                    </a>
                                                </div>
                                            @endforeach
                                            @else
                                            <div class="alert alert-primary lead" role="alert">
                                                <strong><i class="fas fa-exclamation-triangle text-danger"></i></strong> 
                                                {{ __('LAAC Clinic Visits Not Found.') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card dispute-card mb-3">
                                <div class="dispute-card-header">
                                    <div class="dispute-card-title">{{ __('Dispute Progress') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="recent-activity">
                                        @if ( $dispute->activities->count())
                                            @foreach ($dispute->activities as $activity)
                                                <div class="timeline-task">
                                                    @if ($activity->dispute_activity == 'Dispute Reported')
                                                        <div class="icon bg-primary">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                    @elseif ($activity->dispute_activity == 'Dispute Assigned')
                                                        <div class="icon bg-warning">
                                                            <i class="fas fa-hand-point-right"></i>
                                                        </div>
                                                    @elseif ($activity->dispute_activity == 'Dispute Referred')
                                                        <div class="icon bg-secondary">
                                                            <i class="fas fa-angle-double-right"></i>
                                                        </div>
                                                    @elseif ($activity->dispute_activity == 'Dispute Resolved')
                                                        <div class="icon bg-sucess">
                                                            <i class="fas fa-check-double"></i>
                                                        </div>
                                                    @elseif ($activity->dispute_activity == 'Dispute Discontinued')
                                                        <div class="icon bg-danger">
                                                            <i class="fas fa-times"></i>
                                                        </div>
                                                    @else
                                                        <div class="icon bg-primary">
                                                            <i class="fas fa-info"></i>
                                                        </div>
                                                    @endif
                                                    <div class="tm-title">
                                                        <h4>
                                                            {{ __($activity->dispute_activity) }}
                                                            @if ($activity->activity_type === 'attachment' && $activity->description)
                                                                : {{ Str::limit($activity->description, 40) }}
                                                            @endif
                                                        </h4>
                                                        <span class="time">
                                                            <i class="fas fa-calendar-week"></i>
                                                            {{ Carbon\Carbon::parse($activity->created_at)->format('d/m/Y') }}
                                                        </span>
                                                    </div>
                                                    @if ($activity->description && $activity->activity_type !== 'attachment')
                                                        <p>
                                                            <i class="fas fa-caret-right"></i>
                                                            {{ $activity->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card dispute-card">
                                <div class="dispute-card-header">
                                    <div class="dispute-card-title">{{ __('Dispute Progress Center') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="dispute-action-list">
                                        <a href="javascript:void(0)" class="dispute-action-item" data-toggle="modal" data-target="#sendNoticeModal">
                                            <i class="fas fa-paper-plane fa-fw text-info"></i>
                                            {{ __('Send Notification to Beneficiary') }}
                                        </a>
                                        <a href="javascript:void(0)" class="dispute-action-item" data-toggle="modal" data-target="#clinicProgressModal">
                                            <i class="fas fa-comment-medical fa-fw text-primary"></i>
                                            {{ __('Legal Aid Advice Counseling Clinic') }}
                                        </a>
                                        <a href="javascript:void(0)" class="dispute-action-item" data-toggle="modal" data-target="#providerRemarksModal">
                                            <i class="fas fa-marker fa-fw text-secondary"></i>
                                            {{ __('Legal Aid Provider Remarks') }}
                                        </a>
                                        @cannot('isClerk')
                                            <a href="javascript:void(0)" class="dispute-action-item" data-toggle="modal" data-target="#generateLetterModal">
                                                <i class="fas fa-file-alt fa-fw text-primary"></i>
                                                {{ __('Generate Letter (WITO / Referral / Feedback)') }}
                                            </a>
                                        @endcannot
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="container">
                        {{ __('No information found on this dispute, please try again.') }}
                    </div>
                    @endif
                </div>
                <!-- View Dispute area end -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    @include('dates.js')
    <script src="{{ asset('plugins/moment/locale/sw.js') }}"></script>

    <script src="{{ asset('assets/js/letter-print.js') }}"></script>

    {{-- Select2 --}}
    <script type="text/javascript">
        $(function() {
            $('.select2').select2();
        });
    </script>

    {{-- Date Picker --}}
    <script type="text/javascript">
        $(function() {
            $('#attended_at').datetimepicker({
                format: 'L',
                viewMode: 'years'
            });

        });
    </script>

    {{-- Time picker --}}
    <script type="text/javascript">
        $(function () {
            $('#time_in').datetimepicker({
                format: 'LT'
            });

            $('#time_out').datetimepicker({
                format: 'LT'
            });
        });
    </script>

    {{-- Letter date/time --}}
    <script type="text/javascript">
        $(function () {
            function initLetterPickers() {
                var datePicker = $('#meeting_date_picker');
                var timePicker = $('#meeting_time_picker');
                if (datePicker.data('datetimepicker')) {
                    datePicker.datetimepicker('destroy');
                }
                if (timePicker.data('datetimepicker')) {
                    timePicker.datetimepicker('destroy');
                }
            }

            initLetterPickers();
            $('#generateLetterModal').on('shown.bs.modal', function () {
                initLetterPickers();
            });
        });
    </script>

    <script type="text/javascript">
        $(function () {
            $(document).on('change', '.custom-file-input', function () {
                var fileName = (this.files && this.files.length) ? this.files[0].name : '';
                if (fileName) {
                    var maxLen = 45;
                    var displayName = fileName.length > maxLen ? fileName.slice(0, maxLen - 3) + '...' : fileName;
                    $(this).next('.custom-file-label')
                        .addClass('selected')
                        .text(displayName)
                        .attr('title', fileName);
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(function () {
            var modal = $('#generateLetterModal');
            if (!modal.length) {
                return;
            }

            var disputeNo = modal.data('dispute-no') || '';
            var beneficiaryName = modal.data('beneficiary') || '';
            var beneficiaryAge = modal.data('beneficiary-age') || '';
            var beneficiaryGender = modal.data('beneficiary-gender') || '';
            var beneficiaryAddress = modal.data('beneficiary-address') || '';
            var beneficiaryWard = modal.data('beneficiary-ward') || '';
            var beneficiaryDistrict = modal.data('beneficiary-district') || '';
            var beneficiaryLocation = modal.data('beneficiary-location') || '';
            var serviceType = modal.data('service-type') || '';
            var problemDescription = modal.data('problem-description') || '';
            var whereReported = modal.data('where-reported') || '';
            var reportedOn = modal.data('reported-on') || '';
            var caseType = modal.data('case-type') || '';
            var letterDate = modal.data('letter-date') || '';

            var logoUrl = @json(asset('assets/images/logo.png'));

            function buildHeader(contactLine) {
                var contactHtml = contactLine
                    ? "<div class=\"letter-contact\">" + contactLine + "</div>"
                    : "";
                return ""
                    + "<div class=\"letter-head\">"
                    + "<div class=\"letter-logo\"><img src=\"{logoUrl}\" alt=\"AJISO\"></div>"
                    + contactHtml
                    + "</div>"
                    + "<div class=\"letter-divider\"></div>";
            }

            function buildFeedbackSection() {
                return ""
                    + "<div class=\"letter-form\">"
                    + "<div class=\"letter-center\">ACTION FOR JUSTICE IN SOCIETY (AJISO)</div>"
                    + "<div class=\"letter-center\">FOMU YA MAONI YA MTEJA</div>"
                    + "<div class=\"letter-line-row\">"
                    + "<span class=\"letter-line-label\">JINA</span>"
                    + "<span class=\"letter-line letter-line--xl\">{feedbackName}</span>"
                    + "<span class=\"letter-note\">(Usiandike Jina)</span>"
                    + "</div>"
                    + "<div class=\"letter-line-row\">"
                    + "<span class=\"letter-line-label\">UMRI</span>"
                    + "<span class=\"letter-line letter-line--sm\">{feedbackAge}</span>"
                    + "</div>"
                    + "<div class=\"letter-line-row\">"
                    + "<span class=\"letter-line-label\">JINSIA</span>"
                    + "<span class=\"letter-line letter-line--sm\">{feedbackGender}</span>"
                    + "</div>"
                    + "<div class=\"letter-line-row\">"
                    + "<span class=\"letter-line-label\">UNAKOTOKEA</span>"
                    + "<span class=\"letter-line letter-line--xl\">{feedbackLocation}</span>"
                    + "</div>"
                    + "<div class=\"letter-line-row letter-line-row--wrap\">"
                    + "<span class=\"letter-line-label\">TATIZO LAKO</span>"
                    + "<span class=\"letter-line letter-line--lg\">{feedbackIssue}</span>"
                    + "<span class=\"letter-line-label\">MUDA ULIOINGIA</span>"
                    + "<span class=\"letter-line letter-line--sm\">{feedbackTimeIn}</span>"
                    + "<span class=\"letter-line-label\">MUDA ULIOTOKA</span>"
                    + "<span class=\"letter-line letter-line--sm\">{feedbackTimeOut}</span>"
                    + "</div>"
                    + "<div class=\"letter-line-row\">"
                    + "<span class=\"letter-line-label\">UMEKUJA KWA HUDUMA GANI ?</span>"
                    + "<span class=\"letter-line letter-line--xl\">{feedbackService}</span>"
                    + "</div>"
                    + "<table class=\"letter-table\">"
                    + "<tr>"
                    + "<td class=\"letter-table-num\">1.</td>"
                    + "<td class=\"letter-table-question\">Umepokelewaje / Mapokezi ?</td>"
                    + "<td class=\"letter-table-option\">Vizuri</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "<td class=\"letter-table-option\">Vibaya</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "</tr>"
                    + "<tr>"
                    + "<td class=\"letter-table-num\">2.</td>"
                    + "<td class=\"letter-table-question\">Umehudumiwaje na Mwanasheria ?</td>"
                    + "<td class=\"letter-table-option\">Vizuri</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "<td class=\"letter-table-option\">Vibaya</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "</tr>"
                    + "<tr>"
                    + "<td class=\"letter-table-num\">3.</td>"
                    + "<td class=\"letter-table-question\">Je umeridhika na huduma uliyopewa?</td>"
                    + "<td class=\"letter-table-option\">Ndiyo</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "<td class=\"letter-table-option\">Hapana</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "</tr>"
                    + "<tr>"
                    + "<td class=\"letter-table-num\">4.</td>"
                    + "<td class=\"letter-table-question\">Je umepata huduma kwa wakati ?</td>"
                    + "<td class=\"letter-table-option\">Ndiyo</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "<td class=\"letter-table-option\">Hapana</td>"
                    + "<td><span class=\"letter-box\"></span></td>"
                    + "</tr>"
                    + "</table>"
                    + "</div>";
            }

            var templates = {
                wito: {
                    language: "sw",
                    title: "BARUA YA WITO.",
                    html: buildHeader("P.O. Box 272 MOSHI, KILIMANJARO,TANZANIA,Phone 255 792 119 129; Email: ajisotz@yahoo.com")
                        + "<div class=\"letter-meta\">"
                        + "<div>KUMB: Na: <span class=\"letter-line letter-line--long\">{disputeNo}</span></div>"
                        + "<div>Tarehe <span class=\"letter-line letter-line--short\">{letterDate}</span></div>"
                        + "</div>"
                        + "<div class=\"letter-recipient\">"
                        + "<div>KWA: <span class=\"letter-line letter-line--long\">{recipientName}</span></div>"
                        + "<div class=\"letter-lines\">[[recipientAddressLines]]</div>"
                        + "</div>"
                        + "<div class=\"letter-body\">"
                        + "<div>Ndugu,</div>"
                        + "<div class=\"letter-subject\">YAH: <span class=\"letter-subject-underline\">BARUA YA WITO.</span></div>"
                        + "<p>Tafadhali rejea kichwa cha habari hapo juu.</p>"
                        + "<p>AJISO ni Kituo cha Msaada wa Sheria na Habari za Haki za Binadamu kinatoa msaada wa kisheria kwa jamii.</p>"
                        + "<p>Hivyo basi kwa barua hii, tunakuomba ufike hapa ofisini kwetu tarehe <span class=\"letter-line letter-line--medium\">{meetingDate}</span>, saa <span class=\"letter-line letter-line--short\">{meetingTime}</span> <span class=\"letter-line letter-line--short\">{meetingPeriod}</span> siku ya <span class=\"letter-line letter-line--medium\">{meetingDay}</span> kwa majadiliano ya pamoja.</p>"
                        + "<p>Ofisi ipo KARIBU NA KANISA KATOLIKI KORONGONI AU KARIBU NA HOSPITALI YA MOSHI HEALTH CENTRE, KATA YA KIUSA, MTAA WA RAMOLE unaotazamana na shule ya msingi jamhuri, utaona kibao kinachoelekeza.</p>"
                        + "<p>Tafadhali tunaomba ushirikiano wako.</p>"
                        + "<p>Wako.</p>"
                        + "<div class=\"letter-signature\">"
                        + "<div><span class=\"letter-line letter-line--long\"></span></div>"
                        + "<div>Virginia Silayo</div>"
                        + "<div>MKURUGENZI MTENDAJI - AJISO.</div>"
                        + "</div>"
                        + "<div class=\"letter-receive\">"
                        + "<div>Nimepokea leo tarehe <span class=\"letter-line letter-line--long\"></span></div>"
                        + "<div>Sahihi <span class=\"letter-line letter-line--long\"></span></div>"
                        + "</div>"
                        + "</div>"
                },
                reminder: {
                    language: "sw",
                    title: "KUMBUSHO LA WITO.",
                    html: buildHeader("P.O. Box 272 MOSHI, KILIMANJARO,TANZANIA.Phone (255 792 119 129: Email: ajisotz@yahoo.com")
                        + "<div class=\"letter-meta\">"
                        + "<div>KUMB: Na: <span class=\"letter-line letter-line--long\">{disputeNo}</span></div>"
                        + "<div>Tarehe <span class=\"letter-line letter-line--short\">{letterDate}</span></div>"
                        + "</div>"
                        + "<div class=\"letter-recipient\">"
                        + "<div>KWA: <span class=\"letter-line letter-line--long\">{recipientName}</span></div>"
                        + "<div class=\"letter-lines\">[[recipientAddressLines]]</div>"
                        + "</div>"
                        + "<div class=\"letter-body\">"
                        + "<div>Ndugu,</div>"
                        + "<div class=\"letter-subject\">YAH: KUMBUSHO LA WITO.</div>"
                        + "<p>Kichwa cha habari hapo juu cha husika.</p>"
                        + "<p>Rejea barua yetu ya tarehe <span class=\"letter-line letter-line--long\">{letterDate}</span> yenye kichwa cha habari Wito.</p>"
                        + "<p>Kwa barua hii, tunakuomba ufike hapa ofisini kwetu tarehe <span class=\"letter-line letter-line--short\">{meetingDate}</span> saa <span class=\"letter-line letter-line--short\">{meetingTime}</span> <span class=\"letter-line letter-line--short\">{meetingPeriod}</span> kwa majadiliano ya pamoja.</p>"
                        + "<p>Ofisi ipo KARIBU NA KANISA KATOLIKI KORONGONI AU KARIBU NA HOSPITALI YA MOSHI HEALTH CENTRE, KATA YA KIUSA, MTAA WA RAMOLE unaotazamana na shule ya Msingi Jamhuri, utaona kibao kinachoelekeza.</p>"
                        + "<p>Endapo hutafika tutawajibika kuchukua hatua zaidi za kisheria ikiwa ni pamoja na kufungua kesi Mahakamani.</p>"
                        + "<p>Kwa maelezo zaidi unaweza kuwasiliana na ofisi kupitia namba 0622450127</p>"
                        + "<p>Tafadhali ufike bila kukosa.</p>"
                        + "<p>Wako,</p>"
                        + "<div class=\"letter-signature\">"
                        + "<div><span class=\"letter-line letter-line--long\"></span></div>"
                        + "<div>Virginia C. Silayo.</div>"
                        + "<div>MKURUGENZI MTENDAJI - AJISO.</div>"
                        + "</div>"
                        + "</div>"
                },
                feedback: {
                    language: "sw",
                    title: "FOMU YA MAONI YA MTEJA",
                    html: buildFeedbackSection() + buildFeedbackSection() + buildFeedbackSection()
                },
                referral: {
                    language: "en",
                    title: "REFFERAL FORM",
                    html: buildHeader("P.O. Box 272 MOSHI, KILIMANJARO, TANZANIA. Tel: 255 - 272750941; Fax: (255) 22727522241; Email: ajisotz@yahoo.com")
                        + "<div class=\"letter-ref-title\">REFFERAL FORM</div>"
                        + "<div class=\"letter-line-row letter-line-row--wrap\">"
                        + "<span class=\"letter-line-label\">Client Name</span>"
                        + "<span class=\"letter-line letter-line--xl\">{clientName}</span>"
                        + "<span class=\"letter-line-label\">Age</span>"
                        + "<span class=\"letter-line letter-line--xs\">{clientAge}</span>"
                        + "<span class=\"letter-line-label\">Sex:</span>"
                        + "<span class=\"letter-box\">[[clientSexF]]</span><span class=\"letter-box-label\">F</span>"
                        + "<span class=\"letter-box\">[[clientSexM]]</span><span class=\"letter-box-label\">M</span>"
                        + "</div>"
                        + "<div class=\"letter-line-row letter-line-row--wrap\">"
                        + "<span class=\"letter-line-label\">Dispute No.</span>"
                        + "<span class=\"letter-line letter-line--md\">{disputeNo}</span>"
                        + "<span class=\"letter-line-label\">District:</span>"
                        + "<span class=\"letter-line letter-line--md\">{district}</span>"
                        + "<span class=\"letter-line-label\">Village/ward:</span>"
                        + "<span class=\"letter-line letter-line--md\">{villageWard}</span>"
                        + "</div>"
                        + "<div class=\"letter-line-row letter-line-row--wrap\">"
                        + "<span class=\"letter-line-label\">Date:</span>"
                        + "<span class=\"letter-line letter-line--sm\">{referralDate}</span>"
                        + "<span class=\"letter-line-label\">Dispute from</span>"
                        + "<span class=\"letter-line letter-line--md\">{disputeFrom}</span>"
                        + "<span class=\"letter-line-label\">To:</span>"
                        + "<span class=\"letter-line letter-line--md\">{disputeTo}</span>"
                        + "</div>"
                        + "<table class=\"letter-ref-table\">"
                        + "<tr>"
                        + "<th>Type of Case</th>"
                        + "<th class=\"letter-ref-center\">M</th>"
                        + "<th>Type of Case</th>"
                        + "<th class=\"letter-ref-center\">M</th>"
                        + "</tr>"
                        + "<tr>"
                        + "<td>1. Land</td><td></td><td>5. Labour</td><td></td>"
                        + "</tr>"
                        + "<tr>"
                        + "<td>2.</td><td></td><td>6. Civil</td><td></td>"
                        + "</tr>"
                        + "<tr>"
                        + "<td class=\"letter-ref-center\">Marriage/Matrimonia<br>l</td><td></td><td></td><td></td>"
                        + "</tr>"
                        + "<tr>"
                        + "<td>3. Child maintenance</td><td></td><td>7. Criminal</td><td></td>"
                        + "</tr>"
                        + "<tr>"
                        + "<td>4. Probate/inheritance</td><td></td><td>8. GBV</td><td></td>"
                        + "</tr>"
                        + "</table>"
                        + "<div class=\"letter-section-title\">Information on how the client was served up to this stage:</div>"
                        + "<div class=\"letter-ref-box\"></div>"
                        + "<div class=\"letter-line-row letter-line-row--wrap\">"
                        + "<span class=\"letter-line-label\">Name of the referrer</span>"
                        + "<span class=\"letter-line letter-line--lg\"></span>"
                        + "<span class=\"letter-line-label\">Signature:</span>"
                        + "<span class=\"letter-line letter-line--md\"></span>"
                        + "<span class=\"letter-line-label\">Date</span>"
                        + "<span class=\"letter-line letter-line--sm\"></span>"
                        + "</div>"
                        + "<div class=\"letter-ref-divider\">= = = = = = = = = = = = = = = === = = = = This part should be referred to the office = = = = = = = = = = = = = = = =</div>"
                        + "<div class=\"letter-section-title letter-center\">INSTITUTION/WARD/ORGANIZATION/COURT TRANSFER/COURT RECEIVED DISPUTE</div>"
                        + "<div class=\"letter-line-row letter-line-row--wrap\">"
                        + "<span class=\"letter-line-label\">Name of the organization</span>"
                        + "<span class=\"letter-line letter-line--lg\"></span>"
                        + "<span class=\"letter-line-label\">Tel. No:</span>"
                        + "<span class=\"letter-line letter-line--md\"></span>"
                        + "<span class=\"letter-line-label\">Date:</span>"
                        + "<span class=\"letter-line letter-line--sm\"></span>"
                        + "</div>"
                        + "<div class=\"letter-section-title letter-center\">INFORMATION ON HOW THE CLIENT WAS SERVED</div>"
                        + "<div class=\"letter-ref-box\"></div>"
                        + "<div class=\"letter-ref-code\">AJS-RF-4-B</div>"
                        + "<div class=\"letter-center letter-footnote\">AJISO-Client Referral Form-AJS-RF 4-B</div>"
                        + "<div class=\"letter-line-row letter-line-row--wrap\">"
                        + "<span class=\"letter-line-label\">Name :</span>"
                        + "<span class=\"letter-line letter-line--lg\"></span>"
                        + "<span class=\"letter-line-label\">Signature</span>"
                        + "<span class=\"letter-line letter-line--md\"></span>"
                        + "<span class=\"letter-line-label\">Position:</span>"
                        + "<span class=\"letter-line letter-line--md\"></span>"
                        + "</div>"
                }
            };

            function normalizeValue(value, fallback) {
                if (value && value.toString().trim().length) {
                    return value.toString().trim();
                }
                return fallback || "________";
            }

            function escapeHtml(value) {
                return value
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/\"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function formatMultiline(value) {
                return escapeHtml(value).replace(/\n/g, "<br>");
            }

            function normalizeLineValue(value) {
                if (value && value.toString().trim().length) {
                    return value.toString().trim();
                }
                return "";
            }

            function compileTemplate(template, data) {
                var output = template.replace(/\[\[([a-zA-Z0-9_]+)\]\]/g, function (match, key) {
                    if (Object.prototype.hasOwnProperty.call(data, key)) {
                        return data[key];
                    }
                    return match;
                });
                return output.replace(/\{([a-zA-Z0-9_]+)\}/g, function (match, key) {
                    if (Object.prototype.hasOwnProperty.call(data, key)) {
                        return data[key];
                    }
                    return match;
                });
            }

            function buildLineBlocks(value, minLines) {
                var lines = [];
                var rawLines = value ? value.toString().split(/\r?\n/) : [];
                rawLines = rawLines.map(function (line) { return line.trim(); }).filter(Boolean);
                var totalLines = Math.max(minLines || 1, rawLines.length);
                for (var i = 0; i < totalLines; i += 1) {
                    var lineValue = rawLines[i] ? escapeHtml(rawLines[i]) : "&nbsp;";
                    lines.push("<span class=\"letter-line letter-line--block\">" + lineValue + "</span>");
                }
                return lines.join("");
            }

            function updateMeetingDayOptions(language) {
                var selectLabel = language === 'en' ? "Select" : "Chagua";
                var options = language === 'en'
                    ? ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
                    : ["Jumatatu", "Jumanne", "Jumatano", "Alhamisi", "Ijumaa", "Jumamosi", "Jumapili"];
                var mapToSw = {
                    Monday: "Jumatatu",
                    Tuesday: "Jumanne",
                    Wednesday: "Jumatano",
                    Thursday: "Alhamisi",
                    Friday: "Ijumaa",
                    Saturday: "Jumamosi",
                    Sunday: "Jumapili"
                };
                var mapToEn = {
                    Jumatatu: "Monday",
                    Jumanne: "Tuesday",
                    Jumatano: "Wednesday",
                    Alhamisi: "Thursday",
                    Ijumaa: "Friday",
                    Jumamosi: "Saturday",
                    Jumapili: "Sunday"
                };
                var current = $('#meeting_day').val();
                if (current) {
                    if (language === 'sw' && mapToSw[current]) {
                        current = mapToSw[current];
                    } else if (language === 'en' && mapToEn[current]) {
                        current = mapToEn[current];
                    }
                }
                $('#meeting_day').empty().append(new Option(selectLabel, ""));
                options.forEach(function (label) {
                    $('#meeting_day').append(new Option(label, label));
                });
                if (current && options.indexOf(current) !== -1) {
                    $('#meeting_day').val(current);
                } else {
                    $('#meeting_day').val('');
                }
                $('#meeting_day').trigger('change.select2');
            }

            function applyMeetingDayFromDate(dateValue) {
                if (!dateValue) {
                    return;
                }
                var parsed = moment.isMoment(dateValue)
                    ? dateValue.clone()
                    : moment(dateValue, ['DD/MM/YYYY', 'D/M/YYYY', 'L', 'YYYY-MM-DD'], true);
                if (!parsed.isValid()) {
                    return;
                }
                var dayLabel = parsed.locale(currentLanguage === 'en' ? 'en' : 'sw').format('dddd');
                $('#meeting_day').val(dayLabel).trigger('change.select2').trigger('change');
            }

            function updatePickerLocale(language) {
                var locale = language === 'en' ? 'en' : 'sw';
                var datePicker = $('#meeting_date_picker');
                var timePicker = $('#meeting_time_picker');
                if (datePicker.data('datetimepicker')) {
                    datePicker.datetimepicker('locale', locale);
                }
                if (timePicker.data('datetimepicker')) {
                    timePicker.datetimepicker('locale', locale);
                }
            }

            function normalizeSex(value) {
                var raw = (value || '').toString().trim().toLowerCase();
                if (raw === 'm' || raw === 'male' || raw === 'me') {
                    return 'M';
                }
                if (raw === 'f' || raw === 'female' || raw === 'ke') {
                    return 'F';
                }
                return '';
            }

            function normalizeGenderLabel(value) {
                var raw = (value || '').toString().trim().toLowerCase();
                if (raw === 'm' || raw === 'male' || raw === 'me') {
                    return 'ME';
                }
                if (raw === 'f' || raw === 'female' || raw === 'ke') {
                    return 'KE';
                }
                return value || '';
            }

            function getMeetingPeriod(timeValue, language) {
                if (!timeValue) {
                    return '';
                }
                var parsed = moment(timeValue, ['HH:mm', 'H:mm', 'hh:mm A', 'h:mm A'], true);
                if (!parsed.isValid()) {
                    return '';
                }
                var isMorning = parsed.hour() < 12;
                if (language === 'en') {
                    return isMorning ? 'morning' : 'afternoon';
                }
                return isMorning ? 'asubuhi' : 'mchana';
            }

            function setFieldIfEmpty(selector, value) {
                var field = $(selector);
                if (!field.length || field.val()) {
                    return;
                }
                if (value && value.toString().trim().length) {
                    field.val(value).trigger('change');
                }
            }

            function normalizeDateForInput(value) {
                if (!value) {
                    return '';
                }
                var parsed = moment(value, ['DD/MM/YYYY', 'D/M/YYYY', 'L', 'YYYY-MM-DD'], true);
                return parsed.isValid() ? parsed.format('YYYY-MM-DD') : '';
            }

            function formatMeetingDateForLetter(value) {
                if (!value) {
                    return '';
                }
                var parsed = moment(value, ['YYYY-MM-DD', 'DD/MM/YYYY', 'D/M/YYYY', 'L'], true);
                return parsed.isValid() ? parsed.format('DD/MM/YYYY') : value;
            }

            function setMeetingDateIfEmpty(value) {
                var field = $('#meeting_date');
                if (!field.length || field.val()) {
                    return;
                }
                var parsed = moment(value, ['DD/MM/YYYY', 'D/M/YYYY', 'L', 'YYYY-MM-DD'], true);
                if (!parsed.isValid()) {
                    return;
                }
                field.val(parsed.format('YYYY-MM-DD')).trigger('change');
            }

            function applyAutoFill(letterType) {
                if (letterType !== 'referral') {
                    return;
                }
                var normalizedSex = normalizeSex(beneficiaryGender);
                var locationFallback = beneficiaryWard || beneficiaryAddress || beneficiaryLocation || beneficiaryDistrict || '';

                setFieldIfEmpty('#recipientName', beneficiaryName);
                setFieldIfEmpty('#referral_age', beneficiaryAge);
                setFieldIfEmpty('#referral_sex', normalizedSex);
                setFieldIfEmpty('#referral_district', beneficiaryDistrict);
                setFieldIfEmpty('#referral_village', locationFallback);
                setFieldIfEmpty('#referral_dispute_from', whereReported);
                setMeetingDateIfEmpty(reportedOn || letterDate);
                applyMeetingDayFromDate($('#meeting_date').val());
            }

            function toggleLetterFields(letterType, language) {
                var isReferral = letterType === 'referral';
                $('#referralFields').toggleClass('d-none', !isReferral);
                $('#recipientAddressGroup').toggleClass('d-none', isReferral);
                $('#meetingTimeGroup').toggleClass('d-none', isReferral);
                $('#meetingDayGroup').toggleClass('d-none', isReferral);
                $('#letterNotesGroup').toggleClass('d-none', isReferral);
                var nameLabel = isReferral ? 'Client Name' : 'Recipient Name';
                $('#recipientNameLabel').text(nameLabel);
                var namePlaceholder = isReferral ? 'Enter client name' : 'Enter recipient full name';
                $('#recipientName').attr('placeholder', namePlaceholder);
                var dateLabel = isReferral ? (language === 'sw' ? 'Tarehe' : 'Date') : (language === 'sw' ? 'Tarehe ya Mkutano' : 'Meeting Date');
                $('#meetingDateLabel').text(dateLabel);
                var timeLabel = language === 'sw' ? 'Muda' : 'Time';
                $('#meetingTimeLabel').text(timeLabel);
                var dayLabel = language === 'sw' ? 'Siku' : 'Day';
                $('#meetingDayLabel').text(dayLabel);
            }

            var currentLanguage = $('#letterLanguage').val() || 'sw';
            updateMeetingDayOptions(currentLanguage);
            updatePickerLocale(currentLanguage);
            toggleLetterFields($('#letterType').val() || 'wito', currentLanguage);

            $('#letterLanguage').on('change', function () {
                var selected = $(this).val() || 'sw';
                if (selected !== currentLanguage) {
                    currentLanguage = selected;
                    updateMeetingDayOptions(currentLanguage);
                    updatePickerLocale(currentLanguage);
                    toggleLetterFields($('#letterType').val() || 'wito', currentLanguage);
                }
            });

            $('#letterType').on('change', function () {
                var selectedType = $(this).val() || 'wito';
                var templateConfig = templates[selectedType] || templates.wito;
                var language = templateConfig.language || currentLanguage;
                if ($('#letterLanguage').val() !== language) {
                    $('#letterLanguage').val(language).trigger('change.select2').trigger('change');
                }
                toggleLetterFields(selectedType, language);
                applyAutoFill(selectedType);
                updateLetterPreview();
            });

            function buildLetterHtml() {
                var letterType = $('#letterType').val() || 'wito';
                var templateConfig = templates[letterType] || templates.wito;
                var language = templateConfig.language || 'sw';
                if ($('#letterLanguage').val() !== language) {
                    $('#letterLanguage').val(language).trigger('change.select2').trigger('change');
                }
                updateMeetingDayOptions(language);
                updatePickerLocale(language);
                toggleLetterFields(letterType, language);
                applyAutoFill(letterType);
                var recipientName = $('#recipientName').val();
                var recipientAddress = $('#recipientAddress').val();
                var meetingDate = $('#meeting_date').val();
                var meetingTime = $('#meeting_time').val();
                var meetingDay = $('#meeting_day').val();
                var notes = $('#letterNotes').val();
                var clientAge = $('#referral_age').val();
                var clientSex = $('#referral_sex').val();
                var district = $('#referral_district').val();
                var villageWard = $('#referral_village').val();
                var disputeFrom = $('#referral_dispute_from').val();
                var disputeTo = $('#referral_dispute_to').val();
                var sexFMark = clientSex === 'F' ? 'X' : '';
                var sexMMark = clientSex === 'M' ? 'X' : '';
                var feedbackGender = normalizeGenderLabel(beneficiaryGender);
                var feedbackLocation = beneficiaryLocation || beneficiaryAddress || beneficiaryDistrict || '';

                var template = templateConfig.html || '';
                var letterTitle = templateConfig.title || '';
                var recipientNameOrSirMadam = normalizeValue(recipientName, language === 'sw' ? 'Mheshimiwa' : 'Sir/Madam');
                var longDots = "................................................";
                var shortDots = "....................";
                var meetingPeriod = getMeetingPeriod(meetingTime, language);

                var data = {
                    logoUrl: logoUrl,
                    disputeNo: escapeHtml(normalizeValue(disputeNo, longDots)),
                    letterDate: escapeHtml(normalizeValue(letterDate, shortDots)),
                    recipientName: escapeHtml(normalizeLineValue(recipientName)),
                    recipientAddress: formatMultiline(normalizeLineValue(recipientAddress)),
                    recipientAddressLines: buildLineBlocks(recipientAddress, 2),
                    clientName: escapeHtml(normalizeLineValue(recipientName)),
                    clientAge: escapeHtml(normalizeLineValue(clientAge)),
                    district: escapeHtml(normalizeLineValue(district)),
                    villageWard: escapeHtml(normalizeLineValue(villageWard)),
                    disputeFrom: escapeHtml(normalizeLineValue(disputeFrom)),
                    disputeTo: escapeHtml(normalizeLineValue(disputeTo)),
                    referralDate: escapeHtml(normalizeLineValue(formatMeetingDateForLetter(meetingDate))),
                    clientSexF: sexFMark,
                    clientSexM: sexMMark,
                    letterTitle: escapeHtml(letterTitle),
                    recipientNameOrSirMadam: escapeHtml(recipientNameOrSirMadam),
                    caseType: escapeHtml(normalizeValue(caseType, longDots)),
                    meetingDate: escapeHtml(normalizeLineValue(formatMeetingDateForLetter(meetingDate))),
                    meetingTime: escapeHtml(normalizeLineValue(meetingTime)),
                    meetingDay: escapeHtml(normalizeLineValue(meetingDay)),
                    meetingPeriod: escapeHtml(normalizeLineValue(meetingPeriod)),
                    notes: formatMultiline(normalizeValue(notes, "-")),
                    feedbackName: escapeHtml(normalizeLineValue(beneficiaryName)),
                    feedbackAge: escapeHtml(normalizeLineValue(beneficiaryAge)),
                    feedbackGender: escapeHtml(normalizeLineValue(feedbackGender)),
                    feedbackLocation: escapeHtml(normalizeLineValue(feedbackLocation)),
                    feedbackIssue: escapeHtml(normalizeLineValue(problemDescription)),
                    feedbackTimeIn: escapeHtml(normalizeLineValue('')),
                    feedbackTimeOut: escapeHtml(normalizeLineValue('')),
                    feedbackService: escapeHtml(normalizeLineValue(serviceType))
                };

                return compileTemplate(template, data);
            }

            var isPreviewUpdating = false;
            var previewQueued = false;
            var previewTimer = null;

            function updateLetterPreview() {
                if (isPreviewUpdating) {
                    previewQueued = true;
                    return;
                }
                isPreviewUpdating = true;
                window.requestAnimationFrame(function () {
                    $('#letterPreview').html(buildLetterHtml());
                    isPreviewUpdating = false;
                    if (previewQueued) {
                        previewQueued = false;
                        updateLetterPreview();
                    }
                });
            }

            function scheduleLetterPreview() {
                if (previewTimer) {
                    clearTimeout(previewTimer);
                }
                previewTimer = setTimeout(updateLetterPreview, 80);
            }

            modal.on('shown.bs.modal', function () {
                setMeetingDateIfEmpty(letterDate);
                applyMeetingDayFromDate($('#meeting_date').val());
                scheduleLetterPreview();
            });

            modal.find('input, textarea, select').on('input change', function () {
                scheduleLetterPreview();
            });

            $('#meeting_date').off('change.letter').on('change.letter', function () {
                applyMeetingDayFromDate($(this).val());
                scheduleLetterPreview();
            });

            $('#meeting_time').off('change.letter').on('change.letter', function () {
                scheduleLetterPreview();
            });

            $('#previewLetter').on('click', function () {
                updateLetterPreview();
            });

            $('#printLetter').on('click', function () {
                var content = buildLetterHtml();
                if (typeof window.ajisoPrintLetter === 'function') {
                    window.ajisoPrintLetter(content);
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(function () {
            @if (session('prompt_status_update'))
                var statusModal = $('#disputeStatusModal');
                if (statusModal.length) {
                    statusModal.modal('show');
                }
            @endif

            var reopenBtn = $('#reopenCase');
            if (reopenBtn.length) {
                reopenBtn.on('click', function () {
                    var statusId = $(this).data('status-id');
                    var statusSelect = $('#dispute_status');
                    if (statusId && statusSelect.length) {
                        statusSelect.val(statusId).trigger('change');
                    }
                    $('#disputeStatusModal').modal('show');
                });
            }
        });
    </script>
    <script type="text/javascript">
        $(function () {
            var targetSelect = $('#target_staff_id');
            if (targetSelect.length) {
                targetSelect.select2();
            }
            @if ($errors->has('reason_description') || $errors->has('target_staff_id'))
                $('#reassignmentRequestModal').modal('show');
            @endif
        });
    </script>
@endpush

@push('modals')
    @if (!empty($canRequestReassignment))
        @include('modals.reassignment-request')
    @endif
    @include('modals.send-notifications')
    @include('modals.clinic-progress')
    @include('modals.change-dispute-status')
    @include('modals.provider-remarks')
    @cannot('isClerk')
        @include('modals.generate-letter')
    @endcannot
@endpush
