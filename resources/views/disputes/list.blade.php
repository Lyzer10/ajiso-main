@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}" />
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Disputes') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Disputes List') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container">
            @include('includes.errors-statuses')
        </div>
        <!-- beneficiary list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">
                        @canany(['isStaff'])
                            {{ __('My Disputes list') }}
                             <form method="GET" action="{{ route('disputes.my.list', [app()->getLocale(), auth()->user()->staff->id]) }}" class="d-flex align-items-center pull-right mb-2 dispute-filter">
                                <div class="d-flex align-items-center mr-4">
                                     <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by beneficiary') }}" class="form-control form-control-sm me-2 border-prepend-black p-2">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                                </div>

                                <select name="status" class="select2 select2-container--default border-input-primary" style="width: 180px;" onchange="this.form.submit()">
                                    <option value="">{{ __('All Cases') }}</option>
                                    <option value="proceeding" {{ request('status') == 'proceeding' ? 'selected' : '' }}>
                                        {{ __('Proceeding') }}
                                    </option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                        {{ __('Resolved') }}
                                    </option>
                                </select> 
                            </form>
                        @elsecanany(['isSuperAdmin', 'isAdmin', 'isClerk'])
                            {{ __('Disputes list') }}

                            <a class="btn btn-sm text-white light-custom-color dropdown-toggle pull-right ml-4" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Add Dispute') }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-md-right" aria-labelledby="bd-versions">
                                <a class="dropdown-item btn-link" href="{{ route('dispute.create.new', app()->getLocale()) }}">{{ __('New') }}</a>
                                <a class="dropdown-item btn-link" href="{{ route('dispute.select.archive', app()->getLocale()) }}">{{ __('Archived') }}</a>
                            </div>

                             <form method="GET" action="{{ route('disputes.list', [app()->getLocale()]) }}" class="d-flex align-items-center pull-right mb-2 dispute-filter">
                                <div class="d-flex align-items-center mr-4">
                                     <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by beneficiary') }}" class="form-control form-control-sm me-2 border-prepend-black p-2">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                                </div>
                                <select name="status" class="select2 select2-container--default border-input-primary" style="width: 200px;" onchange="this.form.submit()">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @if (!empty($dispute_statuses) && $dispute_statuses->count())
                                        @foreach ($dispute_statuses as $dispute_status)
                                            <option value="{{ $dispute_status->id }}" {{ (string) request('status') === (string) $dispute_status->id ? 'selected' : '' }}>
                                                {{ __($dispute_status->dispute_status) }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">{{ __('No statuses found') }}</option>
                                    @endif
                                </select>
                            </form>
                        @endcanany
                    </div>
                </div>
                <div class="card-body"style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table table-striped progress-table text-center">
                            @canany(['isSuperAdmin', 'isAdmin', 'isClerk'])
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('S/N') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Beneficiary') }}</th>
                                    <th>{{ __('Legal Aid Provider') }}</th>
                                    <th>{{ __('Reported') }}</th>
                                    <th>{{ __('Dispute Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if ($disputes->count())
                                @foreach ($disputes as $dispute)
                                <tr>
                                    <td>{{ $disputes->firstItem() + $loop->index }}</td>
                                    <td>
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" class="text-secondary" title="{{ __('Click to view dispute') }}">
                                            {{ $dispute->dispute_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                                            {{ $dispute->reportedBy->first_name.' '
                                                .$dispute->reportedBy->middle_name.' '.
                                                $dispute->reportedBy->last_name
                                            }}
                                        </a>
                                    </td>
                                    <td>
                                        @if (is_null($dispute->staff_id))
                                            @canany(['isAdmin', 'isStaff', 'isSuperAdmin'])
                                                <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                {{ __('Unassigned') }}
                                                </a>
                                            @elsecanany(['isClerk', 'isStaff'])
                                                <a class="text-danger" >{{ __('Unassigned') }}</a>
                                            @endcanany
                                        @else
                                            @canany(['isAdmin', 'isStaff', 'isSuperAdmin'])
                                            <a href="{{ route('staff.show', [app()->getLocale(), $dispute->staff_id, ]) }}" title="{{  __('Click to view assigned legal aid provider') }}">
                                                {{ $dispute->assignedTo->first_name.' '
                                                    .$dispute->assignedTo->middle_name.' '
                                                    .$dispute->assignedTo->last_name
                                                }}
                                            </a>
                                            @elsecanany(['isClerk', 'isStaff'])
                                                    <a class="text-danger" >
                                                        {{ $dispute->assignedTo->first_name.' '
                                                            .$dispute->assignedTo->middle_name.' '
                                                            .$dispute->assignedTo->last_name
                                                        }}
                                                    </a>
                                            @endcanany
                                        @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($dispute->reported_on)->diffForHumans() }}</td>
                                    <td>
                                        @php
                                            $statusSlug = \Illuminate\Support\Str::slug($dispute->disputeStatus->dispute_status);
                                        @endphp
                                        <span class="badge-status status-{{ $statusSlug }}">
                                            {{ __($dispute->disputeStatus->dispute_status) }}
                                        </span>
                                    </td>
                                    <td class="d-flex justify-content-between">
                                        <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" title="{{ __('Edit Dispute') }}">
                                            <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                        </a> /
                                        <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" title="{{ __('View Dispute') }}">
                                            <i class="fas fa-eye fa-fw text-success"></i>
                                        </a>
                                        @can(['isSuperAdmin'])
                                            /
                                            <form method="POST" action="{{ route('dispute.trash', [app()->getLocale(), $dispute->id]) }}">
                                                @csrf
                                                @METHOD('PUT')
                                                    <i class="fas fa-trash-alt fa-fw text-danger show_delete" data-toggle="tooltip" title="{{  __('Delete Dispute"') }}"></i>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="p-1">{{ __('No disputes found') }}</td>
                            </tr>
                            @endif
                            </tbody>
                            @endcanany
                        </table>
                    </div>
                    {{ $disputes->count() ? $disputes->links() : ''}}
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection

@push('scripts')
    {{-- Include the sweetalert --}}
    @include('modals.confirm-trash')

    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $('.select2').select2({
                minimumResultsForSearch: Infinity
            });
        });
    </script>

     <script>
    const searchInput = document.querySelector('input[name="search"]');

    searchInput.addEventListener('input', function () {
        if (this.value === "") {
            this.form.submit(); // auto reload full list
        }
    });
</script>

@endpush
