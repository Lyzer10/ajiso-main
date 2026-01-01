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
                <div class="card-body"style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table table-striped progress-table text-center">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Reason Description') }}</th>
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
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $assignment_request->dispute_id]) }}" class="text-secondary" title="{{  __('Click to view dispute') }}">
                                                {{ $assignment_request->dispute->dispute_no }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ __($assignment_request->reason_description) }}
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($assignment_request->created)->diffForHumans() }}</td>
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