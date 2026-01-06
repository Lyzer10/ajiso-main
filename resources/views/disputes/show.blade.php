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
                <div class="card-header">
                    <h4 class="header-title">{{ __('Dispute Profile')}}
                        <a href="{{ route('disputes.list', app()->getLocale()) }}"
                            class="btn btn-sm btn-primary pull-right text-white">{{ __('Disputes list') }}
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    @if ($dispute->count())
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">{{ __('Dispute Info') }}</h6>
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
                                                <label for="dispute_status" class="font-weight-bold">{{ __('Dispute Status') }}</label>
                                                <input type="text" readonly class="form-control  border-input-primary" id="dispute_status" value="{{ __($dispute->disputeStatus->dispute_status) }}">
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
                        <div class="col-md-6">
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text  mb-3 font-weight-bold">{{ __('Dispute History') }} |
                                        <code class="">{{ __('Reported') }} </code>
                                        <span class="text-primary">
                                            {{ $occurrences->count().'x' }}
                                        </span>
                                    </h6>
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
                                                                    $statusSlug = \Illuminate\Support\Str::slug($occurrence->disputeStatus->dispute_status);
                                                                @endphp
                                                                <span class="badge-status status-{{ $statusSlug }}">
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
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">
                                        {{ __('LAAC Clinic Visits') }} 
                                        <span class="badge badge-success">
                                            {{  ($dispute->counselingSheets->count()) ??  0 }}
                                        </span>
                                    </h6>
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
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">{{ __('Dispute Progress') }}</h6>
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
                            <div class="card card-bordered">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">{{ __('Dispute Progress Center') }}</h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="grid-col">
                                                <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#sendNoticeModal">
                                                    <i class="fas fa-paper-plane fa-fw text-info"></i>
                                                    {{ __('Send Notification to Beneficiary') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="grid-col">
                                                <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#clinicProgressModal">
                                                    <i class="fas fa-comment-medical fa-fw text-primary"></i>
                                                    {{ __('Legal Aid Advice Counseling Clinic') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="grid-col">
                                                <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#disputeStatusModal">
                                                    <i class="fas fa-edit fa-fw text-success"></i>
                                                    {{ __('Update Dispute Statuses') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="grid-col">
                                                <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#providerRemarksModal">
                                                    <i class="fas fa-marker fa-fw text-secondary"></i>
                                                    {{ __('Legal Aid Provider Remarks') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="grid-col">
                                                <a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#disputeAttachmentModal">
                                                    <i class="fas fa-paperclip fa-fw text-info"></i>
                                                    {{ __('Add Attachment') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">
                                        {{ __('Dispute Attachments') }}
                                        <span class="badge badge-success">
                                            {{ ($dispute->attachments->count()) ?? 0 }}
                                        </span>
                                    </h6>
                                    <div class="alert-items">
                                        @if ($dispute->attachments->count())
                                            @foreach ($dispute->attachments as $attachment)
                                                <div class="alert alert-info lead attachment-item" role="alert">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between attachment-row">
                                                        <div class="attachment-meta">
                                                            <strong>{{ '#'.$loop->iteration }}</strong> |
                                                            <span class="attachment-name" title="{{ $attachment->name }}">
                                                                {{ Str::limit($attachment->name, 30) }}
                                                            </span> |
                                                            {{ Str::upper($attachment->file_type) }}
                                                        </div>
                                                        <div class="attachment-actions">
                                                            <a href="{{ route('dispute.activity.attachment.view', ['locale' => app()->getLocale(), 'attachment' => $attachment->id]) }}" target="_blank" rel="noopener">
                                                                {{ __('View') }}
                                                            </a>
                                                            /
                                                            <a href="{{ route('dispute.activity.attachment.download', ['locale' => app()->getLocale(), 'attachment' => $attachment->id]) }}">
                                                                {{ __('Download') }}
                                                            </a>
                                                            /
                                                            <form method="POST" action="{{ route('dispute.activity.attachment.delete', ['locale' => app()->getLocale(), 'attachment' => $attachment->id]) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this attachment?') }}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-link p-0 text-danger" title="{{ __('Delete attachment') }}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-primary lead" role="alert">
                                                <strong><i class="fas fa-exclamation-triangle text-warning"></i></strong>
                                                {{ __('Attachments Not Found.') }}
                                            </div>
                                        @endif
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
@endpush

@push('modals')
    @include('modals.send-notifications')
    @include('modals.clinic-progress')
    @include('modals.change-dispute-status')
    @include('modals.provider-remarks')
    @include('modals.dispute-attachment')
@endpush
