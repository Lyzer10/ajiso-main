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
            <li><span>{{ __('Edit Organization') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <div class="row">
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title clearfix">
                            {{ __('Edit Organization') }}
                            <a href="{{ route('manager.organizations.list', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right">
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manager.organization.update', [app()->getLocale(), $organization->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="organizationName" class="font-weight-bold">{{ __('Organization Name') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="organizationName" placeholder="{{ __('Organization') }}"
                                        class="form-control border-input-primary @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $organization->name) }}" required autocomplete="name">
                                    @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="organizationRegion" class="font-weight-bold">{{ __('Region') }}<sup class="text-danger">*</sup></label>
                                    <select id="organizationRegion"
                                        class="form-control border-input-primary @error('region_id') is-invalid @enderror"
                                        name="region_id" required>
                                        <option hidden disabled selected value>{{ __('Choose region') }}</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}" {{ (int) old('region_id', $organization->region_id) === (int) $region->id ? 'selected' : '' }}>
                                                {{ __($region->region) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="organizationDistrict" class="font-weight-bold">{{ __('District') }}<sup class="text-danger">*</sup></label>
                                    <select id="organizationDistrict"
                                        class="form-control border-input-primary @error('district_id') is-invalid @enderror"
                                        name="district_id" required>
                                        <option hidden disabled selected value>{{ __('Choose district') }}</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}" {{ (int) old('district_id', $organization->district_id) === (int) $district->id ? 'selected' : '' }}>
                                                {{ __($district->district) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('district_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="organizationWard" class="font-weight-bold">{{ __('Ward') }}</label>
                                    <input type="text" id="organizationWard" placeholder="{{ __('Ward') }}"
                                        class="form-control border-input-primary @error('ward') is-invalid @enderror"
                                        name="ward" value="{{ old('ward', $organization->ward) }}" autocomplete="ward">
                                    @error('ward')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button class="btn text-white light-custom-color float-right" type="submit">{{ __('Update Organization') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#organizationRegion').change(function() {
                var url = '{{ url(app()->getLocale().'/manager/regions') }}' + '/' + $(this).val() + '/districts';

                $.get(url, function(data) {
                    var select = $('#organizationDistrict');
                    select.empty();
                    select.append('<option hidden disabled selected value>{{ __('Choose district') }}</option>');
                    $.each(data, function(key, value) {
                        select.append('<option value=' + value.id + '>' + value.district + '</option>');
                    });
                });
            });
        });
    </script>
@endpush
