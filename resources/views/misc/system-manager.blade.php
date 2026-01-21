@extends('layouts.base')

@php
    $title = __('Settings') 
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Settings') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Entities Manager') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
    <div class="row mb-3">
        <!-- Settings Tabsarea start -->
        <div class=" col-lg-12 mt-5">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        {{ __('Entities Manager') }}
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text"> <code>{{ __('Entities Manager') }}</code>, {{ __('is the central module for managing tweaking system entities. Operations such as creating, viewing, updating, and removing Entities information are achieved here. Entities managed through the Entities Manager can be accessed through the links below.') }}
                    </p>
                </div>
            </div>
        </div>
        <!-- Settings Tabs area end -->
    </div>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.services.types.list', app()->getLocale()) }}">{{ __('Types of Services') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.disputes.types.list', app()->getLocale()) }}">{{ __('Types of Cases') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.disputes.statuses.list', app()->getLocale()) }}">{{ __('Dispute Statuses') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.metrics.list', app()->getLocale()) }}">{{ __('Metrics') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.regions.list', app()->getLocale()) }}">{{ __('Regions') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.religions.list', app()->getLocale()) }}">{{ __('Religions') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.tribes.list', app()->getLocale()) }}">{{ __('Tribes') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.metrics.measures.list', app()->getLocale()) }}">{{ __('Metric Measures') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.organizations.list', app()->getLocale()) }}">{{ __('Organizations') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.districts.list', app()->getLocale()) }}">{{ __('Districts') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.survey.choices.list', app()->getLocale()) }}">{{ __('Survey Choices') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.marital.statuses.list', app()->getLocale()) }}">{{ __('Maritial Statuses') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.marital.forms.list', app()->getLocale()) }}">{{ __('Marriage Forms') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3 text-center">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.designations.list', app()->getLocale()) }}">{{ __('Designations / Titles') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.incomes.list', app()->getLocale()) }}">{{ __('Income Groups') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.education.levels.list', app()->getLocale()) }}">{{ __('Education Levels') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('manager.employment.statuses.list', app()->getLocale()) }}">{{ __('Employment Statuses') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
