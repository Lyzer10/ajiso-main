
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
            <li><span>{{ __('Add Disputes') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <!-- Add Disputes area end -->
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- Add Dispute area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('Add Dispute Details') }}
                        <a href="{{ route('dispute.select.archive', app()->getLocale()) }}" class="btn btn-sm light-custom-color pull-right text-white">
                            {{ __('Back') }}
                        </a>
                    </h4>
                </div>
                @if ($dispute->count())
                <div class="card-body">
                    <form method="POST" action="{{ route('dispute.store.archive', app()->getLocale()) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card card-bordered">
                                    <div class="card-body">
                                        <h6 class="card-text mb-3 font-weight-bold">{{ __('Continue Details') }}</h6>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="disputeNo" class="font-weight-bold">{{ __('Dispute No') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="dispute">#</span>
                                                        </div>
                                                        <input type="text" class="form-control border-append-primary" id="disputeNo"
                                                            value="{{ $dispute->dispute_no }}" name="dispute_no" readonly aria-describedby="dispute" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="beneficiary" class="font-weight-bold">{{ __('Beneficiary') }}<sup class="text-danger">*</sup></label>
                                                    <select id="beneficiary" aria-describedby="selectbeneficiary"
                                                        class="select2 select2-container--default   border-input-primary @error('beneficiary') is-invalid @enderror"
                                                        name="beneficiary" required autocomplete="beneficiary" style="width: 100%;">
                                                        <option hidden disabled selected value>{{ __('Choose beneficiary') }}</option>
                                                        <option  selected="selected" value="{{  $dispute->beneficiary_id }}">
                                                            {{ $dispute->reportedBy->user_no.' | '
                                                                .$dispute->reportedBy->first_name.' '
                                                                .$dispute->reportedBy->middle_name.' '
                                                                .$dispute->reportedBy->last_name
                                                            }}
                                                            </option>
                                                    </select>
                                                    @error('beneficiary')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="problem_description" class="font-weight-bold">{{ __('Problem Description') }}<sup class="text-danger">*</sup></label>
                                                    <textarea class="form-control border-text-primary @error('problem_description') is-invalid @enderror"
                                                        name="problem_description" required autocomplete="problem_description" style="width: 100%;">{{ $dispute->problem_description }}</textarea>
                                                    @error('problem_description')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="type_of_service" class="font-weight-bold">{{ __('Type of Service') }}<sup class="text-danger">*</sup></label>
                                                    <select id="type_of_service" aria-describedby="selectTos"
                                                        class="select2 select2-container--default   border-input-primary @error('type_of_service') is-invalid @enderror"
                                                        name="type_of_service" required autocomplete="type_of_service" style="width: 100%;">
                                                        <option hidden disabled selected value>{{ __('Choose type of service') }}</option>
                                                        @if ($type_of_services->count())
                                                            @foreach ($type_of_services as $type_of_service)
                                                                <option value="{{ $type_of_service->id }}"
                                                                    @if ($type_of_service->id === $dispute->type_of_service_id)
                                                                            selected="selected"
                                                                        @endif
                                                                    >
                                                                    {{ __($type_of_service->type_of_service) }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            <option>{{ __('No type of services found') }}</option>
                                                        @endif
                                                    </select>
                                                    @error('type_of_service')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="type_of_case" class="font-weight-bold">{{ __('Type of Case') }}<sup class="text-danger">*</sup></label>
                                                    <select id="type_of_case" aria-describedby="selectToc"
                                                        class="select2 select2-container--default   border-input-primary @error('type_of_case') is-invalid @enderror"
                                                        name="type_of_case" required autocomplete="type_of_case" style="width: 100%;">
                                                        <option hidden disabled selected value>{{ __('Choose type of case') }}</option>
                                                        @if ($type_of_cases->count())
                                                            @foreach ($type_of_cases as $type_of_case)
                                                                <option value="{{ $type_of_case->id }}"
                                                                    @if ($type_of_case->id === $dispute->type_of_case_id)
                                                                            selected="selected"
                                                                        @endif
                                                                    >
                                                                    {{ __($type_of_case->type_of_case) }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            <option>{{ __('No type of case found') }}</option>
                                                        @endif
                                                    </select>
                                                    @error('type_of_case')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="matter_to_court" class="font-weight-bold">{{ __('Have you taken the matter to court?') }}<sup class="text-danger">*</sup></label>
                                                    <select id="matter_to_court" aria-describedby="selectmatter_to_court"
                                                        class="select2 select2-container--default   border-input-primary @error('matter_to_court') is-invalid @enderror"
                                                        name="matter_to_court" required autocomplete="matter_to_court" style="width: 100%;">
                                                        <option hidden disabled selected value>{{ __('Choose option') }}</option>
                                                        <option value="yes" @if ($dispute->matter_to_court === 'yes') selected="selected" @endif>{{ __('Yes') }}</option>
                                                        <option value="no" @if ($dispute->matter_to_court === 'no') selected="selected" @endif>{{ __('No') }}</option>
                                                    </select>
                                                    @error('matter_to_court')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="where_reported" class="font-weight-bold">{{ __('Where did you report your problem?') }}<sup class="text-danger">*</sup></label>
                                                    <textarea class="form-control border-text-primary @error('where_reported') is-invalid @enderror"
                                                        name="where_reported" required autocomplete="where_reported" style="width: 100%;">{{ $dispute->where_reported }}</textarea>
                                                    @error('where_reported')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="how_did_they_help" class="font-weight-bold">{{ __('How did they help you?') }}</label>
                                                    <textarea id="how_did_they_help"  class="form-control border-text-primary @error('how_did_they_help') is-invalid @enderror"
                                                        name="how_did_they_help" autocomplete="how_did_they_help" style="width: 100%;">{{ $dispute->how_did_they_help }}</textarea>
                                                    @error('how_did_they_help')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="service_experience" class="font-weight-bold">{{ __('Did you experience any inconvinience?') }}</label>
                                                    <textarea class="form-control border-text-primary @error('service_experience') is-invalid @enderror"
                                                        name="service_experience" autocomplete="service_experience" style="width: 100%;">{{ $dispute->service_experience }}</textarea>
                                                    @error('service_experience')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-bordered">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="reported_on" class="font-weight-bold">{{ __('Date Reported') }}</label>
                                                <div class="form-group">
                                                    <div class="input-group date" id="reported_on" data-target-input="nearest">
                                                        <input type="text" id="reportedOn"
                                                            class="form-control datetimepicker-input border-prepend-primary @error('reported_on') is-invalid @enderror"
                                                            name="reported_on" value="{{ Carbon\Carbon::parse($dispute->reported_on)->format('m/d/Y') }}" autocomplete="reported_on" 
                                                            data-target="#reported_on"
                                                            data-toggle="datetimepicker"/>
                                                        <div class="input-group-append" data-target="#reported_on">
                                                            <div class="input-group-text  border-append-primary bg-prepend-primary">
                                                                <i class="fas fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('reported_on')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="how_can_we_help" class="font-weight-bold">{{ __('What do you want help with?') }}<sup class="text-danger">*</sup></label>
                                                <textarea class="form-control border-text-primary @error('how_can_we_help') is-invalid @enderror"
                                                    name="how_can_we_help" required autocomplete="how_can_we_help" style="width: 100%;">{{ $dispute->how_can_we_help }}</textarea>
                                                @error('how_can_we_help')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-12 mb-3">
                                                <label for="defendant_names_addr" class="font-weight-bold">
                                                    {{ __('People to be monitored or prosecuted, and addresses') }}
                                                </label>
                                                <textarea class="form-control border-text-primary @error('defendant_names_addr') is-invalid @enderror"
                                                    name="defendant_names_addr" autocomplete="defendant_names_addr" style="width: 100%;">{{ $dispute->defendant_names_addr }}</textarea>
                                                @error('defendant_names_addr')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class=" btn btn-primary float-right">{{ __('Submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                        <!-- Add Dispute area end -->
                        @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('dates.js')

    {{-- Select2 --}}
    <script type="text/javascript">
        $(function() {
            $('.select2').select2();
        });
    </script>

    {{-- datetimepicker initializer --}}
    <script type="text/javascript">
        $(function() {
            $('#reported_on').datetimepicker({
                format: 'L',
                viewMode: 'years'
            });

        });
    </script>
@endpush