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
            <div class="card-header d-none d-md-block">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="header-title mb-0">{{ __('My Assigned Cases') }}</h4>
                    <span class="badge badge-info">{{ $totalCases ?? 0 }} {{ __('Total Cases') }}</span>
                </div>
            </div>
            <div class="card-body">
                @include('includes.errors-statuses')

                <!-- Filter and Search -->
                <div class="disputes-mobile-header d-md-none mb-3">
                    <div class="disputes-mobile-title">{{ __('My Assigned Cases') }}</div>
                    <span class="badge badge-info">{{ $totalCases ?? 0 }} {{ __('Total Cases') }}</span>
                </div>

                @php
                    $hasFilters = request()->filled('search') || request()->filled('status');
                @endphp
                <div class="dispute-filter-toggle d-md-none">
                    <button class="btn btn-light btn-block d-flex justify-content-between align-items-center" type="button"
                        data-toggle="collapse" data-target="#myCasesFilters" aria-expanded="{{ $hasFilters ? 'true' : 'false' }}"
                        aria-controls="myCasesFilters">
                        <span>{{ __('Search Filters') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div id="myCasesFilters" class="collapse d-md-none {{ $hasFilters ? 'show' : '' }}">
                    <form method="GET" action="{{ route('cases.my-cases', app()->getLocale()) }}" class="mt-3 dispute-filter">
                        <div class="form-row align-items-end">
                            <div class="col-lg-6 mb-3">
                                <label class="font-weight-bold">{{ __('Search by beneficiary name') }}</label>
                                <input type="text" name="search" class="form-control border-input-primary dispute-filter__control" placeholder="{{ __('Search by beneficiary name...') }}"
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="font-weight-bold">{{ __('Status') }}</label>
                                <select name="status" class="form-control border-input-primary dispute-filter__control">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>{{ __('Assigned') }}</option>
                                    <option value="proceeding" {{ request('status') === 'proceeding' ? 'selected' : '' }}>{{ __('Proceeding') }}</option>
                                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Filter') }}</button>
                            <a href="{{ route('cases.my-cases', app()->getLocale()) }}" class="btn btn-light btn-sm">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </div>

                <div class="d-none d-md-block">
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
                </div>

                @if ($disputes->count() > 0)
                    <div class="disputes-mobile-list d-md-none mt-3">
                        <div class="disputes-mobile-cards">
                            @foreach ($disputes as $dispute)
                                @php
                                    $statusSlug = \Illuminate\Support\Str::slug(optional($dispute->disputeStatus)->dispute_status ?? '');
                                    $beneficiary = $dispute->reportedBy;
                                    $name = trim(implode(' ', array_filter([
                                        $beneficiary->first_name ?? '',
                                        $beneficiary->middle_name ?? '',
                                        $beneficiary->last_name ?? ''
                                    ])));
                                @endphp
                                <div class="dispute-mobile-card">
                                    <div class="dispute-mobile-card__top">
                                        <div class="dispute-mobile-card__title">{{ $dispute->dispute_no }}</div>
                                        <div class="dropdown dispute-mobile-menu">
                                            <button class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}">
                                                    <i class="fas fa-eye text-primary"></i>
                                                    {{ __('View') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dispute-mobile-card__name">{{ $name ?? 'N/A' }}</div>
                                    <div class="dispute-mobile-card__meta">
                                        <span class="dispute-mobile-card__case">{{ optional($dispute->typeOfCase)->type_of_case ?? 'N/A' }}</span>
                                        <span class="badge-status status-{{ $statusSlug }}">{{ optional($dispute->disputeStatus)->dispute_status ?? 'N/A' }}</span>
                                        <span class="dispute-mobile-card__date">{{ $dispute->reported_on ? \Carbon\Carbon::parse($dispute->reported_on)->format('Y-m-d') : 'N/A' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="report-pagination">
                            {{ $disputes->links() }}
                        </div>
                    </div>
                    <div class="table-responsive d-none d-md-block">
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
                    <div class="d-flex justify-content-center mt-3 d-none d-md-flex">
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
