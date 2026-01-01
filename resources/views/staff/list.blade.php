@extends('layouts.base')

@section('title', 'AJISO | Legal Aid Providers')

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Legal Aid Providers') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Legal Aid Providers List') }}</span></li>
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
        <!-- Legal Aid Providers list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">{{ __('Legal Aid Providers List') }}
                        <a href="{{ route('staff.create', app()->getLocale()) }}" class="btn btn-sm pull-right text-white light-custom-color">
                            {{ __('Add Legal Aid Provider') }}
                        </a>

                         <form method="GET" action="{{ route('staff.list', [app()->getLocale()]) }}" class="d-flex align-items-center pull-right mb-2">
                                <div class="d-flex align-items-center mr-4">
                                     <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name') }}" class="form-control form-control-sm me-2 border-prepend-black p-2">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                                </div>
                            </form>
                    </div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table progress-table text-center table-striped">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('S/N') }}</th>
                                    <th>{{ __('Username') }}</th>
                                    <th>{{ __('Full Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Office') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($staff->count())
                                    @foreach ($staff as $staf)
                                    <tr>
                                        <td>{{ __($staff->firstItem() + $loop->index) }}</td>
                                        <td>{{ __('@'.$staf->user->name) }}</td>
                                        <td>{{ __($staf->user->first_name.' '.$staf->user->middle_name.' '.$staf->user->last_name) }}</td>
                                        <td>{{ __($staf->user->email) }}</td>
                                        <td>{{ __($staf->office) }}</td>
                                        <td>
                                            <span class="@if ($staf->is_assigned === 1) text-success @else text-danger @endif">
                                                {{ ($staf->is_assigned == 1) ? __('assigned') : __('unassigned') }}
                                            </span>
                                        </td>
                                        <td class="d-flex justify-content-between">
                                            <a href="{{ route('staff.edit', [app()->getLocale(), $staf]) }}" title="{{  __('Edit Legal Aid Provider') }}">
                                                <i class="fas fa-pencil-square-o fa-fw text-warning "></i>
                                            </a> /
                                            <a href="{{ route('staff.show', [app()->getLocale(), $staf]) }}" title="{{  __('View Legal Aid Provider') }}">
                                                <i class="fas fa-eye fa-fw text-success"></i>
                                            </a>
                                            @can('isSuperAdmin')
                                            /
                                            <form method="POST" action="{{ route('staff.trash', [app()->getLocale(), $staf->id]) }}">
                                                @csrf
                                                @METHOD('PUT')
                                                    <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Legal Aid Provider') }}"></i>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="p-1">{{ __('No legal aid providers found') }}</td>
                                </tr>
                                @endif
                                </tbody>
                        </table>
                        {{ $staff->count() ? $staff->links() : '' }}
                    </div>
                </div>
            </div>
        </div>
        <!-- legal aid provider list area end -->
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
