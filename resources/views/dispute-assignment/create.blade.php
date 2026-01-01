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
            <li><span>{{ __('Dispute Reassignment Request') }}</span></li>
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
                    <h4 class="header-title">{{ __('Dispute Reassignment Request') }}
                        <a href="{{ route('disputes.my.list', [app()->getLocale(), auth()->user()->staff->id]) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                            {{ __('Back') }}
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dispute.request.store', app()->getLocale()) }}">
                        @csrf
                        @METHOD('POST')
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
                                                .$dispute->reportedBy->last_name
                                                }}
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
                                                    .$dispute->reportedBy->last_name
                                                }}
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
                            <div class="col-md-5 mb-2">
                                <label for="reason_description" class="font-weight-bold">{{ __('Request Reason') }}<sup class="text-danger">*</sup></label>
                                <textarea name="reason_description" id="reason_description" class="form-control border-text-primary" placeholder="{{  __('Describe request reason here...') }}" style="height: 130px;"></textarea>
                                @error('reason_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1 mt-4 pt-1">
                                <button class="btn text-white light-custom-color btn-rounded float-right" type="submit">
                                    {{ __('Send') }}
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