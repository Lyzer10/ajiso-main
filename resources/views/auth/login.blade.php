@extends('layouts.app')

@section('title', 'AJISO Legal Aid System | Login')

@section('nav-links')
    <li>
        <a class="nav-link scrollto active" href="/#">{{ __('Home') }}</a>
    </li>
    <li>
        <a class="nav-link scrollto" href="/#about">{{ __('About') }}</a>
    </li>
    <li>
        <a class="nav-link scrollto" href="/#faq">{{ __('FAQs') }}</a>
    </li>   
@endsection

@section('hero')
    <div class="container">
        <div class="row justify-content-between">
        <div class="col-lg-5 pt-lg-0 order-1 order-lg-2">
            <div data-aos="zoom-out row">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header light-custom-color">
                            <h5 class="card-title text-white">{{ __('Sign In') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="py-3">
                                @if (session('status'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <strong>Ooops! </strong>{{ __('Something went wrong!') }}<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('login', app()->getLocale()) }}">
                                @csrf
                                <div class="container-fluid">
                                    <div class="form-group mb-4">
                                        <label for="email" class="font-weight-bold">{{ __('E-Mail Address') }}</label>
                                        <input id="email" type="email" class="form-control border-input-warning @error('email') is-invalid @enderror" 
                                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="password" class="font-weight-bold">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control border-input-warning @error('password') is-invalid @enderror" 
                                            name="password" required autocomplete="current-password">
                                        <i class="fas fa-eye fa-fw field-icon toggle-password"></i>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} >
                                                <label class="custom-control-label" for="remember">
                                                    {{ __('Remember Me') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-right">
                                                <a class="btn btn-link" href="{{ route('password.request', app()->getLocale()) }}">
                                                    {{ __('Forgot Password?') }}
                                                </a>
                                        </div>
                                    </div>
                                    <div class="container mb-5">
                                        <div class="row">
                                            <button type="submit" class="btn light-custom-color text-white w-100">
                                                {{ __('Login') }}
                                                <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 order-2 order-lg-1 hero-img" data-aos="zoom-out" data-aos-delay="300">
            <img src="{{ asset('assets/images/hero-img.png') }}" class="img-fluid animated" alt="AJISO on devices">
        </div>
    </div>
    </div>
@endsection

@section('content')
    <div class="container bg-white">
        <div class="my-4">

        </div>
    </div>    
@endsection
