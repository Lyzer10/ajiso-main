@extends('layouts.base')

@php
    $title = __('Beneficiaries') 
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Beneficiary') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Beneficiary List') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container">
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
        <!--Legal Aid Providers list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        <div class="header-title clearfix">{{ __('Beneficiary List') }}
                            <a href="{{ route('beneficiary.create', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right">
                                {{ __('Add Beneficiary') }}
                            </a>

                             <form method="GET" action="{{ route('beneficiaries.list', [app()->getLocale()]) }}" class="d-flex align-items-center pull-right mb-2">
                                <div class="d-flex align-items-center mr-4">
                                     <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by beneficiary') }}" class="form-control form-control-sm me-2 border-prepend-black p-2">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table progress-table text-center table-striped">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('S/N') }}</th>
                                    <th>{{ __('File No') }}</th>
                                    <th>{{ __('Full Name') }}</th>
                                    <th>{{ __('Telephone No') }}</th>
                                    <th>{{ __('Enrolled On') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if ($beneficiaries->count())
                                @foreach ($beneficiaries as $beneficiary)
                                <tr>
                                    <td>{{ $beneficiaries->firstItem() + $loop->index }}</td>
                                    <td>
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $beneficiary]) }}">
                                            {{ $beneficiary->user->user_no }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $beneficiary->user->first_name.' ' }}
                                        @if ($beneficiary->user->middle_name === '')
                                            {{ ' ' }}
                                        @else
                                            {{ Str::substr($beneficiary->user->middle_name, 0, 1).'.' }}
                                        @endif
                                        {{ ' '.$beneficiary->user->last_name }}
                                    </td>
                                    <td>{{ $beneficiary->user->tel_no }}</td>
                                    <td>{{ Carbon\Carbon::parse($beneficiary->created_at)->format('d-m-Y') }}</td>
                                    <td>
                                        @if ((bool) $beneficiary->user->is_active === true)
                                        <span class="p-1
                                            {{ 'badge badge-success' }}">
                                            {{ __("Active") }}
                                        </span>
                                        @else
                                        <span class="p-1
                                            {{ 'badge badge-secondary' }}">
                                            {{ __("Inactive") }}
                                        @endif
                                    </td>
                                    <td class="d-flex justify-content-between">
                                        <a href="{{ route('beneficiary.edit', [app()->getLocale(), $beneficiary]) }}" title="{{ __('Edit Beneficiary') }}">
                                            <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                        </a> /
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $beneficiary]) }}" title="{{ __('View Beneficiary') }}">
                                            <i class="fas fa-eye fa-fw text-success"></i>
                                        </a>
                                        @can('isSuperAdmin')
                                        /
                                        <form method="POST" action="{{ route('beneficiary.trash', [app()->getLocale(), $beneficiary->id]) }}">
                                            @csrf
                                            @METHOD('PUT')
                                                <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Beneficiary') }}"></i>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="p-1">{{ __('No beneficiaries found') }}</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                        {{ $beneficiaries->count() ? $beneficiaries->links() : ''}}
                    </div>
                </div>
            </div>
        </div>
        <!-- user list area end -->
    </div>
@endsection

@push('scripts')
    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

     <script>
    const searchInput = document.querySelector('input[name="search"]');

    searchInput.addEventListener('input', function () {
        if (this.value === "") {
            this.form.submit(); // auto reload full list
        }
    });
</script>
@endpush
