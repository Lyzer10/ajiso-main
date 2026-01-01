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
                        @canany(['isStaff', 'isClerk'])
                            {{ __('My Request list') }}
                        @elsecanany(['isSuperAdmin', 'isAdmin'])
                            {{ __('Reassignment Request list') }}
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
                                    <th>{{ __('Reason Description') }}</th>
                                    <th>{{ __('Staff') }}</th>
                                    <th>{{ __('Requested') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($assignment_requests->count())
                                    @foreach ($assignment_requests as $assignment_request)
                                    <tr>
                                        <td>{{ '#'.$assignment_request->id }}</td>
                                        <td>
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $assignment_request->dispute_id]) }}" class="text-secondary" title="{{  __('Click to view dispute') }}">
                                                {{ $assignment_request->dispute->dispute_no }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ __($assignment_request->reason_description) }}
                                        </td>
                                        <td>
                                            @if (is_null($assignment_request->staff_id))
                                                @canany(['isAdmin', 'isStaff', 'isSuperAdmin'])
                                                    <a href="{{ route('dispute.assign', [app()->getLocale(), $assignment_request]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
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
                                        <td>{{ Carbon\Carbon::parse($assignment_request->created_at)->diffForHumans() }}</td>
                                        <td>
                                            {{-- TODO:Add a column color scheme in status table and compare here--}}
                                            <span class="
                                                @if ( $assignment_request->request_status  === 'accepted')
                                                    text-success
                                                @elseif ( $assignment_request->request_status  === 'pending')
                                                    text-warning font-italic
                                                @else
                                                    text-danger
                                                @endif
                                            ">
                                            {{ __($assignment_request->request_status) }}
                                            </span>
                                        </td>
                                        @if ($assignment_request->request_status  === 'pending')
                                        <td class="d-flex">
                                            <form method="POST" action="{{ route('dispute.request.accept', [app()->getLocale(), $assignment_request->id]) }}">
                                                @csrf
                                                @METHOD('PUT')
                                                <input type="hidden" name="res" value="accepted">
                                                    <i class="fas fa-check fa-fw text-success" id="show_accept" data-toggle="tooltip" title="{{ __('Accept reassignment request') }}"></i>
                                            </form> /
                                            <form method="POST" action="{{ route('dispute.request.reject', [app()->getLocale(), $assignment_request->id]) }}">
                                                @csrf
                                                @METHOD('PUT')
                                                <input type="hidden" name="res" value="rejected">
                                                    <i class="fas fa-times fa-fw text-danger" id="show_reject" data-toggle="tooltip" title="{{ __('Reject reassignment request') }}"></i>
                                            </form>
                                        </td>
                                        @else
                                        <td>{{  __('N/A') }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="p-1">{{ __('No requests found') }}</td>
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
        $('#show_accept').click(function(event) {

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
        $('#show_reject').click(function(event) {

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
