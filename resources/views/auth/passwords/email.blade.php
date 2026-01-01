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
    <li>
        <a class="nav-link scrollto" href="{{ route('login', app()->getLocale()) }}">
            {{ __('Login') }}
        </a>
    </li> 
@endsection

@section('hero')
    <div class="container">
        <div class="row justify-content-between">
        <div class="col-lg-6 pt-lg-0 order-1 order-lg-2">
            <div data-aos="zoom-out row">
                <div class="card">
                    <div class="card-content">
                        <div class="card-header text-white light-custom-color">
                            <h5 class="card-title text-white text-center">{{ __('Reset Password') }}</h5>
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
                            <form method="POST" action="{{ route('password.email', app()->getLocale()) }}">
                                @csrf
                                <div class="container-fluid">
                                    <div class="form-group row mb-4">
                                        <label for="email" class="col-md-4 col-form-label text-md-right font-weight-bold">{{ __('E-Mail Address') }}</label>
                                        <div class="col-md-8">
                                            <input id="email" type="email" class="form-control border-input-primary @error('email') is-invalid @enderror" 
                                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn text-white light-custom-color">
                                                {{ __('Send Password Reset Link') }}
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
            <img src="{{ asset('assets/images/hero-img.png') }}" class="img-fluid animated" alt="ALAS On Gadgets">
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
