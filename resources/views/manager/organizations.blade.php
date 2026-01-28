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
            <li><span>{{ __('Organizations Manager') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-10">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">A-</button>
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">A-</button>
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
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title clearfix">
                            {{ __('Filter Organizations') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('manager.organizations.list', app()->getLocale()) }}">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="filterName" class="font-weight-bold">{{ __('Organization Name') }}</label>
                                    <input type="text" id="filterName" placeholder="{{ __('Organization') }}"
                                        class="form-control border-input-primary"
                                        name="name" value="{{ request('name') }}" autocomplete="name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="filterRegion" class="font-weight-bold">{{ __('Region') }}</label>
                                    <select id="filterRegion" class="form-control border-input-primary" name="region_id">
                                        <option value="">{{ __('All regions') }}</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ __($region->region) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="filterDistrict" class="font-weight-bold">{{ __('District') }}</label>
                                    <select id="filterDistrict" class="form-control border-input-primary" name="district_id">
                                        <option value="">{{ __('All districts') }}</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                                {{ __($district->district) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="filterWard" class="font-weight-bold">{{ __('Ward') }}</label>
                                    <select id="filterWard" class="form-control border-input-primary" name="ward">
                                        <option value="">{{ __('All wards') }}</option>
                                        @foreach ($wards as $wardOption)
                                            <option value="{{ $wardOption->ward }}" {{ request('ward') == $wardOption->ward ? 'selected' : '' }}>
                                                {{ __($wardOption->ward) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn text-white light-custom-color" type="submit">{{ __('Filter') }}</button>
                                    <a class="btn btn-light" href="{{ route('manager.organizations.list', app()->getLocale()) }}">{{ __('Reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-lg-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <div class="header-title clearfix">
                            {{ __('Organizations') }}
                            <button type="button" class="btn btn-sm text-white light-custom-color pull-right ml-2" data-toggle="modal" data-target="#addOrganizationModal">
                                {{ __('Add Organization') }}
                            </button>
                            <a href="{{ route('settings.manager', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%;">
                        <div class="table-responsive">
                            <table class="table progress-table text-center table-striped">
                                <thead class="text-capitalize text-white light-custom-color">
                                    <tr>
                                        <th>{{ __('S/N') }}</th>
                                        <th>{{ __('Organization') }}</th>
                                        <th>{{ __('Region') }}</th>
                                        <th>{{ __('District') }}</th>
                                        <th>{{ __('Ward') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($organizations->count())
                                        @foreach ($organizations as $organization)
                                            <tr>
                                                <td>{{ $organizations->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <a href="{{ route('manager.organizations.show', [app()->getLocale(), $organization]) }}">
                                                        {{ $organization->name }}
                                                    </a>
                                                </td>
                                                <td>{{ optional($organization->region)->region ?? 'N/A' }}</td>
                                                <td>{{ optional($organization->district)->district ?? 'N/A' }}</td>
                                                <td>{{ $organization->ward ?? 'N/A' }}</td>
                                                <td class="d-flex justify-content-between">
                                                    <a href="{{ route('manager.organizations.edit', [app()->getLocale(), $organization]) }}" title="{{ __('Edit Organization') }}">
                                                        <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                                    </a> /
                                                    <a href="{{ route('manager.organizations.show', [app()->getLocale(), $organization]) }}" title="{{ __('View Organization') }}">
                                                        <i class="fas fa-eye fa-fw text-success"></i>
                                                    </a>
                                                    @can('isSuperAdmin')
                                                    /
                                                    <form method="POST" action="{{ route('manager.organization.trash', [app()->getLocale(), $organization->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{ __('Delete Organization') }}"></i>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">{{ __('No organizations found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        {{ $organizations->count() ? $organizations->appends(request()->query())->links() : ''}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOrganizationModal" tabindex="-1" role="dialog" aria-labelledby="addOrganizationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrganizationModalLabel">{{ __('Add Organization') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('manager.organization.store', app()->getLocale()) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="organizationName" class="font-weight-bold">{{ __('Organization Name') }}<sup class="text-danger">*</sup></label>
                                <input type="text" id="organizationName" placeholder="{{ __('Organization') }}"
                                    class="form-control border-input-primary @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required autocomplete="name">
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
                                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
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
                                    @foreach ($districtsAll as $district)
                                        <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
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
                                    name="ward" value="{{ old('ward') }}" autocomplete="ward">
                                @error('ward')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button class="btn text-white light-custom-color" type="submit">{{ __('Add Organization') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('modals.confirm-trash')

    <script>
        $(function() {
            @if ($errors->has('name') || $errors->has('region_id') || $errors->has('district_id') || $errors->has('ward'))
                $('#addOrganizationModal').modal('show');
            @endif

            $('#filterRegion').change(function() {
                var regionId = $(this).val();
                var districtSelect = $('#filterDistrict');
                var wardSelect = $('#filterWard');

                wardSelect.empty().append('<option value="">{{ __('All wards') }}</option>');

                if (!regionId) {
                    districtSelect.empty().append('<option value="">{{ __('All districts') }}</option>');
                    @foreach ($districtsAll as $district)
                        districtSelect.append('<option value="{{ $district->id }}">{{ __($district->district) }}</option>');
                    @endforeach
                    return;
                }

                var url = '{{ url(app()->getLocale().'/manager/regions') }}' + '/' + regionId + '/districts';
                $.get(url, function(data) {
                    districtSelect.empty().append('<option value="">{{ __('All districts') }}</option>');
                    $.each(data, function(key, value) {
                        districtSelect.append('<option value=' + value.id + '>' + value.district + '</option>');
                    });
                });
            });

            $('#filterDistrict').change(function() {
                var districtId = $(this).val();
                var wardSelect = $('#filterWard');

                wardSelect.empty().append('<option value="">{{ __('All wards') }}</option>');

                if (!districtId) {
                    return;
                }

                var url = '{{ url(app()->getLocale().'/manager/organizations/districts') }}' + '/' + districtId + '/wards';
                $.get(url, function(data) {
                    $.each(data, function(key, value) {
                        wardSelect.append('<option value="' + value.ward + '">' + value.ward + '</option>');
                    });
                });
            });

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
