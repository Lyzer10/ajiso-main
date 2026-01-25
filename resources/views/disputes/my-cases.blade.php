@extends('layouts.base')

@php
    $title = __('My Cases')
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('My Cases') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isStaff', 'isClerk'])
                <li><a href="{{ route('staff.home', app()->getLocale()) }}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Assigned Cases') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card mt-5">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="header-title mb-0">{{ __('My Assigned Cases') }}</h4>
                    <span class="badge badge-info">{{ $totalCases ?? 0 }} {{ __('Total Cases') }}</span>
                </div>
            </div>
            <div class="card-body">
                @include('includes.errors-statuses')

                <!-- Filter and Search -->
                <form method="GET" action="{{ route('cases.my-cases', app()->getLocale()) }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search by beneficiary name...') }}" 
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>{{ __('Assigned') }}</option>
                                    <option value="proceeding" {{ request('status') === 'proceeding' ? 'selected' : '' }}>{{ __('Proceeding') }}</option>
                                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </form>

                @if ($disputes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="text-white light-custom-color">
                                <tr>
                                    <th>{{ __('S/N') }}</th>
                                    <th>{{ __('Case No') }}</th>
                                    <th>{{ __('Beneficiary') }}</th>
                                    <th>{{ __('Type of Case') }}</th>
                                    <th>{{ __('Type of Service') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Reported On') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disputes as $key => $dispute)
                                    @php
                                        $statusClass = match(strtolower(optional($dispute->disputeStatus)->dispute_status)) {
                                            'assigned' => 'badge-warning',
                                            'proceeding' => 'badge-info',
                                            'resolved' => 'badge-success',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $disputes->firstItem() + $key }}</td>
                                        <td>
                                            <strong>#{{ $dispute->dispute_no }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $beneficiary = $dispute->reportedBy;
                                                $name = trim(implode(' ', array_filter([
                                                    $beneficiary->first_name ?? '',
                                                    $beneficiary->middle_name ?? '',
                                                    $beneficiary->last_name ?? ''
                                                ])));
                                            @endphp
                                            {{ $name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ optional($dispute->typeOfCase)->type_of_case ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{ optional($dispute->typeOfService)->type_of_service ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusClass }}">
                                                {{ optional($dispute->disputeStatus)->dispute_status ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $dispute->reported_on ? \Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" 
                                                class="btn btn-sm btn-primary" title="{{ __('View Case') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $disputes->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> {{ __('No assigned cases found.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
