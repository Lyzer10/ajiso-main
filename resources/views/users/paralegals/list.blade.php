@extends('layouts.base')

@php
    $membersMode = $membersMode ?? false;
    $listRoute = $listRoute ?? 'paralegals.list';
    $title = $membersMode ? __('Members') : __('Paralegals');
    $organizationName = $organizationName ?? request('organization_name');
    if (!empty($organizationId)) {
        $organizationName = null;
    }
    $exportQuery = '';
    if (! $membersMode) {
        $exportParams = array_filter([
            'search' => request('search'),
            'organization_id' => request('organization_id'),
            'organization_name' => $organizationName,
        ]);
        $exportQuery = $exportParams ? ('?' . http_build_query($exportParams)) : '';
    }
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ $membersMode ? __('Members') : __('Paralegals') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ $membersMode ? __('Members List') : __('Paralegals List') }}</span></li>
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

    <div class="col-md-12 mt-5 mb-3">
        <div class="card">
            <div class="card-header paralegals-desktop-header d-none d-md-block">
                <div class="header-title clearfix">
                    {{ $membersMode ? __('Members list') : __('Paralegals list') }}
                    @if (!auth()->user()->can('isClerk') || auth()->user()->can_register_staff)
                        <a class="btn btn-sm text-white light-custom-color pull-right" href="{{ route('paralegal.create', app()->getLocale()) }}">
                            {{ $membersMode ? __('Add Member') : __('Add Paralegal') }}
                        </a>
                    @endif
                    @unless ($membersMode)
                        <div class="dropdown pull-right mr-2">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-download fa-fw"></i>
                                {{ __('Export') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('paralegals.export.pdf', app()->getLocale()) }}{{ $exportQuery }}">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    {{ __('as pdf') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('paralegals.export.excel', app()->getLocale()) }}{{ $exportQuery }}">
                                    <i class="fas fa-file-excel text-success"></i>
                                    {{ __('as excel') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('paralegals.export.csv', app()->getLocale()) }}{{ $exportQuery }}">
                                    <i class="fas fa-file-csv text-warning"></i>
                                    {{ __('as csv') }}
                                </a>
                            </div>
                        </div>
                    @endunless
                    <form method="GET" action="{{ route($listRoute, [app()->getLocale()]) }}" class="d-flex align-items-center pull-right mb-2 mr-2">
                        <div class="d-flex align-items-center mr-4">
                            @unless ($membersMode)
                                <select name="organization_id" class="form-control form-control-sm select2 mr-2" style="min-width: 220px;">
                                    <option value="">{{ __('All Organizations') }}</option>
                                    @foreach ($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ (string) $organizationId === (string) $organization->id ? 'selected' : '' }}>
                                            {{ $organization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="organization_name" value="{{ $organizationName }}" placeholder="{{ __('Organization name') }}" class="form-control form-control-sm mr-2 border-prepend-black p-2">
                            @endunless
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name') }}" class="form-control form-control-sm mr-2 border-prepend-black p-2">
                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body" style="width: 100%;">
                <div class="paralegals-mobile-panel d-md-none">
                    <form method="GET" action="{{ route($listRoute, [app()->getLocale()]) }}" class="paralegals-mobile-search">
                        @unless ($membersMode)
                            <label class="paralegals-mobile-label">{{ __('Organization') }}</label>
                            <select name="organization_id" class="form-control">
                                <option value="">{{ __('All Organizations') }}</option>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}" {{ (string) $organizationId === (string) $organization->id ? 'selected' : '' }}>
                                        {{ $organization->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label class="paralegals-mobile-label">{{ __('Organization name') }}</label>
                            <input type="text" name="organization_name" value="{{ $organizationName }}" class="form-control" placeholder="{{ __('Type organization name') }}">
                        @endunless
                        <label class="paralegals-mobile-label">{{ __('Search by name') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search by name...') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>
                            {{ __('Search') }}
                        </button>
                    </form>
                    <div class="paralegals-mobile-header">
                        <div class="paralegals-mobile-title">{{ $membersMode ? __('Members List') : __('Paralegals List') }}</div>
                        <div class="paralegals-mobile-actions">
                            @if (!auth()->user()->can('isClerk') || auth()->user()->can_register_staff)
                                <a class="btn btn-light btn-sm" href="{{ route('paralegal.create', app()->getLocale()) }}" aria-label="{{ $membersMode ? __('Add Member') : __('Add Paralegal') }}">
                                    <i class="fas fa-plus"></i>
                                </a>
                            @endif
                            @unless ($membersMode)
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Export') }}">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('paralegals.export.pdf', app()->getLocale()) }}{{ $exportQuery }}">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                            {{ __('as pdf') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('paralegals.export.excel', app()->getLocale()) }}{{ $exportQuery }}">
                                            <i class="fas fa-file-excel text-success"></i>
                                            {{ __('as excel') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('paralegals.export.csv', app()->getLocale()) }}{{ $exportQuery }}">
                                            <i class="fas fa-file-csv text-warning"></i>
                                            {{ __('as csv') }}
                                        </a>
                                    </div>
                                </div>
                            @endunless
                        </div>
                    </div>
                    <div class="paralegals-mobile-cards">
                        @forelse ($users as $user)
                            @php
                                $fullName = trim(implode(' ', array_filter([
                                    $user->first_name ?? '',
                                    $user->middle_name ? Str::substr($user->middle_name, 0, 1) . '.' : '',
                                    $user->last_name ?? ''
                                ])));
                            @endphp
                            <div class="paralegal-mobile-card">
                                <div class="paralegal-mobile-card__top">
                                    <div>
                                        <div class="paralegal-mobile-card__name">{{ $fullName }}</div>
                                        <div class="paralegal-mobile-card__username">{{ '@'.$user->name }}</div>
                                    </div>
                                    <div class="dropdown paralegal-mobile-menu">
                                        <button class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('user.show', [app()->getLocale(), $user->name]) }}">
                                                <i class="fas fa-eye text-primary"></i>
                                                {{ __('View') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('user.edit', [app()->getLocale(), $user->name]) }}">
                                                <i class="fas fa-pencil-square-o text-warning"></i>
                                                {{ __('Edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('user.trash', [app()->getLocale(), $user->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item text-danger show_delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="paralegal-mobile-card__meta">
                                    <span><i class="fas fa-envelope"></i> {{ $user->email ?? 'N/A' }}</span>
                                    @unless ($membersMode)
                                        <span><i class="fas fa-building"></i> {{ $user->organization->name ?? 'N/A' }}</span>
                                    @endunless
                                    <span class="badge {{ (bool) $user->is_active ? 'badge-success' : 'badge-secondary' }}">
                                        {{ (bool) $user->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">{{ $membersMode ? __('No members found') : __('No paralegals found') }}</div>
                        @endforelse
                    </div>
                </div>
                <div class="paralegals-table table-responsive d-none d-md-block">
                    <table class="table progress-table text-center table-striped">
                        <thead class="text-capitalize text-white light-custom-color">
                            <tr>
                                <th>{{ __('S/N') }}</th>
                                <th>{{ __('Username') }}</th>
                                <th>{{ __('Full Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                @unless ($membersMode)
                                    <th>{{ __('Organization') }}</th>
                                @endunless
                                <th>{{ __('Status') }}</th>
                                <th colspan="2">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ '@'.$user->name }}</td>
                                <td>
                                    {{ $user->first_name.' ' }}
                                    @if ($user->middle_name === '')
                                        {{ ' ' }}
                                    @else
                                        {{ Str::substr($user->middle_name, 0, 1).'.' }}
                                    @endif
                                    {{ ' '.$user->last_name }}
                                </td>
                                <td>{{ $user->email ?? 'N/A' }}</td>
                                @unless ($membersMode)
                                    <td>{{ $user->organization->name ?? 'N/A' }}</td>
                                @endunless
                                <td>
                                    @if ((bool) $user->is_active === true)
                                    <span class="p-1 {{ 'badge badge-success' }}">
                                        {{ __('Active') }}
                                    </span>
                                    @else
                                    <span class="p-1 {{ 'badge badge-secondary' }}">
                                        {{ __('Inactive') }}
                                    </span>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-between">
                                    <a href="{{ route('user.edit', [app()->getLocale(), $user->name]) }}" title="{{ __('Edit User') }}">
                                        <i class="fas fa-pencil-square-o fa-fw text-warning "></i>
                                    </a> /
                                    <a href="{{ route('user.show', [app()->getLocale(), $user->name]) }}" title="{{ __('User Profile') }}">
                                        <i class="fas fa-user-shield fa-fw text-secondary"></i>
                                    </a> /
                                    <form method="POST" action="{{ route('user.trash', [app()->getLocale(), $user->id]) }}">
                                        @csrf
                                        @method('PUT')
                                            <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{ __('Delete User') }}"></i>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-1">{{ $membersMode ? __('No members found') : __('No paralegals found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $users->count() ? $users->appends(request()->query())->links() : ''}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endpush

@push('scripts')
    @include('modals.confirm-trash')
    <script>
        const searchInputs = document.querySelectorAll('input[name="search"]');
        const organizationNameInputs = document.querySelectorAll('input[name="organization_name"]');
        const organizationSelects = document.querySelectorAll('select[name="organization_id"]');

        searchInputs.forEach((input) => {
            input.addEventListener('input', function () {
                if (this.value === "") {
                    this.form.submit();
                }
            });
        });

        organizationNameInputs.forEach((input) => {
            input.addEventListener('input', function () {
                if (this.value === "") {
                    this.form.submit();
                }
            });
        });

        organizationSelects.forEach((select) => {
            select.addEventListener('change', function () {
                const orgNameInput = this.form ? this.form.querySelector('input[name="organization_name"]') : null;
                if (orgNameInput) {
                    orgNameInput.value = '';
                }
                this.form.submit();
            });
        });
    </script>
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(function() {
            $('.select2').select2({
                placeholder: "{{ __('All Organizations') }}",
                allowClear: true,
                width: 'resolve'
            });
        });
    </script>
@endpush
