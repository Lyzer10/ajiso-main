@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}" />
    <style>
        .request-action-cell {
            white-space: nowrap;
        }
        .request-action-cell .action-inline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
        }
        .request-action-cell .action-inline form {
            margin-bottom: 0;
        }
        .request-action-cell .select2-container {
            width: 210px !important;
            min-width: 210px !important;
            max-width: 210px !important;
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
            <li><span>{{ __('Reassignment Request List') }}</span></li>
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
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        {{ __('Reassignment Request list') }}
                        <div class="small text-muted mt-1">
                            {{ __('Total Requests') }}: {{ $assignment_requests->total() }}
                        </div>
                        @canany(['isSuperAdmin', 'isAdmin', 'isClerk'])
                            <button class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#bulkNoticeModal">
                                <i class="fas fa-paper-plane"></i>
                                {{ __('Bulk Message') }}
                            </button>
                        @endcanany
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table table-striped progress-table text-center">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Case Status') }}</th>
                                    <th>{{ __('Reason Description') }}</th>
                                    <th>{{ __('Requested By') }}</th>
                                    <th>{{ __('Requested Assistance From') }}</th>
                                    <th>{{ __('Requested') }}</th>
                                    <th>{{ __('Request Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($assignment_requests->count())
                                    @foreach ($assignment_requests as $assignment_request)
                                    <tr>
                                        <td>{{ '#'.$assignment_request->id }}</td>
                                        <td>
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $assignment_request->dispute_id]) }}" class="text-secondary font-weight-bold" title="{{  __('Click to view dispute') }}">
                                                {{ $assignment_request->dispute->dispute_no }}
                                            </a>
                                        </td>
                                        <td>
                                            @php
                                                $statusSlug = \Illuminate\Support\Str::slug($assignment_request->dispute->disputeStatus->dispute_status ?? 'pending');
                                            @endphp
                                            <span class="badge badge-info">
                                                {{ $assignment_request->dispute->disputeStatus->dispute_status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span title="{{ $assignment_request->reason_description }}">
                                                {{ Illuminate\Support\Str::limit($assignment_request->reason_description, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $requester = $assignment_request->requestedBy ?: $assignment_request->requesterUser;
                                            @endphp
                                            @if ($assignment_request->staff_id && $requester)
                                                @canany(['isAdmin', 'isStaff', 'isSuperAdmin'])
                                                    <a href="{{ route('staff.show', [app()->getLocale(), $assignment_request->staff_id, ]) }}" title="{{  __('Click to view legal aid provider') }}">
                                                        {{ $requester->first_name.' '
                                                            .$requester->middle_name.' '
                                                            .$requester->last_name
                                                        }}
                                                    </a>
                                                @elsecanany(['isClerk', 'isStaff'])
                                                    <span class="text-danger">
                                                        {{ $requester->first_name.' '
                                                            .$requester->middle_name.' '
                                                            .$requester->last_name
                                                        }}
                                                    </span>
                                                @endcanany
                                            @elseif ($requester)
                                                {{ $requester->first_name.' '
                                                    .$requester->middle_name.' '
                                                    .$requester->last_name
                                                }}
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($assignment_request->targetStaff && $assignment_request->targetStaff->user)
                                                <a href="{{ route('staff.show', [app()->getLocale(), $assignment_request->target_staff_id]) }}" title="{{  __('Click to view staff') }}">
                                                    {{ $assignment_request->targetStaff->user->first_name.' '
                                                        .$assignment_request->targetStaff->user->middle_name.' '
                                                        .$assignment_request->targetStaff->user->last_name
                                                    }}
                                                </a>
                                                @if ($assignment_request->targetStaff->center)
                                                    <br><small>{{ $assignment_request->targetStaff->center->name }}</small>
                                                @endif
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                        @php
                                            $requestedAt = $assignment_request->created_at ?? $assignment_request->updated_at;
                                        @endphp
                                        <td>
                                            {{ $requestedAt ? $requestedAt->diffForHumans() : __('N/A') }}
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if ( $assignment_request->request_status  === 'accepted')
                                                    badge-success
                                                @elseif ( $assignment_request->request_status  === 'pending')
                                                    badge-warning
                                                @else
                                                    badge-danger
                                                @endif
                                            ">
                                            {{ __($assignment_request->request_status) }}
                                            </span>
                                        </td>
                                        @if ($assignment_request->request_status  === 'pending')
                                        <td class="request-action-cell">
                                            <div class="action-inline">
                                                @canany(['isAdmin', 'isSuperAdmin'])
                                                    @if (isset($availableStaff) && $availableStaff->count())
                                                        <div class="action-inline">
                                                            <form method="POST" action="{{ route('dispute.request.accept', [app()->getLocale(), $assignment_request->id]) }}" class="d-flex align-items-center mb-0">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="res" value="accepted">
                                                                @php
                                                                    $selectId = 'target_staff_id_'.$assignment_request->id;
                                                                @endphp
                                                                <select id="{{ $selectId }}" name="target_staff_id" class="form-control form-control-sm mr-2 select2" required style="width: 210px;">
                                                                    <option hidden disabled {{ $assignment_request->target_staff_id ? '' : 'selected' }} value>
                                                                        {{ __('Select legal aid provider') }}
                                                                    </option>
                                                                    @foreach ($availableStaff as $staffMember)
                                                                        @continue($assignment_request->staff_id && (int) $staffMember->id === (int) $assignment_request->staff_id)
                                                                        <option value="{{ $staffMember->id }}"
                                                                            {{ (int) $assignment_request->target_staff_id === (int) $staffMember->id ? 'selected' : '' }}>
                                                                            {{ $staffMember->user->first_name.' '
                                                                                .$staffMember->user->middle_name.' '
                                                                                .$staffMember->user->last_name.' | '
                                                                                .(optional($staffMember->center)->name ?? __('N/A'))
                                                                            }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="button" class="btn btn-sm btn-success show_accept" data-toggle="tooltip" title="{{ __('Accept reassignment request') }}">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>

                                                            <form method="POST" action="{{ route('dispute.request.reject', [app()->getLocale(), $assignment_request->id]) }}" class="mb-0">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="res" value="rejected">
                                                                <button type="button" class="btn btn-sm btn-danger show_reject" data-toggle="tooltip" title="{{ __('Reject reassignment request') }}">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="d-flex align-items-center" style="gap: 5px;">
                                                        <form method="POST" action="{{ route('dispute.request.accept', [app()->getLocale(), $assignment_request->id]) }}" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="res" value="accepted">
                                                            <button type="button" class="btn btn-sm btn-success show_accept" data-toggle="tooltip" title="{{ __('Accept reassignment request') }}">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('dispute.request.reject', [app()->getLocale(), $assignment_request->id]) }}" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="res" value="rejected">
                                                            <button type="button" class="btn btn-sm btn-danger show_reject" data-toggle="tooltip" title="{{ __('Reject reassignment request') }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endcanany
                                            </div>
                                        </td>
                                        @else
                                        <td class="request-action-cell">
                                            @if ($assignment_request->request_status === 'accepted')
                                                <a href="{{ route('dispute.show', [app()->getLocale(), $assignment_request->dispute_id]) }}" class="btn btn-sm btn-info text-white" title="{{ __('View case details') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                {{  __('N/A') }}
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="p-1" colspan="9">{{ __('No requests found') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{ $assignment_requests->count() ? $assignment_requests->links() : '' }}
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection

@push('scripts')

    {{-- sweetalert --}}
    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            if ($.fn.select2) {
                $('.select2').select2({
                    width: '210px',
                    placeholder: "{{ __('Select legal aid provider') }}",
                    minimumResultsForSearch: 0,
                });
            }
        });
    </script>

    {{-- Accept --}}
    <script type="text/javascript">
        $(document).on('click', '.show_accept', function(event) {

                var form =  $(this).closest("form");
                var select = form.find("select[name='target_staff_id']");

                if (select.length && !select.val()) {
                    event.preventDefault();
                    swal({
                        title: "{{ _('Select Legal Aid Provider') }}",
                        text: "{{ _('Please select a legal aid provider before accepting this request.') }}",
                        icon: "warning",
                    });
                    return;
                }

                var name = $(this).data("name");

                event.preventDefault();

                swal({

                    title: "{{ _('Accepting Reassignment Request') }}",

                    text: "{{ _('Are you sure you want to proceed with accepting this request?') }}",

                    icon: "warning",

                    buttons: true,

                    dangerMode: true,

                })

                .then((willDelete) => {

                if (willDelete) {

                    form.submit();

                }

                });

            });
    </script>

    {{-- Reject --}}
    <script type="text/javascript">
        $(document).on('click', '.show_reject', function(event) {

                var form;
                var requestId = $(this).data("request-id");
                
                // If button has request-id, find the reject form by action URL
                if (requestId) {
                    var rejectUrl = "{{ route('dispute.request.reject', [app()->getLocale(), ':id']) }}".replace(':id', requestId);
                    form = $('form[action="' + rejectUrl + '"]');
                } else {
                    // Otherwise use closest form
                    form = $(this).closest("form");
                }

                var name = $(this).data("name");

                event.preventDefault();

                swal({

                    title: "{{ _('Rejecting Reassignment Request') }}",

                    text: "{{ _('Are you sure you want to proceed with rejecting this request?') }}",

                    icon: "warning",

                    buttons: true,

                    dangerMode: true,

                })

                .then((willDelete) => {

                if (willDelete) {

                    form.submit();

                }

                });

            });
    </script>

    
@endpush

@push('modals')
    <!-- Bulk Notification modal-->
    <div class="modal fade" id="bulkNoticeModal" tabindex="-1" aria-labelledby="bulkNoticeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkNoticeModalLabel">
                        <i class="fas fa-paper-plane fa-fw text-info"></i>
                        {{ __('Send Bulk Notification') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('notification.store', app()->getLocale()) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="bulk_title" class="font-weight-bold">{{ __('Notification Title') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control border-append-primary" id="bulk_title" name="title" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="bulk_priority" class="font-weight-bold">{{ __('Priority') }}<sup class="text-danger">*</sup></label>
                                    <select id="bulk_priority" class="form-control border-input-primary" name="priority" required>
                                        <option hidden disabled selected value>{{ __('Choose priority') }}</option>
                                        <option value="high">{{ __('High') }}</option>
                                        <option value="medium">{{ __('Medium') }}</option>
                                        <option value="low">{{ __('Low') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="bulk_publish_to" class="font-weight-bold">{{ __('Publish To') }}<sup class="text-danger">*</sup></label>
                                    <select id="bulk_publish_to" class="form-control border-input-primary" name="publish_to" required>
                                        <option hidden disabled selected value>{{ __('Choose a recipient group') }}</option>
                                        <option value="allParalegals">{{ __('All Paralegals') }}</option>
                                        <option value="allBeneficiaries">{{ __('All Beneficiaries') }}</option>
                                        <option value="allParalegalsAndBeneficiaries">{{ __('All Paralegals and Beneficiaries') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="bulk_message" class="font-weight-bold">{{ __('Message') }}<sup class="text-danger">*</sup></label>
                                    <textarea class="form-control border-text-primary" id="bulk_message" name="message" required style="width: 100%;"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Bulk Notification modal-->
@endpush
