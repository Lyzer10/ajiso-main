@extends('layouts.base')

@php
    $title = __('Users') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Users') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Edit Users') }}</span></li>
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
        <!-- Edit user area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">
                        {{ __('Edit User') }}
                        <a href="{{ route('users.list', app()->getLocale())}}" class="btn btn-sm text-white light-custom-color pull-right">
                            {{ __('User list')}}
                        </a>
                    </h4>
                </div>
                <form action="{{ route('user.update', [app()->getLocale(), $user]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @METHOD ('PUT')
                    <div class="card-body">
                        @if ($user->count())
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card card-bordered mb-3">
                                        <img class="card-img-top img-fluid img-circle img-thumbnail"
                                            src="
                                            @if (File::exists('storage/uploads/images/profiles/'.$user->image))
                                                {{ asset('storage/uploads/images/profiles/'.$user->image) }}
                                            @else
                                                {{ asset('storage/uploads/images/avatar/avatar.png') }}
                                            @endif
                                            "
                                            style="height: 308px; width: 100%; " alt="user image">
                                        <div class="card-footer text-center">
                                            <div class="form-row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" id="image" class="custom-file-input @error('image') is-invalid @enderror"
                                                                name="image" autocomplete="image">
                                                            <label class="custom-file-label  border-input-primary" for="image">{{ __('Upload new image') }}</label>
                                                        </div>
                                                    </div>
                                                    @error('image')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
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
                                        <div class="col-md-6 mb-3">
                                            <label class="selectDesignation font-weight-bold">{{ __('Designation / Title') }}<sup class="text-danger">*</sup></label>
                                            <select id="designation" aria-describedby="selectDesignation"
                                                class="select2 select2-container--default   border-input-primary @error('designation') is-invalid @enderror"
                                                name="designation" required autocomplete="designation" style="width: 100%;">
                                                <option hidden disabled selected value>{{ __('Choose designation / title') }}</option>
                                                @if ($designations->count())
                                                    @foreach ($designations as $designation)
                                                        <option value="{{ $designation->id }}"
                                                        @if ($designation->id === $user->salutation_id)
                                                            selected="selected"
                                                        @endif
                                                        >{{ __($designation->name) }}
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="firstName" class="font-weight-bold">{{ __('First Name') }}<sup class="text-danger">*</sup></label>
                                            <input type="text" id="firstName" placeholder="First name"
                                                class="form-control  border-input-primary @error('first_name') is-invalid @enderror"
                                                name="first_name" value="{{ $user->first_name }}" required autocomplete="first_name">
                                            @error('first_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="validationCustom01" class="font-weight-bold">{{ __('Email Address (Optional)') }}</label>
                                            <input type="email"  id="email" placeholder="Email Address"
                                                class="form-control  border-input-primary @error('email') is-invalid @enderror"
                                                name="email" value="{{ $user->email }}" autocomplete="email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="middleName" class="font-weight-bold">{{ __('Middle Name (optional)') }}</label>
                                            <input type="text" id="middleName" placeholder="Middle name"
                                                class="form-control  border-input-primary @error('middle_name') is-invalid @enderror"
                                                name="middle_name" value="{{ $user->middle_name ?? '' }}" autocomplete="middle_name">
                                            @error('middle_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="tel_no" class="font-weight-bold">{{ __('Telephone No') }}<sup class="text-danger">*</sup></label>
                                            <input type="tel" id="tel_no" placeholder="Telephone number"
                                                class="form-control  border-input-primary @error('tel_no') is-invalid @enderror"
                                                name="tel_no" value="{{ $user->tel_no }}" required autocomplete="tel_no" style="width: 100%;">
                                                <small class="form-text text-muted"> <i class="fas fa-exclamation-circle text-info"></i> {{ __('Format: +255712345678') }}</small>
                                            @error('tel_no')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="lastName" class="font-weight-bold">{{ __('Last Name') }}<sup class="text-danger">*</sup></label>
                                            <input type="text" id="lastName" placeholder="Last name"
                                                class="form-control  border-input-primary @error('last_name') is-invalid @enderror"
                                                name="last_name" value="{{ $user->last_name }}" required autocomplete="last_name">
                                            @error('last_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="font-weight-bold">{{ __('Status') }}<sup class="text-danger">*</sup></label>
                                            <select id="status"
                                                class="select2 select2-container--default   border-input-primary @error('status') is-invalid @enderror"
                                                name="status" required autocomplete="status" style="width: 100%;">
                                                <option hidden disabled selected value>{{ __('Choose status') }}</option>
                                               <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>
        {{ __('Active') }}
    </option>

    <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>
        {{ __('Inactive') }}
    </option>
                                            </select>
                                            @error('status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="password" class="font-weight-bold">{{ __('Password') }}<sup class="text-danger">*</sup></label>
                                    <input id="password" type="password" class="form-control  border-primary @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="password-confirm" class="font-weight-bold">{{ __('Confirm Password') }}<sup class="text-danger">*</sup></label>
                                    <input id="password-confirm" type="password" class="form-control  border-primary" name="password_confirmation" required autocomplete="new-password">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="user_role" class="font-weight-bold">{{ __('User Role') }}<sup class="text-danger">*</sup></label>
                                    <select id="user_role"
                                        class="select2 select2-container--default   border-input-primary @error('user_role') is-invalid @enderror"
                                        name="user_role" required autocomplete="user_role" style="width: 100%;">
                                        <option hidden disabled selected value>{{ __('Choose a role') }}</option>
                                        @if ($user_roles->count())
                                            @foreach ($user_roles as $user_role)
                                                <option value="{{ $user_role->id }}" data-role="{{ $user_role->role_abbreviation }}"
                                                    @if ($user_role->id === $user->user_role_id)
                                                        selected="selected"
                                                    @endif
                                                    >{{ __(Str::ucfirst($user_role->role_abbreviation)) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option>{{ __('No User roles found') }}</option>
                                        @endif
                                    </select>
                                    @error('user_role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3" id="organization_group" style="{{ optional($user->role)->role_abbreviation === 'paralegal' ? '' : 'display: none;' }}">
                                    <label for="organization_id" class="font-weight-bold">{{ __('Organization') }}<sup class="text-danger">*</sup></label>
                                    <select id="organization_id"
                                        class="select2 select2-container--default border-input-primary @error('organization_id') is-invalid @enderror"
                                        name="organization_id" autocomplete="organization_id" style="width: 100%;">
                                        <option hidden disabled selected value>{{ __('Choose organization') }}</option>
                                        @if ($organizations->count())
                                            @foreach ($organizations as $organization)
                                                <option value="{{ $organization->id }}" {{ $user->organization_id == $organization->id ? 'selected' : '' }}>
                                                    {{ __($organization->name) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option>{{ __('No organizations found') }}</option>
                                        @endif
                                    </select>
                                    @error('organization_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button class="btn text-white light-custom-color float-right" type="submit">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Edit user area end -->
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

    <script type="text/javascript">
        $(function() {
            function toggleOrganization() {
                var role = $('#user_role').find(':selected').data('role');
                if (role === 'paralegal') {
                    $('#organization_group').show();
                } else {
                    $('#organization_group').hide();
                    $('#organization_id').val(null).trigger('change');
                }
            }

            toggleOrganization();
            $('#user_role').on('change', toggleOrganization);
        });
    </script>
@endpush
