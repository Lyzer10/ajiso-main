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
            <div class="card mb-4">
                <div class="card-header">
                    <div class="header-title clearfix">
                        {{ __('My Reassignment Requests') }}
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
                                    <th>{{ __('Requested Assistance From') }}</th>
                                    <th>{{ __('Requested On') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($assignment_requests->count())
                                    @foreach ($assignment_requests as $assignment_request)
                                        <tr>
                                            <td>{{ '#'.$assignment_request->id }}</td>
                                            <td>
                                                <a href="{{ route('dispute.show', [app()->getLocale(), $assignment_request->dispute_id]) }}" class="text-secondary" title="{{ __('Click to view dispute') }}">
                                                    {{ $assignment_request->dispute->dispute_no }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ __($assignment_request->reason_description) }}
                                            </td>
                                            <td>
                                                @if ($assignment_request->targetStaff && $assignment_request->targetStaff->user)
                                                    {{ $assignment_request->targetStaff->user->first_name.' '
                                                        .$assignment_request->targetStaff->user->middle_name.' '
                                                        .$assignment_request->targetStaff->user->last_name
                                                    }}
                                                    @if ($assignment_request->targetStaff->center)
                                                        {{ ' | '.$assignment_request->targetStaff->center->name }}
                                                    @endif
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </td>
                                            <td>{{ Carbon\Carbon::parse($assignment_request->created_at)->diffForHumans() }}</td>
                                            <td>
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
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="p-1" colspan="6">{{ __('No requests found') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{ $assignment_requests->count() ? $assignment_requests->links() : '' }}
                </div>
            </div>

            @if (!isset($showAssignedCases) || $showAssignedCases)
                <div class="card">
                    <div class="card-header">
                        <div class="header-title clearfix">
                            {{ __('Cases Assigned To Me') }}
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <div class="table-responsive">
                            <table class="table table-striped progress-table text-center">
                                <thead class="text-capitalize text-white light-custom-color">
                                    <tr>
                                        <th>{{ __('Dispute No') }}</th>
                                        <th>{{ __('Beneficiary') }}</th>
                                        <th>{{ __('Reported On') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($assigned_disputes->count())
                                        @foreach ($assigned_disputes as $dispute)
                                            <tr>
                                                <td>{{ $dispute->dispute_no }}</td>
                                                <td>
                                                    {{ $dispute->reportedBy->first_name.' '
                                                        .$dispute->reportedBy->middle_name.' '
                                                        .$dispute->reportedBy->last_name
                                                    }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') }}</td>
                                                <td>
                                                    {{ __($dispute->disputeStatus->dispute_status ?? '') }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="p-1" colspan="5">{{ __('No assigned cases found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        {{ $assigned_disputes->count() ? $assigned_disputes->links() : '' }}
                    </div>
                </div>
            @endif
        </div>
        <!-- dispute list area end -->
    </div>
@endsection
