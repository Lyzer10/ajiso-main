@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'AJISO | '.$title)

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
                                    <th>{{ __('Beneficiary') }}</th>
                                    <th>{{ __('Case Status') }}</th>
                                    <th>{{ __('Reason Description') }}</th>
                                    <th>{{ __('Current Staff') }}</th>
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
                                                $beneficiary = $assignment_request->dispute->reportedBy;
                                                $beneficiaryName = trim(implode(' ', array_filter([
                                                    $beneficiary->first_name ?? '',
                                                    $beneficiary->middle_name ?? '',
                                                    $beneficiary->last_name ?? ''
                                                ])));
                                            @endphp
                                            <a href="{{ route('beneficiary.show', [app()->getLocale(), $assignment_request->dispute->beneficiary_id]) }}" class="text-secondary">
                                                {{ $beneficiaryName ?? 'N/A' }}
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
                                            @if (is_null($assignment_request->staff_id))
                                                @canany(['isAdmin', 'isStaff', 'isSuperAdmin'])
                                                    <a href="{{ route('dispute.assign', [app()->getLocale(), $assignment_request->dispute_id]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                    {{ __('Unassigned') }}
                                                    </a>
                                                @elsecanany(['isClerk', 'isStaff'])
                                                    <a class="text-danger" >{{ __('Unassigned') }}</a>
                                                @endcanany
                                            @else
                                                @canany(['isAdmin', 'isStaff', 'isSuperAdmin'])
                                                <a href="{{ route('staff.show', [app()->getLocale(), $assignment_request->staff_id, ]) }}" title="{{  __('Click to view assigned legal aid provider') }}">
                                                    {{ $assignment_request->requestedBy->first_name.' '
                                                        .$assignment_request->requestedBy->middle_name.' '
                                                        .$assignment_request->requestedBy->last_name
                                                    }}
                                                </a>
                                                @elsecanany(['isClerk', 'isStaff'])
                                                        <a class="text-danger" >
                                                            {{ $assignment_request->requestedBy->first_name.' '
                                                                .$assignment_request->requestedBy->middle_name.' '
                                                                .$assignment_request->requestedBy->last_name
                                                            }}
                                                        </a>
                                                @endcanany
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
                                        <td>{{ Carbon\Carbon::parse($assignment_request->created_at)->diffForHumans() }}</td>
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
                                        <td class="d-flex justify-content-center">
                                            <form method="POST" action="{{ route('dispute.request.accept', [app()->getLocale(), $assignment_request->id]) }}" style="margin-right: 5px;">
                                                @csrf
                                                @METHOD('PUT')
                                                <input type="hidden" name="res" value="accepted">
                                                <i class="fas fa-check fa-fw text-success show_accept cursor-pointer" data-toggle="tooltip" title="{{ __('Accept reassignment request') }}"></i>
                                            </form>
                                            <form method="POST" action="{{ route('dispute.request.reject', [app()->getLocale(), $assignment_request->id]) }}">
                                                @csrf
                                                @METHOD('PUT')
                                                <input type="hidden" name="res" value="rejected">
                                                <i class="fas fa-times fa-fw text-danger show_reject cursor-pointer" data-toggle="tooltip" title="{{ __('Reject reassignment request') }}"></i>
                                            </form>
                                        </td>
                                        @else
                                        <td>
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
                                    <td class="p-1" colspan="10">{{ __('No requests found') }}</td>
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

    {{-- Accept --}}
    <script type="text/javascript">
        $(document).on('click', '.show_accept', function(event) {

                var form =  $(this).closest("form");

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

                var form =  $(this).closest("form");

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
