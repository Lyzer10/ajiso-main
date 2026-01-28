@extends('layouts.base')

@php
    $title = __('Users') 
@endphp
@section('title', 'LAIS | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Users') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('User Profile') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="card-area">
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
        <div class="row">
            @if ($user->count())
            <div class="col-md-4 mt-5">
                <div class="card card-bordered">
                    <img class="card-img-top img-fluid img-circle img-thumbnail"
                        src="
                        @if (File::exists('storage/uploads/images/profiles/'.$user->image))
                            {{ asset('storage/uploads/images/profiles/'.$user->image) }}
                        @else
                            {{ asset('assets/images/avatar/avatar.png') }}
                        @endif
                        "
                        style="height: 100%; width: 100%;" alt="user image">
                    <div class="card-body text-center">
                        <h4 class="text-uppercase font-weight-bold mb-3">
                            <a href="">
                                {{ Str::upper($user->first_name).' ' }}
                                @if ($user->middle_name === '')
                                    {{ ' ' }}
                                @else
                                    {{ Str::ucfirst(Str::substr($user->middle_name, 0, 1)).'.' }}
                                @endif
                                {{ ' '.Str::upper($user->last_name) }}
                            </a>
                        </h4>
                        <h6 class="mb-3"> {{ $user->email }} </h6>
                        <p class="card-text">
                            <span class="text-success"> {{ Str::ucfirst($user->role->role_abbreviation) }} </span>
                        </p>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-outline-primary btn-rounded" data-toggle="modal" data-target="#modalUploadPic">{{ __('Upload new photo') }}</button>
                    </div>
                </div>
            </div>
            <!-- user nav tabs start -->
            <div class="col-md-8 mt-5">
                <div class="card card-bordered">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">{{ __('Profile') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="setting-tab" data-toggle="tab" href="#setting" role="tab" aria-controls="setting" aria-selected="false">{{ __('Settings') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="false">{{ __('Security') }}</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('User No') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ $user->user_no }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('Username') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ '@'.$user->name }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('Full Name') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>
                                            {{ Str::ucfirst($user->first_name).' ' }}
                                            @if ($user->middle_name === '')
                                                {{ ' ' }}
                                            @else
                                                {{ Str::ucfirst($user->middle_name) }}
                                            @endif
                                            {{ ' '.Str::ucfirst($user->last_name) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('Email Address') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ $user->email }}</span>
                                        @if (is_null($user->email_verified_at))
                                        <div class="alert alert-warning mt-3 w-75">{{ __('Your email is not confirmed. Please check your inbox.') }}<br>
                                            <a href="">{{ __('Resend confirmation') }}</a>
                                        </div>
                                        @else
                                        <span class="ml-1"><i class="fas fa-check-circle text-primary"></i></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('Telephone No') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ $user->tel_no }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('User Role') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ Str::ucfirst($user->role->role_name) }} </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('Registered') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span>{{ Carbon\Carbon::parse($user->created_at)->format('d, F Y') }}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="font-weight-bold">{{ __('Status') }}:</label>
                                    </div>
                                    <div class="col-md-8">
                                        @if ((bool)$user->is_active === true)
                                        <span class="p-1
                                            {{ 'badge badge-success' }}">
                                            {{ __("Active") }}
                                        </span>
                                        @else
                                        <span class="p-1
                                            {{ 'badge badge-secondary' }}">
                                            {{ __("Inactive") }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="setting" role="tabpanel" aria-labelledby="setting-tab">
                                <div class="row mt-4 mb-3">
                                    <!-- Edit User area start -->
                                    <div class="col-12">
                                        <form action="{{ route('user.update.profile', [app()->getLocale(), auth()->user()->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="username" class="font-weight-bold">{{ __('Username') }}<sup class="text-danger">*</sup></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="inputGroupUserName">@</span>
                                                        </div>
                                                        <input  readonly type="text" id="username" placeholder="{{ __('Username') }}" aria-describedby="inputGroupUserName"
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
                                                <div class="col-md-6 mb-3">
                                                    <label for="middleName" class="font-weight-bold">{{ __('Middle Name (optional)') }}</label>
                                                    <input type="text" id="middleName" placeholder="{{ __('Middle Name (optional)') }}"
                                                        class="form-control  border-input-primary @error('middle_name') is-invalid @enderror"
                                                        name="middle_name" value="{{ $user->middle_name ?? "N/A" }}" autocomplete="middle_name">
                                                    @error('middle_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
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
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="tel_no" class="font-weight-bold">{{ __('Telephone No') }}<sup class="text-danger">*</sup></label>
                                                    <input type="tel" id="tel_no" placeholder="{{ __('Telephone number') }}"
                                                        class="form-control  border-input-primary @error('tel_no') is-invalid @enderror"
                                                        name="tel_no" value="{{ $user->tel_no }}" required autocomplete="tel_no" style="width: 100%;">
                                                        <small class="form-text text-muted"> <i class="fas fa-exclamation-circle text-info"></i> {{ __('Format: +255712345678') }}</small>
                                                    @error('tel_no')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="validationCustom01" class="font-weight-bold">{{ __('Email Address') }}<sup class="text-danger">*</sup></label>
                                                    <input type="email"  id="email" placeholder="{{ __('Email Address') }}"
                                                        class="form-control border-input-primary @error('email') is-invalid @enderror"
                                                        name="email" value="{{ $user->email }}" required autocomplete="email">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12 mt-3">
                                                    <button class="btn btn-primary float-right" type="submit">
                                                        {{ __('Save Changes') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- Edit User area end -->
                            </div>
                            @endif
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                <form action="{{ route('user.update.password', [app()->getLocale(), auth()->user()->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row mt-4 mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="current_password" class="font-weight-bold">{{ __('Current Password') }}<sup class="text-danger">*</sup></label>
                                                <input id="current_password" type="password" class="form-control  border-primary @error('current_password') is-invalid @enderror" 
                                                    name="current_password" required autocomplete="current_password">
                                                    <i class="fas fa-eye fa-fw field-icon" id="toggle-password"></i>
                                                @error('current_password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="password" class="font-weight-bold">{{ __('New Password') }}<sup class="text-danger">*</sup></label>
                                                <input id="password" type="password" pattern="[0-9a-zA-Z]{8,50}"
                                                    title="password should be 8 or more characters containing at least a number, a lowercase and uppercase letter"
                                                    class="form-control  border-primary @error('password') is-invalid @enderror" 
                                                    name="password" required autocomplete="password">
                                                    <i class="fas fa-eye fa-fw field-icon" id="toggle-curr-password"></i>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="password-confirm" class="font-weight-bold">{{ __('Confirm Password') }}<sup class="text-danger">*</sup></label>
                                                <input id="password-confirm" type="password" class="form-control  border-primary" 
                                                    name="password_confirmation" required autocomplete="new_password">
                                                    <i class="fas fa-eye fa-fw field-icon" id="toggle-password-conf"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2 font-weight-normal">{{ __('Password Requirements') }}</p>
                                            <p class="text-muted mb-2">{{ __('To create a new password, you have to meet all of the following requirements:') }}</p>
                                            <ul class="text-muted pl-4 mb-0">
                                                <li>{{ __(' - Minimum 8 character') }}</li>
                                                <li>{{ __(' - At least one uppercase') }}</li>
                                                <li>{{ __(' - At least one number') }}</li>
                                                <li>{{ __(' - Can’t be the same as a previous password') }}</li>
                                            </ul>
                                            <button class="btn btn-primary float-right mt-4" type="submit">{{ __('Confirm') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- user nav tabs end -->
    </div>
    @include('modals.update-photo')
@endsection

@push('scripts')

    {{-- Toggle password hide/show --}}
    <script type="text/javascript">

        $("#toggle-password, #toggle-curr-password, #toggle-password-conf").on("click", function () {

            $(this).toggleClass("fa-eye fa-eye-slash");

            var type =  $(this).hasClass('fa-eye-slash') ? "text" : "password";

            var id = $(this).attr("id");

            if (id == "toggle-password") {
                $("#current_password").attr("type", type);
            
            }else if (id == "toggle-curr-password"){
                $("#password").attr("type", type);

            }else{
                $("#password-confirm").attr("type", type);
            }

        })
    </script>
@endpush
