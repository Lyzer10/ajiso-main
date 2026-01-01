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
            <li><span>{{ __('District Manager') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="Container-fluid">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <div class="row">
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('manager.district.store', app()->getLocale()) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3 ">
                                    <label for="regions" class="font-weight-bold">{{ __('Region') }}<sup class="text-danger">*</sup></label>
                                    <select id="regions" aria-describedby="selectDegion"
                                        class="select2 select2-container--default   border-input-primary @error('regions') is-invalid @enderror"
                                        name="regions" required autocomplete="regions" style="width: 100%;">
                                        <option hidden disabled selected value>{{ __('Choose region') }}</option>
                                        @if ($regions->count())
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->id }}">
                                                    {{ __($region->region) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option>{{ __('No regions found') }}</option>
                                        @endif
                                    </select>
                                    @error('regions')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </select>
                                </div>
                                <div class="col-md-4 mb-3 ">
                                    <label for="districts" class="font-weight-bold">{{ __('District') }}<sup class="text-danger">*</sup></label>
                                    <input type="text" id="districts" placeholder="{{ __('One district at a time') }}"
                                        class="form-control  border-input-primary @error('districts') is-invalid @enderror"
                                        name="districts" value="{{ old('districts') }}" required autocomplete="districts">
                                    @error('districts')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-4 ">
                                    <button class="btn text-white light-custom-color float-right" type="submit ">{{ __('Add District') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3 ">
            <!-- District Managerarea start -->
            <div class=" col-lg-12 mt-3 ">
                <div class="card ">
                    <div class="card-header ">
                        <div class="header-title clearfix ">
                            <div class="header-title clearfix ">{{ __('Districts') }}
                                <a href="{{ route('settings.manager', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                                    {{ __('Back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="width: 100%; ">
                        <div class="table-responsive ">
                            <table class="table progress-table text-center table-striped ">
                                <thead class="text-capitalize text-white light-custom-color ">
                                    <tr>
                                        <th>S/N</th>
                                        <th>{{ __('Region Name') }}</th>
                                        <th>{{ __('District Name') }}</th>
                                        <th colspan="2">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($districts->count())
                                        @foreach ($districts as $district)
                                            <tr>
                                                <form action="{{ route('manager.district.update', [app()->getLocale(), $district->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <td>{{ $districts->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <input type="text" id="region" placeholder="region"
                                                            class="form-control-sm border-0 @error('region') is-invalid @enderror"
                                                            name="region" value="{{ $district->region->region}}" required autocomplete="region" readonly>
                                                        @error('region')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" id="district" placeholder="district"
                                                            class="form-control-sm border-0 @error('district') is-invalid @enderror"
                                                            name="district" value="{{ $district->district }}" required autocomplete="district">
                                                        @error('district')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <button role="button" class="btn btn-light border-0" title="Update">
                                                            <i class="fas fa-upload fa-fw text-success"></i>
                                                        </button>
                                                    </td>
                                                </form>
                                                <td class="d-flex justify-content-between">
                                                    @can('isSuperAdmin')
                                                    /
                                                    <form method="POST" action="{{ route('manager.district.trash', [app()->getLocale(), $district->id]) }}">
                                                        @csrf
                                                        @METHOD('PUT')
                                                            <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete District') }}"></i>
                                                    </form>
                                                    @endcan
                                                </td>
                                        </tr>
                                        @endforeach
                                </tbody>
                                @else
                                    <span>{{ __('No districts found') }}</span>
                                @endif
                            </table>
                        </div>
                        {{ $districts->count() ? $districts->links() : ''}}
                    </div>
                </div>
            </div>
            <!-- District Manager area end -->
        </div>
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

{{-- Include the sweetalert --}}
@include('modals.confirm-trash')

@endpush
