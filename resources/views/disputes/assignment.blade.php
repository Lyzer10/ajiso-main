@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Disputes') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Dispute Assignment') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- Assign Disputes area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('Dispute assignment') }}
                        <a href="{{ route('disputes.list', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                            {{ __('Back') }}
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dispute.assign.bind', app()->getLocale()) }}">
                        @csrf
                        @METHOD('PATCH')
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="dispute" class="font-weight-bold">{{ __('Dispute') }}<sup class="text-danger">*</sup></label>
                                <select id="dispute" aria-describedby="selectDispute"
                                    class="select2 select2-container--default border-input-primary @error('dispute') is-invalid @enderror"
                                    name="dispute" required autocomplete="dispute" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose dispute') }}</option>
                                    @if ($dispute->count())
                                        <option value="{{ $dispute->id }}" selected="selected">
                                            {{ __('Dispute').' #'
                                                .$dispute->dispute_no.' | '
                                                .$dispute->reported_on.' >> '
                                                .__('Beneficiary').' #'
                                                .$dispute->reportedBy->user_no.' | '
                                                .$dispute->reportedBy->first_name.' '
                                                .$dispute->reportedBy->middle_name.' '
                                                .$dispute->reportedBy->last_name.' >>'
                                                }}
                                                @if (is_null($dispute->staff_id))
                                                    {{ __('UNASSIGNED') }}
                                                @else
                                                    {{ __('ASSIGNED') }}
                                                @endif
                                        </option>
                                    @elseif ($disputes->count())
                                        @foreach ($disputes as $dispute)
                                            <option value="{{ $dispute->id }}">
                                                {{ __('Dispute').' #'
                                                    .$dispute->dispute_no.' | '
                                                    .$dispute->reported_on.' >> '
                                                    .__('Beneficiary').' #'
                                                    .$dispute->reportedBy->user_no.' | '
                                                    .$dispute->reportedBy->first_name.' '
                                                    .$dispute->reportedBy->middle_name.' '
                                                    .$dispute->reportedBy->last_name.' >>'
                                                }}
                                                @if (is_null($dispute->staff_id))
                                                    {{ __('UNASSIGNED') }}
                                                @else
                                                {{ __('ASSIGNED') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No disputes found') }}</option>
                                    @endif
                                </select>
                                @error('dispute')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1 mb-2">
                                <i class="fas fa-link fa-fw fa-2x pt-4 mt-2 text-success"></i>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="staff" class="font-weight-bold">{{ __('Legal Aid Provider') }}<sup class="text-danger">*</sup></label>
                                <select id="staff" aria-describedby="selectStaff"
                                    class="select2 select2-container--default  border-input-primary @error('staff') is-invalid @enderror"
                                    name="staff" required autocomplete="staff" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose legal aid provider') }}</option>
                                    <option value="null">{{ __('Unassign') }}</option>
                                    @if ($staff->count())
                                        @foreach ($staff as $staf)
                                            <option value="{{ $staf->id }}">
                                                {{ $staf->user->first_name.' '
                                                    .$staf->user->middle_name.' '
                                                    .$staf->user->last_name.' | '
                                                    .$staf->office
                                                }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No legal aid providers found') }}</option>
                                    @endif
                                </select>
                                @error('staff')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1 mt-4 pt-1">
                                <button class="btn text-white light-custom-color btn-rounded float-right" type="submit">
                                    {{ __('Assign') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Assign Disputes area end -->
@endsection

@push('scripts')
    @include('dates.js')

    {{-- Select2 --}}
    <script type="text/javascript">
        $(function() {
            $('.select2').select2();
        });
    </script>
@endpush
