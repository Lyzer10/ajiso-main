@extends('layouts.base')

@php
    $title = __('Client Cases');
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Client Cases') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Client Cases') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        {{ __('Client Cases') }}
                        <form method="GET" action="{{ route('reports.client.cases', [app()->getLocale()]) }}" class="d-flex align-items-center pull-right mb-2">
                            <div class="d-flex align-items-center mr-4">
                                <select name="type_of_case_id" class="form-control form-control-sm mr-2" style="min-width: 220px;">
                                    <option value="">{{ __('All Case Types') }}</option>
                                    @foreach ($type_of_cases as $caseType)
                                        <option value="{{ $caseType->id }}" {{ (string) request('type_of_case_id') === (string) $caseType->id ? 'selected' : '' }}>
                                            {{ $caseType->type_of_case }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search client') }}" class="form-control form-control-sm mr-2 border-prepend-black p-2">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table progress-table text-center table-striped">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('S/N') }}</th>
                                    <th>{{ __('Client') }}</th>
                                    <th>{{ __('Case Type') }}</th>
                                    <th>{{ __('Service Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date Reported') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($disputes as $dispute)
                                    @php
                                        $client = $dispute->reportedBy ?? null;
                                        $clientName = $client
                                            ? trim($client->first_name.' '.$client->middle_name.' '.$client->last_name)
                                            : 'N/A';
                                    @endphp
                                    <tr>
                                        <td>{{ $disputes->firstItem() + $loop->index }}</td>
                                        <td>{{ $clientName }}</td>
                                        <td>{{ $dispute->typeOfCase->type_of_case ?? 'N/A' }}</td>
                                        <td>{{ $dispute->typeOfService->type_of_service ?? 'N/A' }}</td>
                                        <td>{{ $dispute->disputeStatus->dispute_status ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') }}</td>
                                        <td>{{ $dispute->dispute_no }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="p-1">{{ __('No cases found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $disputes->count() ? $disputes->appends(request()->query())->links() : '' }}
                </div>
            </div>
        </div>
    </div>
@endsection
