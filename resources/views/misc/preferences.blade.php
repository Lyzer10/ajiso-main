@extends('layouts.base')

@php
    $title = __('Settings') 
@endphp
@section('title', 'LAIS | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Settings') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('System Preferences') }}</span></li>
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
    @if ($preferences->count())
    <!-- View preferences area start -->
    <div class="col-12">
        <div class="card mt-5">
            <div class="card-header">
                <h4 class="header-title">{{ __('System preferences') }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('preference.update', [app()->getLocale(), $preferences->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="site_abbr" class="col-md-3 col-form-label font-weight-bold">{{ __('System Abbreviation') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control border-input-primary" name="site_abbr" id="site_abbr" value="{{ $preferences->sys_abbr }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="site_name" class="col-md-3 col-form-label font-weight-bold">{{ __('System Name') }}</label>
                        <div class="col-md-9">
                            <input type=" text" class="form-control border-input-primary" name="site_name" id="site_name" value="{{ __($preferences->sys_name) }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="org_abbr" class="col-md-3 col-form-label font-weight-bold">{{ __('Organization Abbreviation') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control border-input-primary" name="org_abbr" id="org_abbr" value="{{ $preferences->org_abbr }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="org_name" class="col-md-3 col-form-label font-weight-bold">{{ __('Organization Name') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control border-input-primary" name="org_name" id="org_name" value="{{ $preferences->org_name }}">
                        </div>
                    </div>
                    <div class=" form-group row">
                        <label for="org_site" class="col-md-3 col-form-label font-weight-bold">{{ __('Organization Website') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <input type="url" class="form-control border-input-primary" name="org_site" id="org_site" value="{{ $preferences->org_site }}">
                        </div>
                    </div>
                    <div class=" form-group row">
                        <label for="org_URL" class="col-md-3 col-form-label font-weight-bold">{{ __('Organization Email') }}</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control border-input-primary" name="org_email" id="org_email" value="{{ $preferences->org_email }}">
                        </div>
                    </div>
                    <div class=" form-group row">
                        <label for="org_tel" class="col-md-3 col-form-label font-weight-bold">{{ __('Organization Tel') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <input type="tel" class="form-control border-input-primary" name="org_tel" id="org_tel" value="{{ $preferences->org_tel }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label id="language" class="col-md-3 col-form-label font-weight-bold">{{ __('Language') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <select id="language" aria-describedby="language"
                                class="select2 select2-container--default   border-input-primary @error('language') is-invalid @enderror"
                                name="language" required autocomplete="language" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose language') }}</option>
                                    <option value="en" @if ($preferences->language === 'en') selected = 'selected' @endif >
                                        {{ __('English (Defalut)') }}
                                    </option>
                                    <option value="sw" @if ($preferences->language === 'sw') selected = 'selected' @endif >
                                        {{ __('Swahili') }}
                                    </option>
                            </select>
                            @error('language')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label id="Currency" class="col-md-3 col-form-label font-weight-bold">{{ __('Currency') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <select id="currency" aria-describedby="Currency"
                                class="select2 select2-container--default   border-input-primary @error('currency') is-invalid @enderror"
                                name="currency" required autocomplete="currency" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose currency') }}</option>
                                    <option value="Tshs" @if ($preferences->currency_format === 'Tshs') selected = 'selected' @endif >
                                        {{ __('Tshs (Default)') }}
                                    </option>
                                    <option value="Dollars" @if ($preferences->currency_format === 'Dollars') selected = 'selected' @endif >
                                        {{ __('Dollars') }}
                                    </option>
                            </select>
                            @error('currency')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label id="date_format" class="col-md-3 col-form-label font-weight-bold">{{ __('Date Format') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <select id="date_format" aria-describedby="date_format"
                                class="select2 select2-container--default   border-input-primary @error('date_format') is-invalid @enderror"
                                name="date_format" required autocomplete="date_format" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose date format') }}</option>
                                    <option value="Y-m-d" @if ($preferences->date_format === 'Y-m-d') selected = 'selected' @endif >
                                        {{ __('Y-m-d (Default)') }}
                                    </option>
                                    <option value="d-m-Y" @if ($preferences->date_format === 'd-m-Y') selected = 'selected' @endif >
                                        {{ __('d-m-Y') }}
                                    </option>
                                    <option value="m-d-Y" @if ($preferences->date_format === 'm-d-Y') selected = 'selected' @endif >
                                        {{ __('m-d-Y') }}
                                    </option>
                            </select>
                            @error('date_format')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="notification" class="col-md-3 col-form-label font-weight-bold">{{ __('Notification Mode') }}<sup class="text-danger">*</sup></label>
                        <div class="col-md-9">
                            <select id="notification" aria-describedby="notification"
                                class="select2 select2-container--default   border-input-primary @error('notification') is-invalid @enderror"
                                name="notification" required autocomplete="notification" style="width: 100%;">
                                    <option hidden disabled selected value>{{ __('Choose date format') }}</option>
                                    <option value="sms_email_sys" @if ($preferences->notification_mode === 'sms_email_sys') selected = 'selected' @endif >
                                        {{ __('SMS, Email & System (Default)') }}
                                    </option>
                                    <option value="email_sys" @if ($preferences->notification_mode === 'email_sys') selected = 'selected' @endif >
                                        {{ __('Email & System') }}
                                    </option>
                                    <option value="system" @if ($preferences->notification_mode === 'system') selected = 'selected' @endif >
                                        {{ __('System') }}
                                    </option>
                                    <option value="email" @if ($preferences->notification_mode === 'email') selected = 'selected' @endif >
                                        {{ __('Email') }}
                                    </option>
                                    <option value="sms" @if ($preferences->notification_mode === 'sms') selected = 'selected' @endif >
                                        {{ __('SMS') }}
                                    </option>
                            </select>
                            @error('notification')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-right">{{ __('Save Changes') }}</button>
                </form>
            </div>
        </div>
    </div>
    <!-- View preferences  area end -->
    @endif
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
