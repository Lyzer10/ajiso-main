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
            <div class="card-header beneficiaries-desktop-header d-none d-md-block">
                <div class="header-title clearfix">
                    <div class="header-title clearfix">{{ __('Beneficiary List') }}
                        <a href="{{ route('beneficiary.create', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right">
                            {{ __('Add Beneficiary') }}
                        </a>
                        @php
                            $exportSearch = request('search');
                            $exportQuery = $exportSearch ? '?search=' . urlencode($exportSearch) : '';
                        @endphp
                        <div class="dropdown pull-right mr-2">
                            <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-download fa-fw"></i>
                                {{ __('Export') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('beneficiaries.export.pdf', app()->getLocale()) }}{{ $exportQuery }}">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    {{ __('as pdf') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('beneficiaries.export.excel', app()->getLocale()) }}{{ $exportQuery }}">
                                    <i class="fas fa-file-excel text-success"></i>
                                    {{ __('as excel') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('beneficiaries.export.csv', app()->getLocale()) }}{{ $exportQuery }}">
                                    <i class="fas fa-file-csv text-warning"></i>
                                    {{ __('as csv') }}
                                </a>
                            </div>
                        </div>

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
                <div class="beneficiaries-mobile-panel d-md-none">
                    <form method="GET" action="{{ route('beneficiaries.list', [app()->getLocale()]) }}" class="beneficiaries-mobile-search">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search by name or file number...') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>
                            {{ __('Search') }}
                        </button>
                    </form>
                    <div class="beneficiaries-mobile-header">
                        <div class="beneficiaries-mobile-title">{{ __('Beneficiary List') }}</div>
                        <div class="beneficiaries-mobile-actions">
                            <a href="{{ route('beneficiary.create', app()->getLocale()) }}" class="btn btn-light btn-sm" aria-label="{{ __('Add Beneficiary') }}">
                                <i class="fas fa-plus"></i>
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Export') }}">
                                    <i class="fas fa-download"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('beneficiaries.export.pdf', app()->getLocale()) }}{{ $exportQuery }}">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        {{ __('as pdf') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('beneficiaries.export.excel', app()->getLocale()) }}{{ $exportQuery }}">
                                        <i class="fas fa-file-excel text-success"></i>
                                        {{ __('as excel') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('beneficiaries.export.csv', app()->getLocale()) }}{{ $exportQuery }}">
                                        <i class="fas fa-file-csv text-warning"></i>
                                        {{ __('as csv') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="beneficiaries-mobile-cards">
                        @if ($beneficiaries->count())
                            @foreach ($beneficiaries as $beneficiary)
                                @php
                                    $beneficiaryName = trim(implode(' ', array_filter([
                                        $beneficiary->user->first_name ?? '',
                                        $beneficiary->user->middle_name ? Str::substr($beneficiary->user->middle_name, 0, 1) . '.' : '',
                                        $beneficiary->user->last_name ?? ''
                                    ])));
                                @endphp
                                <div class="beneficiary-mobile-card">
                                    <div class="beneficiary-mobile-card__top">
                                        <div class="beneficiary-mobile-card__name">{{ $beneficiaryName }}</div>
                                        <div class="dropdown beneficiary-mobile-menu">
                                            <button class="btn btn-light btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ route('beneficiary.show', [app()->getLocale(), $beneficiary]) }}">
                                                    <i class="fas fa-eye text-primary"></i>
                                                    {{ __('View') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('beneficiary.edit', [app()->getLocale(), $beneficiary]) }}">
                                                    <i class="fas fa-pencil-square-o text-warning"></i>
                                                    {{ __('Edit') }}
                                                </a>
                                                @can('isSuperAdmin')
                                                    <form method="POST" action="{{ route('beneficiary.trash', [app()->getLocale(), $beneficiary->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item text-danger show_delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="beneficiary-mobile-card__file">{{ $beneficiary->user->user_no }}</div>
                                    <div class="beneficiary-mobile-card__meta">
                                        <span><i class="fas fa-phone-alt"></i> {{ $beneficiary->user->tel_no ?: 'N/A' }}</span>
                                        <span class="beneficiary-mobile-card__gender">{{ __($beneficiary->gender) }}</span>
                                        <span class="beneficiary-mobile-card__cases">{{ $beneficiary->disputes_count ?? 0 }} {{ __('cases') }}</span>
                                        <span class="badge badge-success beneficiary-mobile-card__status">
                                            {{ (bool) $beneficiary->user->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info mb-0">{{ __('No beneficiaries found') }}</div>
                        @endif
                    </div>
                    @if ($beneficiaries->count())
                        <div class="beneficiaries-mobile-pagination">
                            {{ $beneficiaries->links() }}
                        </div>
                    @endif
                </div>
                <div class="beneficiaries-table table-responsive d-none d-md-block">
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
                                        </span>
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
                                            @method('PUT')
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
    const searchInputs = document.querySelectorAll('input[name="search"]');

    searchInputs.forEach((input) => {
        input.addEventListener('input', function () {
            if (this.value === "") {
                this.form.submit(); // auto reload full list
            }
        });
    });
</script>
@endpush
