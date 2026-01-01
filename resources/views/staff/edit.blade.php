@extends('layouts.base')

@section('title', 'AJISO | Legal Aid Providers')

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
<div class="breadcrumbs-area clearfix">
    <h4 class="page-title pull-left">{{ __('Legal Aid Providers') }}</h4>
    <ul class="breadcrumbs pull-left">
        @canany(['isSuperAdmin','isAdmin', 'isClerk'])
            <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
        @elsecanany(['isStaff'])
            <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
        @endcanany
        <li><span>{{ __('Edit Legal Aid Provider') }}</span></li>
    </ul>
</div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- Edit legal aid provider area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('Edit Legal Aid Provider') }}
                        <a href="{{ route('staff.list', app()->getLocale())}}" class="btn btn-sm text-white light-custom-color pull-right">
                            {{ __('Legal Aid Provider List') }}
                        </a>
                    </h4>
                </div>
                <div class="card-body ">
                    <form action="{{ route('staff.update', [app()->getLocale(), $staff]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @METHOD ('PUT')
                        @if ($staff->count())
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="user_no" class="font-weight-bold">{{ __('User No') }}<sup class="text-danger">*</sup></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="inputGroupUserNo">#</span>
                                    </div>
                                    <input readonly type="text" class="form-control border-append-primary" name="user_no" id="user_no" placeholder="User Id"
                                    value="{{ $staff->user->user_no }}"  aria-describedby="inputGroupUser" required>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="username" class="font-weight-bold">{{ __('Username') }}<sup class="text-danger">*</sup></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="inputGroupUserName">@</span>
                                    </div>
                                    <input type="text" id="username" placeholder="Username" aria-describedby="inputGroupUserName"
                                        class="form-control border-append-primary @error('name') is-invalid @enderror" name="name"
                                        value="{{ $staff->user->name }}" required autocomplete="name" autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="selectDesignation font-weight-bold">{{ __('Designation / Title') }}<sup class="text-danger">*</sup></label>
                                <select id="designation" aria-describedby="selectDesignation"
                                    class="select2 select2-container--default   border-input-primary @error('designation') is-invalid @enderror"
                                    name="designation" required autocomplete="designation" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose designation') }}</option>
                                    @if ($designations->count())
                                        @foreach ($designations as $designation)
                                            <option value="{{ $designation->id }}"
                                                @if ($designation->id === $staff->user->designation->id)
                                                    selected="selected"
                                                @endif>
                                                {{ __($designation->designation) }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>{{ __('No designations / titles found') }}</option>
                                    @endif
                                </select>
                                @error('designation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3 ">
                                <label for="office " class="font-weight-bold ">{{ __('Office Location') }}<sup class="text-danger ">*</sup></label>
                                <input type="text "
                                    class="form-control  border-input-primary" name="office" id="office"
                                    placeholder="Eg. Moshi, Kilimanjaro" value="{{ $staff->office }}" required autocomplete="office">
                                @error('office')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                        @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="firstName" class="font-weight-bold">{{ __('First Name') }}<sup class="text-danger">*</sup></label>
                                <input type="text" id="firstName" placeholder="{{ __('First Name') }}"
                                    class="form-control  border-input-primary @error('first_name') is-invalid @enderror"
                                    name="first_name" value="{{ $staff->user->first_name }}" required autocomplete="first_name">
                                @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middleName" class="font-weight-bold">{{ __('Middle Name (optional)') }}</label>
                                <input type="text" id="middleName" placeholder="{{ __('Middle Name (optional)') }}"
                                    class="form-control  border-input-primary @error('middle_name') is-invalid @enderror"
                                    name="middle_name" value="{{ $staff->user->middle_name ?? '' }}" autocomplete="middle_name">
                                @error('middle_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lastName" class="font-weight-bold">{{ __('Last Name') }}<sup class="text-danger">*</sup></label>
                                <input type="text" id="lastName" placeholder="{{ __('Last Name') }}"
                                    class="form-control  border-input-primary @error('last_name') is-invalid @enderror"
                                    name="last_name" value="{{ $staff->user->last_name }}" required autocomplete="last_name">
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="validationCustom01" class="font-weight-bold">{{ __('Email Address') }}<sup class="text-danger">*</sup></label>
                                <input type="email"  id="email" placeholder="{{ __('Email Address') }}"
                                    class="form-control  border-input-primary @error('email') is-invalid @enderror"
                                    name="email" value="{{ $staff->user->email }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tel_no" class="font-weight-bold">{{ __('Telephone No') }}<sup class="text-danger">*</sup></label>
                                <input type="tel" id="tel_no" placeholder="{{ __('Telephone number') }}"
                                    class="form-control  border-input-primary @error('tel_no') is-invalid @enderror"
                                    name="tel_no" value="{{ $staff->user->tel_no }}" required autocomplete="tel_no" style="width: 100%;">
                                    <small class="form-text text-muted"> <i class="fas fa-exclamation-circle text-info"></i> {{ __('Format: 0712345678') }}</small>
                                @error('tel_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="image" class="font-weight-bold">{{ __('Image (optional)') }} </label>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" id="image" class="custom-file-input @error('image') is-invalid @enderror"
                                            name="image" autocomplete="image">
                                        <label class="custom-file-label  border-input-primary" for="image">{{ __('Choose file') }}</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted"> <i class="fas fa-exclamation-circle text-info"></i> {{ __('png, jpg, gif. Max: 2MB') }}</small>
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="card-footer ">
                            <button class="btn text-white light-custom-color float-right " type="submit ">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit legal aid provider area end -->
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
@endpush
