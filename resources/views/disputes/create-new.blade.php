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
    <div class="row">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-10">
                @if (session('status'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>Ooops! </strong>{{ __('There were some problems with your input.') }}<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Add Disputes area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('Register Dispute')}}
                        <a href="{{ route('disputes.list', app()->getLocale()) }}"
                            class="btn btn-sm text-white light-custom-color pull-right text-white">{{ __('Disputes list') }}
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dispute.store', app()->getLocale())}}">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="beneficiary" class="font-weight-bold">{{ __('Beneficiary') }}<sup class="text-danger">*</sup></label>
                                <select id="beficiary" aria-describedby="selectbeneficiary"
                                    class="select2 select2-container--default  border-input-primary @error('beneficiary') is-invalid @enderror"
                                    name="beneficiary" required autocomplete="beneficiary" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose beneficiary') }}</option>
                                    @if ($beneficiaries->count())
                                        @foreach ($beneficiaries as $beneficiary)
                                            <option value="{{ $beneficiary->id }}" {{ old('beneficiary') == $beneficiary->id ? ' selected="selected"' : '' }}>
                                                {{ $beneficiary->user->user_no.' | '
                                                    .$beneficiary->user->first_name.' '
                                                    .$beneficiary->user->middle_name.' '
                                                    .$beneficiary->user->last_name
                                                }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No beneficiaries found') }}</option>
                                    @endif
                                </select>
                                @error('district')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="disputeNo" class="font-weight-bold">{{ __('Dispute No') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="dispute">#</span>
                                    </div>
                                    <input type="text" class="form-control border-append-primary" id="disputeNo"
                                        name="dispute_no" value="" aria-describedby="dispute" required>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="reported_on" class="font-weight-bold">{{ __('Date Reported') }}<sup class="text-danger">*</sup></label>
                                <div class="form-group">
                                    <div class="input-group date" id="reported_on" data-target-input="nearest">
                                        <input type="text" id="reportedOn"
                                            class="form-control datetimepicker-input border-prepend-primary @error('reported_on') is-invalid @enderror"
                                            name="reported_on" value="{{ old('reported_on') }}" required autocomplete="reported_on" data-target="#reported_on"
                                            data-toggle="datetimepicker" />
                                        <div class="input-group-append" data-target="#reported_on">
                                            <div class="input-group-text  border-append-primary bg-prepend-primary"><i class="fas fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                @error('reported_on')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="matter_to_court" class="font-weight-bold">{{ __('Taken the matter to court?') }}<sup class="text-danger">*</sup></label>
                                <select id="matter_to_court" aria-describedby="selectmatter_to_court"
                                    class="select2 select2-container--default border-input-primary @error('matter_to_court') is-invalid @enderror"
                                    name="matter_to_court" required autocomplete="matter_to_court" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose option') }}</option>
                                    <option value="yes" {{ old('matter_to_court') == 'yes' ? ' selected="selected"' : '' }}>{{ __('Yes') }}</option>
                                    <option value="no" {{ old('matter_to_court') == 'no' ? ' selected="selected"' : '' }}>{{ __('No') }}</option>
                                </select>
                                @error('matter_to_court')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div id="type_of_court_wrapper" style="display: none;" class="col-md-3 mb-3">
                                <label for="type_of_court"  class="font-weight-bold" >{{ __('Type of court?') }}</label>
                                <select id="type_of_court" aria-describedby="selecttype_of_court"
                                    class="select2 select2-container--default border-input-primary @error('type_of_court') is-invalid @enderror"
                                    name="type_of_court" autocomplete="type_of_court" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Select') }}</option>
                                    <option value="supreme court" {{ old('type_of_court') == 'supreme court' ? ' selected="selected"' : '' }}>{{ __('Supreme court') }}</option>
                                    <option value="land court" {{ old('type_of_court') == 'land court' ? ' selected="selected"' : '' }}>{{ __('Land court') }}</option>
                                    <option value="regional court" {{ old('type_of_court') == 'regional court' ? ' selected="selected"' : '' }}>{{ __('Regional court') }}</option>
                                    <option value="district court" {{ old('type_of_court') == 'district court' ? ' selected="selected"' : '' }}>{{ __('District court') }}</option>
                                    <option value="the court of first instance" {{ old('type_of_court') == 'the court of first instance' ? ' selected="selected"' : '' }}>{{ __('The court of first instance') }}</option>
                                     <option value="court of kadhi" {{ old('type_of_court') == 'court of kadhi' ? ' selected="selected"' : '' }}>{{ __('Court of kadhi') }}</option>
                                     <option value="social welfare/police" {{ old('type_of_court') == 'social welfare/police' ? ' selected="selected"' : '' }}>{{ __('Social welfare/police') }}</option>
                                     <option value="arbitration commission" {{ old('type_of_court') == 'arbitration commission' ? ' selected="selected"' : '' }}>{{ __('Arbitration commission') }}</option>
                                </select>
                                @error('type_of_court')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="problem_description" class="font-weight-bold">{{ __('Problem Description') }}<sup class="text-danger">*</sup></label>
                                <textarea class="form-control border-text-primary @error('problem_description') is-invalid @enderror"
                                    name="problem_description" value="{{ old('problem_description') }}" required autocomplete="problem_description" style="width: 100%;"></textarea>
                                @error('problem_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="where_reported" class="font-weight-bold">{{ __('Where did you report your problem?') }}<sup class="text-danger">*</sup></label>
                                <textarea class="form-control border-text-primary @error('where_reported') is-invalid @enderror"
                                    name="where_reported" value="{{ old('where_reported') }}" required autocomplete="where_reported" style="width: 100%;"></textarea>
                                @error('where_reported')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="service_experience" class="font-weight-bold">{{ __('Did you experience any inconvenience?') }}</label>
                                <textarea class="form-control border-text-primary @error('service_experience') is-invalid @enderror"
                                    name="service_experience" value="{{ old('service_experience') }}" autocomplete="service_experience" style="width: 100%;"></textarea>
                                @error('service_experience')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="how_can_we_help" class="font-weight-bold">{{ __('What do you want help with?') }}<sup class="text-danger">*</sup></label>
                                <textarea class="form-control border-text-primary @error('how_can_we_help') is-invalid @enderror"
                                    name="how_can_we_help" value="{{ old('how_can_we_help') }}" required autocomplete="how_can_we_help" style="width: 100%;"></textarea>
                                @error('how_can_we_help')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="how_did_they_help" class="font-weight-bold">{{ __('How did they help you?') }}</label>
                                <textarea id="how_did_they_help"  class="form-control border-text-primary @error('how_did_they_help') is-invalid @enderror"
                                    name="how_did_they_help" value="{{ old('how_did_they_help') }}"  autocomplete="how_did_they_help" style="width: 100%;"></textarea>
                                @error('how_did_they_help')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="defendant_names_addr" class="font-weight-bold">
                                    {{ __('People to be monitored or prosecuted, and addresses') }}
                                </label>
                                <textarea class="form-control border-text-primary @error('defendant_names_addr') is-invalid @enderror"
                                    name="defendant_names_addr" value="{{ old('defendant_names_addr') }}" autocomplete="defendant_names_addr" style="width: 100%;"></textarea>
                                @error('defendant_names_addr')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="type_of_service" class="font-weight-bold">{{ __('Type of Service') }}<sup class="text-danger">*</sup></label>
                                <select id="tpe_of_service" aria-describedby="selectTos"
                                    class="select2 select2-container--default   select2 select2-container--default  border-input-primary @error('type_of_service') is-invalid @enderror"
                                    name="type_of_service" required autocomplete="type_of_service" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose type of service') }}</option>
                                    @if ($type_of_services->count())
                                        @foreach ($type_of_services as $type_of_service)
                                            <option value="{{ $type_of_service->id }}" {{ old('type_of_service') == $type_of_service->id ? ' selected="selected"' : '' }}>
                                                {{ __($type_of_service->type_of_service) }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No type of service found') }}</option>
                                    @endif
                                </select>
                                @error('type_of_service')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="type_of_case" class="font-weight-bold">{{ __('Type of Case') }}<sup class="text-danger">*</sup></label>
                                <select id="typ_of_case" aria-describedby="selectToc"
                                    class="select2 select2-container--default  select2 select2-container--default  border-input-primary @error('type_of_case') is-invalid @enderror"
                                    name="type_of_case" required autocomplete="type_of_case" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose type of case') }}</option>
                                    @if ($type_of_cases->count())
                                        @foreach ($type_of_cases as $type_of_case)
                                            <option value="{{ $type_of_case->id }}" {{ old('type_of_case') == $type_of_case->id ? ' selected="selected"' : '' }}>
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
                        <div class="container">
                            <p class="form-text text-muted">
                                <i class="fas fa-exclamation-triangle fa-fw text-danger"></i>
                                <sup class="text-danger font-weight-bold">*</sup> - {{ __('These are required fields.') }}
                            </p>
                        </div>
                </div>
                <div class="card-footer">
                    <button class="btn text-white light-custom-color float-right" type="submit">{{ __('Submit') }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Disputes area end -->
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

    {{-- Autogenerate dispute no from file no --}}
   {{-- Autogenerate dispute no from file no --}}
<script type="text/javascript">
    $(function() {
        $('select#beficiary').change(function() {
            $('#disputeNo').empty();

            // Get the selected option text (e.g., "AJISO/2025/0001 | John Doe")
            var input = $(this).find(":selected").text().trim();

            // Extract the part before the "|"
            let separator = input.indexOf('|');
            let user_no = (separator !== -1) ? input.slice(0, separator).trim() : input;

            // Generate a random 3-digit number (e.g., 001–999)
            let rand_num = Math.floor(100 + Math.random() * 900); // always 3 digits

            // Combine them -> e.g. AJISO/2025/0003/123
            var dispute_no = user_no + '/' + rand_num;

            // Set the generated value into the dispute number field
            $('#disputeNo').val(dispute_no);
        });
    });
</script>

@endpush


@push('scripts')
<script>
    $(document).ready(function () {
        let $matterSelect = $('#matter_to_court');
        let $typeCourtWrapper = $('#type_of_court_wrapper');
        let $typeCourtSelect = $('#type_of_court');

        function toggleCourtField() {
            if ($matterSelect.val() === 'yes') {
                $typeCourtWrapper.show();
            } else {
                $typeCourtWrapper.hide();
                $typeCourtSelect.prop('required', false);
            }
        }

        // Run on page load (to respect old values)
        toggleCourtField();

        // Run when selection changes
        $matterSelect.on('change', toggleCourtField);
    });
</script>
@endpush
