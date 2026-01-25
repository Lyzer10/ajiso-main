@extends('layouts.base')

@php
    $title = __('Dashboard') 
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Dashboard') }}</h4>
        <ul class="breadcrumbs pull-left">
            <li><a href="{{ route('staff.home', app()->getLocale()) }}">{{__('Home') }}</a></li>
            <li><span>{{ __('Legal Aid Provider') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- seo fact area start -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-4 mt-5 mb-3">
                <a href="{{ route('cases.my-cases', app()->getLocale()) }}" style="text-decoration: none; color: inherit;">
                    <div class="card cursor-pointer">
                        <div class="seo-fact sbg1">
                            <div class="p-4 d-flex justify-content-between align-items-center">
                                <div class="seofct-icon"><i class="fas fa-archive"></i>{{ __('Assigned Disputes') }}</div>
                                <h2>{{ $dispute_total ?? 0 }}</h2>
                            </div>
                            <canvas id="" height="50"></canvas>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mt-md-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg2">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="fas fa-check-double"></i>{{ __('Resolved Disputes') }}</div>
                            <h2>{{ $dispute_resolved ?? 0 }}</h2>
                        </div>
                        <canvas id="" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-md-5 mb-3">
                <div class="card">
                    <div class="seo-fact sbg4">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <div class="seofct-icon"><i class="fas fa-sync-alt"></i>{{ __('Proceeding Disputes') }} </div>
                            <h2>{{ $dispute_proceed ?? 0 }}</h2>
                        </div>
                        <canvas id="" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- seo fact area end -->
    <!-- Cases area start -->
    <div class="col-lg-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ __('My Dispute List') }}
                    <a href="{{ route('cases.my-cases', app()->getLocale()) }}" 
   class="btn btn-sm text-white light-custom-color pull-right text-white">
    {{ __('View All My Cases') }}
</a>

                </h4>
            </div>
            <div class="card-body">
                <!-- display disputes by id of the current legal aid provider-->
                <div class="table-responsive">
                    <table class="table table-striped progress-table text-center">
                        <thead class="text-capitalize text-white light-custom-color">
                            <tr>
                                <th>S/N</th>
                                <th>{{ __('Dispute No') }}</th>
                                <th>{{ __('Beneficiary') }}</th>
                                <th>{{ __('Type of Case') }}</th>
                                <th>{{ __('Dispute Status') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($disputes->count())
                                @foreach ($disputes as $dispute)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}">
                                            {{ $dispute->dispute_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                                        {{ $dispute->reportedBy->first_name.' '
                                            .$dispute->reportedBy->middle_name.' '
                                            .$dispute->reportedBy->last_name
                                        }}
                                    </a>
                                </td>
                                    <td>{{ $dispute->typeOfService->type_of_service }}</td>
                                    <td>{{ $dispute->typeOfCase->type_of_case }}</td>
                                    <td>
                                        @php
                                            $statusSlug = \Illuminate\Support\Str::slug($dispute->disputeStatus->dispute_status);
                                        @endphp
                                        <span class="badge-status status-{{ $statusSlug }}">
                                            {{ $dispute->disputeStatus->dispute_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" title="{{ __('Edit Dispute') }}">
                                            <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                        </a> /
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" title="{{ __('View Dispute') }}">
                                            <i class="fas fa-eye fa-fw text-success"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="p-1" colspan="7">{{ __('No disputes associated to legal aid provider found') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Case Types area end -->
</div>
@endsection                                <th>{{ __('Dispute Status') }}</th>
                                <th>{{ __('Duration') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($disputes->count())
                                @foreach ($disputes as $dispute)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}">
                                            {{ $dispute->dispute_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                                        {{ $dispute->reportedBy->first_name.' '
                                            .$dispute->reportedBy->middle_name.' '
                                            .$dispute->reportedBy->last_name
                                        }}
                                    </a>
                                </td>
                                    <td>{{ $dispute->typeOfService->type_of_service }}</td>
                                    <td>{{ $dispute->typeOfCase->type_of_case }}</td>
                                    <td>
                                        @php
                                            $statusSlug = \Illuminate\Support\Str::slug($dispute->disputeStatus->dispute_status);
                                        @endphp
                                        <span class="badge-status status-{{ $statusSlug }}">
                                            {{ $dispute->disputeStatus->dispute_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" title="{{ __('Edit Dispute') }}">
                                            <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                        </a> /
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" title="{{ __('View Dispute') }}">
                                            <i class="fas fa-eye fa-fw text-success"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="p-1" colspan="7">{{ __('No disputes associated to legal aid provider found') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Case Types area end -->
</div>
@endsection
