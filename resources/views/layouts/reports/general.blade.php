@extends('layouts.base')

@php
    $title = __('Reports') 
@endphp
@section('title', 'LAIS | '.$title)

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
                @yield('subsection')
            </div>
            <div class="card">
                @if ($disputes->count())
                    @php
                        $filter_by = $filter_by ?? 'N/A';
                        $filter_val = $filter_val ?? 'All';
                        $date_raw = $date_raw ?? __('All time');
                        $summaryCollection = method_exists($disputes, 'getCollection') ? $disputes->getCollection() : $disputes;
                        $statusCounts = isset($statusCounts)
                            ? collect($statusCounts)
                            : $summaryCollection->groupBy('dispute_status_id')->map->count();
                        $totalCases = $totalCases ?? $summaryCollection->count();
                        $isParalegalView = auth()->user() && auth()->user()->can('isClerk');
                        $excludedStatusSlugs = $isParalegalView
                            ? ['judged', 'discontinued', 'discontinue', 'pending']
                            : [];
                        $summaryToneMap = [
                            'judged' => 'summary-card--slate',
                            'resolved' => 'summary-card--red',
                            'continue' => 'summary-card--amber',
                            'continued' => 'summary-card--amber',
                            'referred' => 'summary-card--green',
                            'discontinued' => 'summary-card--teal',
                            'discontinue' => 'summary-card--teal',
                            'pending' => 'summary-card--orange',
                            'proceeding' => 'summary-card--purple',
                        ];
                        $filteredDisputeStatuses = $dispute_statuses;
                        if ($excludedStatusSlugs) {
                            $filteredDisputeStatuses = $dispute_statuses->reject(function ($status) use ($excludedStatusSlugs) {
                                return in_array(\Illuminate\Support\Str::slug($status->dispute_status), $excludedStatusSlugs, true);
                            })->values();
                        }
                    @endphp
                    <div class="card-header">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h4 class="header-title mb-0">{{ __('General Summary') }}</h4>
                            <div class="dropdown mt-2 mt-md-0">
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
                        <div class="summary-grid mt-3">
                            <div class="summary-card summary-card--brand summary-card--tone">
                                <div class="summary-card__label">{{ __('Total Cases') }}</div>
                                <div class="summary-card__value">{{ $totalCases }}</div>
                            </div>
                            @foreach ($filteredDisputeStatuses as $dispute_status)
                                @php
                                    $statusSlug = \Illuminate\Support\Str::slug($dispute_status->dispute_status);
                                    $paletteClass = $summaryToneMap[$statusSlug] ?? 'summary-card--blue';
                                @endphp
                                <div class="summary-card summary-card--tone {{ $paletteClass }}">
                                    <div class="summary-card__label">{{ __($dispute_status->dispute_status) }}</div>
                                    <div class="summary-card__value">{{ $statusCounts->get($dispute_status->id, 0) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <div class="table-responsive table-striped">
                            <table class="table progress-table text-center" id="dataTable">
                                <thead class="text-capitalize bg-primary">
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
                                    @foreach ($disputes ?? '' as $dispute)
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
                                            @php
                                                $paralegalUser = $dispute->paralegalUser;
                                            @endphp
                                            @if (is_null($dispute->staff_id) && is_null($dispute->paralegal_user_id))
                                                <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                    {{ __('Unassigned') }}
                                                </a>
                                            @elseif ($paralegalUser)
                                                <a href="{{ route('user.show', [app()->getLocale(), $paralegalUser->id]) }}" title="{{ __('Click to view paralegal') }}">
                                                    {{ __('Paralegal') }}: {{ $paralegalUser->first_name.' '
                                                        .$paralegalUser->middle_name.' '
                                                        .$paralegalUser->last_name
                                                    }}
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
                                            @php
                                                $statusSlug = \Illuminate\Support\Str::slug($dispute->disputeStatus->dispute_status);
                                            @endphp
                                            <span class="badge-status status-{{ $statusSlug }}">
                                                {{ $dispute->disputeStatus->dispute_status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                {{ __('No Results Found') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection
