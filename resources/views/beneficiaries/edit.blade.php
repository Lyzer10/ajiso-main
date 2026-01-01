@extends('layouts.base')

@php
    $title = __('Beneficiaries') 
@endphp
@section('title', 'LAIS | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Beneficiaries') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Edit Beneficiary') }}</span></li>
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
                            <strong>Ooops!</strong> {{ __('Something went wrong!') }}<br><br>
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
        <!-- Edit Beneficiary area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('Edit Beneficiary') }}
                        <a href="{{ route('beneficiaries.list', app()->getLocale())}}"
                            class="btn btn-sm text-white light-custom-color pull-right">{{ _('Beneficiaries List') }}
                        </a>
                    </h4>
                </div>
                @if ($beneficiary->count())
                    <div class="card-body">
                        <form method="POST" action="{{ route('beneficiary.update', [app()->getLocale(), $beneficiary->id]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @php
                                $user = $beneficiary->user;
                            @endphp
                            <fieldset class="form-group border p-2">
                                <legend class="w-auto pl-2 h6 font-weight-bold">{{ __('~ Personal Info') }}</legend>
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label for="user_no" class="font-weight-bold">{{ __('File No') }}<sup class="text-danger">*</sup></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="inputGroupUserNo">#</span>
                                            </div>
                                            <input readonly type="text" class="form-control border-append-primary" id="user_no" placeholder="User Id"
                                                value="{{ $user->user_no }}" aria-describedby="inputGroupUser" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                    </span>
                                        <label for="username" class="font-weight-bold">{{ __('Username') }}<sup class="text-danger">*</sup></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="inputGroupUserName">@</span>
                                            </div>
                                            <input type="text" id="username" placeholder="Username" aria-describedby="inputGroupUserName"
                                                class="form-control border-append-primary @error('name') is-invalid @enderror" name="name"
                                                value="{{ $user->name }}" required autocomplete="name" autofocus>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="selectDesignation font-weight-bold">{{ __('Designation / Title') }}<sup class="text-danger">*</sup></label>
                                        <select id="designation" aria-describedby="selectDesignation"
                                            class="select2 select2-container--default   border-input-primary @error('designation') is-invalid @enderror"
                                            name="designation" required autocomplete="designation" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose designation / title') }}</option>
                                            @if ($designations->count())
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}"
                                                    @if ($designation->id === $user->designation->id)
                                                        selected="selected"
                                                    @endif
                                                    >{{ __($designation->designation) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option>{{ __('No designations found') }}</option>
                                            @endif
                                        </select>
                                        @error('designation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="firstName" class="font-weight-bold">{{ __('First Name') }}<sup class="text-danger">*</sup></label>
                                        <input type="text" id="firstName" placeholder="{{ __('First Name') }}"
                                            class="form-control  border-input-primary @error('first_name') is-invalid @enderror"
                                            name="first_name" value="{{ $user->first_name }}" required autocomplete="first_name">
                                        @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label for="middleName" class="font-weight-bold">{{ __('Middle Name (optional)') }}</label>
                                        <input type="text" id="middleName" placeholder="{{ __('Middle Name (optional)') }}"
                                            class="form-control  border-input-primary @error('middle_name') is-invalid @enderror"
                                            name="middle_name" value="{{ $user->middle_name ?? '' }}" autocomplete="middle_name">
                                        @error('middle_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="lastName" class="font-weight-bold">{{ __('Last Name') }}<sup class="text-danger">*</sup></label>
                                        <input type="text" id="lastName" placeholder="{{ __('Last Name') }}"
                                            class="form-control  border-input-primary @error('last_name') is-invalid @enderror"
                                            name="last_name" value="{{ $user->last_name }}" required autocomplete="last_name">
                                        @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="selectGender font-weight-bold">{{ __('Gender') }}<sup class="text-danger">*</sup></label>
                                        <select id="gender" aria-describedby="selectGender"
                                            class="select2 select2-container--default   border-input-primary @error('gender') is-invalid @enderror"
                                            name="gender" required autocomplete="gender" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose gender') }}</option>
                                            @if ($beneficiary->gender === "male")
                                                <option value = "{{ $beneficiary->gender }}" selected="selected">{{ __('Male') }}</option>
                                                <option value="female">{{ __('Female') }}</option>
                                                <option value="other">{{ __('Other') }}</option>
                                            @elseif ($beneficiary->gender === "female")
                                                <option value="female">{{ __('Male') }}</option>
                                                <option value="{{ $beneficiary->gender }}" selected="selected"> {{ __('Female') }}</option>
                                                <option value="other">{{ __('Other') }}</option>
                                            @else
                                                <option value="female">{{ __('Male') }}</option>
                                                <option value="{{ $beneficiary->gender }}"> {{ __('Female') }}</option>
                                                <option value="other" selected="selected">{{ __('Other') }}</option>
                                            @endif
                                        </select>
                                        @error('gender')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="age" class="font-weight-bold">{{ __('Age') }}</label>
                                        <div class="form-group">
                                            <input type="text" id="age"
                                                class="form-control border-prepend-primary @error('age') is-invalid @enderror"
                                                name="age" value="{{ $beneficiary->age }}" placeholder="{{ __('Eg. 24') }}" required autocomplete="age"/>
                                        </div>
                                        @error('age')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                     <div class="col-md-3 mb-3">
                                        <label class="selectGender font-weight-bold">{{ __('Disabled') }}</label>
                                        <select id="disabled" aria-describedby="selectDisabled"
                                            class="select2 select2-container--default   border-input-primary @error('disabled') is-invalid @enderror"
                                            name="disabled" required autocomplete="disabled" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Select') }}</option>
                                            @if ($beneficiary->disabled === "yes")
                                                <option value = "{{ $beneficiary->gender }}" selected="selected">{{ __('Yes') }}</option>
                                                <option value="no">{{ __('No') }}</option>
                                            @elseif ($beneficiary->disabled === "no")
                                                <option value="no">{{ __('Yes') }}</option>
                                                <option value="{{ $beneficiary->gender }}" selected="selected"> {{ __('No') }}</option>
                                                
                                            @endif
                                        </select>
                                        @error('gender')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="selectEducation font-weight-bold">{{ __('Education Level') }}<sup class="text-danger">*</sup></label>
                                        <select id="education_level" aria-describedby="selectEducation"
                                            class="select2 select2-container--default   border-input-primary @error('education_level') is-invalid @enderror"
                                            name="education_level" required autocomplete="education_level" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose education level')}}</option>
                                            @if ($education_levels->count())
                                                @foreach ($education_levels as $education_level)
                                                    <option value="{{ $education_level->id }}"
                                                    @if ($education_level->id === $beneficiary->educationLevel->id)
                                                        selected="selected"
                                                    @endif
                                                    >{{ __($education_level->education_level) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option>{{ __('No education levels found') }}</option>
                                            @endif
                                        </select>
                                        @error('education_level')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="tel_no" class="font-weight-bold">{{ __('Telephone No') }}<sup class="text-danger">*</sup></label>
                                        <input type="tel" id="tel_no" placeholder="{{ __('Telephone number') }}"
                                            class="form-control  border-input-primary @error('tel_no') is-invalid @enderror"
                                            name="tel_no" value="{{ $user->tel_no }}" required autocomplete="tel_no" style="width: 100%;">
                                        @error('tel_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="mobile_no" class="font-weight-bold">{{ __('Telephone No 2 (optional)') }}</label>
                                        <input type="tel" id="mobile_no" placeholder="{{ __('Telephone Number') }}"
                                            class="form-control  border-input-primary @error('mobile_no') is-invalid @enderror"
                                            name="mobile_no" value="{{ $user->mobile_no ?? 0 }}" required autocomplete="mobile_no" style="width: 100%;">
                                            <small class="form-text text-muted">
                                                <i class="fas fa-exclamation-circle text-info"></i>
                                                {{ __('Format: 0712345678') }}
                                            </small>
                                        @error('mobile_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label for="region" class="font-weight-bold">{{ __('Region') }}<sup class="text-danger">*</sup></label>
                                        <input readonly type="text" id="region" placeholder="Region"
                                            class="form-control  border-input-primary @error('region') is-invalid @enderror"
                                            name="region" value="{{ $beneficiary->region->region }}" required autocomplete="region" style="width: 100%;">
                                            <small class="form-text text-muted">
                                                <i class="fas fa-exclamation-circle text-info"></i>
                                                {{ __('Disabled: Auto updates on editing district') }}
                                            </small>
                                        </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="selectDistrict font-weight-bold">{{ __('District') }}<sup class="text-danger">*</sup></label>
                                        <select id="district" aria-describedby="selectDistrict"
                                            class="select2 select2-container--default   border-input-primary @error('district') is-invalid @enderror"
                                            name="district" required autocomplete="district" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose district') }}</option>
                                            @if ($districts->count())
                                                @foreach ($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                    @if ($district->id === $beneficiary->district->id)
                                                        selected="selected"
                                                    @endif
                                                    >{{ __($district->district) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option>{{ __('No districts found') }}</option>
                                            @endif
                                        </select>
                                        @error('district')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="ward" class="font-weight-bold">{{ __('Ward') }}</label>
                                        <input type="text" id="ward" placeholder="{{ __('Ward') }}"
                                            class="form-control  border-input-primary @error('ward') is-invalid @enderror"
                                            name="ward" value="{{ $beneficiary->ward }}" autocomplete="ward" style="width: 100%;">
                                        @error('ward')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group border p-2">
                                <legend class="w-auto pl-2 h6">{{ __('~ Marital Information') }}</legend>
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label class="selectMaritalStatus font-weight-bold">{{ __('Marital Status') }}<sup class="text-danger">*</sup></label>
                                        <select id="marital_status" aria-describedby="selectMaritalStatus"
                                            class="select2 select2-container--default   border-input-primary @error('marital_status') is-invalid @enderror"
                                            name="marital_status" required autocomplete="marital_status" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Select marital status') }}</option>
                                            @if ($marital_statuses->count())
                                                @foreach ($marital_statuses as $marital_status)
                                                    <option value="{{ $marital_status->id }}"
                                                    @if ($marital_status->id === $beneficiary->marital_status_id)
                                                        selected="selected"
                                                    @endif
                                                    >{{ __($marital_status->marital_status) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option>{{ __('No statuses found') }}</option>
                                            @endif
                                        </select>
                                        @error('marital_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>

                                    {{-- <div class="col-md-3 mb-3">
                                        <label class="selectFormMarriage font-weight-bold">{{ __('Form of Marriage') }}<sup class="text-danger">*</sup></label>
                                        <select id="form_of_marriage" aria-describedby="selectFormMarriage"
                                            class="select2 select2-container--default   border-input-primary @error('form_of_marriage') is-invalid @enderror"
                                            name="form_of_marriage" required autocomplete="form_of_marriage" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose form of marriage') }}</option>
                                            @if ($marriage_forms->count())
                                            @foreach ($marriage_forms as $marriage_form)
                                                <option value = "{{ $marriage_form->id }}"
                                                    @if ($marriage_form->id === $beneficiary->marriage_form_id)
                                                    selected="selected"
                                                @endif
                                                >{{ $marriage_form->marriage_form }}
                                                </option>
                                            @endforeach
                                            @else
                                                <option>{{ __('No marriage form found') }}</option>
                                            @endif
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                            {{ __('Select N/A only when not applicable') }}
                                        </small>
                                        @error('form_of_marriage')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="marriage_date" class="font-weight-bold">
                                            {{ __('Marriage Date') }}
                                        </label>
                                        <div class="form-group">
                                            <div class="input-group date" id="marriage_date" data-target-input="nearest">
                                                <input type="text" id="dateOfMariage"
                                                    class="form-control datetimepicker-input border-prepend-primary @error('marriage_date') is-invalid @enderror"
                                                   name="marriage_date" 
value="{{ (!empty($beneficiary->marriage_date) && $beneficiary->marriage_date !== 'N/A') 
    ? \Carbon\Carbon::parse($beneficiary->marriage_date)->format('m/d/Y') 
    : '' }}"

                                                     autocomplete="marriage_date"  data-target="#marriage_date"
                                                    data-toggle="datetimepicker"/>
                                                <div class="input-group-append" data-target="#marriage_date">
                                                    <div class="input-group-text  border-append-primary bg-prepend-primary">
                                                        <i class="fas fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-exclamation-circle text-info"></i>
                                            {{ __('Select date to edit, only when applicable') }}
                                        </small>
                                        @error('marriage_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div> --}}

                                    <div class="col-md-3 mb-3">
                                        <label for="no_of_children" class="font-weight-bold">{{ __('Number of Children') }}<sup class="text-danger">*</sup></label>
                                        <input type="text" id="no_of_children" placeholder="Number of Children"
                                            class="form-control  border-input-primary @error('no_of_children') is-invalid @enderror"
                                            name="no_of_children" value="{{ $beneficiary->no_of_children }}" required autocomplete="no_of_children">
                                            <small class="form-text text-muted"> <i class="fas fa-exclamation-triangle text-warning"></i>
                                                {{ __('Fill only when applicable') }}
                                            </small>
                                        @error('no_of_children')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                           
                            <fieldset class="form-group border p-2">
                                <legend class="w-auto pl-2 h6">{{ __('~ Financial Details') }}</legend>
                                <div class="form-row">
                                    <div class="col-md-3 mb-3">
                                        <label class="selectCapability font-weight-bold">{{ __('Financial Capability') }}<sup class="text-danger">*</sup></label>
                                        <select id="financial_capability" aria-describedby="selectCapability"
                                            class="select2 select2-container--default   border-input-primary @error('financial_capability') is-invalid @enderror"
                                            name="financial_capability" required autocomplete="financial_capability" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose option') }}</option>
                                            <option value="Capable" {{ $beneficiary->financial_capability == 'Capable' ? ' selected="selected"' : '' }}>
                                                {{ __('Capable') }}
                                            </option>
                                            <option value="Incapable" {{ $beneficiary->financial_capability == 'Incapable' ? ' selected="selected"' : '' }}>
                                                {{ __('Incapable') }}
                                            </option>
                                        </select>
                                        @error('financial_capability')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="selectEmployment font-weight-bold">{{ __('Employment Status') }}<sup class="text-danger">*</sup></label>
                                            <select id="employment_status" aria-describedby="selectEmployment"
                                            class="select2 select2-container--default   border-input-primary @error('employment_status') is-invalid @enderror"
                                                name="employment_status" required autocomplete="employment_status" style="width: 100%;">
                                                <option hidden disabled selected value>{{ __('Choose employment status')}}</option>
                                                @if ($employment_statuses->count())
                                                    @foreach ($employment_statuses as $employment_status)
                                                    <option value = "{{ $employment_status->id }}"
                                                        @if ($employment_status->id === $beneficiary->employment_status_id)
                                                            selected="selected"
                                                        @endif
                                                        >{{ __($employment_status->employment_status) }}
                                                    </option>
                                                    @endforeach
                                                @else
                                                    <option>{{ __('No employment statuses found') }}</option>
                                                @endif
                                            </select>
                                            @error('employment_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="occupation_business" class="font-weight-bold">{{ __('Occupation / Business') }}<sup class="text-danger">*</sup></label>
                                        <input type="text" id="occupation_business" placeholder="{{ __('Occupation or business') }}"
                                            class="form-control  border-input-primary @error('occupation_business') is-invalid @enderror"
                                            name="occupation_business" value="{{ $beneficiary->occupation_business }}" required autocomplete="occupation_business">
                                        @error('occupation_business')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="monthly_income" class="font-weight-bold">{{ __('Monthly Income') }}<sup class="text-danger">*</sup></label>
                                        <select id="monthly_income" aria-describedby="selectFormMarriage"
                                            class="select2 select2-container--default   border-input-primary @error('monthly_income') is-invalid @enderror"
                                            name="monthly_income" required autocomplete="monthly_income" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose income group') }}</option>
                                            @if ($incomes->count())
                                                @foreach ($incomes as $income)
                                                    <option value = "{{ $income->id }}"
                                                        @if ($income->id === $beneficiary->income_id)
                                                        selected="selected"
                                                    @endif
                                                    >{{ __($income->income) }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option>{{ __('No income group found') }}</option>
                                            @endif
                                        </select>
                                        @error('monthly_income')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>
                            <div class="container">
                                <p class="form-text text-muted">
                                    <i class="fas fa-exclamation-triangle fa-fw text-danger"></i>
                                    <sup class="text-danger font-weight-bold">*</sup> - {{ __('These are required fields.') }}
                                </p>
                            </div>
                    </div>
                    @endif
                    <div class="card-footer">
                        <button class="btn text-white light-custom-color float-right" type="submit">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Beneficiary area end -->
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
            $('#marriage_date').datetimepicker({
                format: 'L',
                viewMode: 'years'
            });

        });
    </script>
@endpush
