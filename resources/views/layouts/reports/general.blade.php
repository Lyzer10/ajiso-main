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
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="header-title bg-info p-2 w-100 text-white">
                                    {{ __('Results Summary')}}
                                </h4>
                                <div class="h6">
                                    <span class="font-weight-bold">{{ __('Dates') }}</span> :
                                </div>
                                <div class="h6">
                                    {{ $date_raw ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row px-3 mb-2">
                                    <div class="h6">
                                        <span class="font-weight-bold">{{ __('Filter') }}</span> :
                                        {{ __($filter_by) ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="row px-3 mb-2">
                                    <div class="h6">
                                        <span class="font-weight-bold">{{ __('Query') }}</span> :
                                        {{ $filter_val ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row px-3 mb-2">
                                    <div class="h6">
                                        <span class="font-weight-bold">{{ __('Disputes Found') }}</span>
                                        <span class="text-primary">{{ ': '.$disputes->count() ?? '0' }}</span>
                                    </div>
                                </div>
                                <div class="row px-3 mb-2">
                                    <div class="h6">
                                        <span class="font-weight-bold">{{ __('Resolved Disputes') }}</span>
                                        <span class="text-success">{{ ': '.$resolved_count ?? '0' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="dropdown">
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
                                            @if (is_null($dispute->staff_id))
                                                <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                    {{ __('Unassigned') }}
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
                                            {{-- TODO:Add a column color scheme in status table and compare here--}}
                                            <span class="
                                                @if ( $dispute->disputeStatus->dispute_status  === 'resolved')
                                                    text-success
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'pending')
                                                    text-warning font-italic
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'proceeding')
                                                    text-primary
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'continue')
                                                    text-info
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'referred')
                                                    text-secondary
                                                @else
                                                    text-danger
                                                @endif
                                            ">
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
