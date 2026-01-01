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
        <li><span>{{ __('Legal Aid Provider Profile') }}</span></li>
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
                            <strong>Ooops!</strong> {{ __('Something went wrong!') }}.<br><br>
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
        <!-- Edit legal aid provider area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('Legal Aid Provider Profile') }}
                        <a href=" {{ route('staff.list', app()->getLocale())}}" class="btn btn-sm text-white light-custom-color pull-right">
                            {{ __('Legal Aid Provider List') }}
                        </a>
                    </h4>
                </div>
                @if ($staff->count())
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="media mb-5">
                                        <img class="img-fluid mr-4"
                                            src="
                                            @if (File::exists('storage/uploads/images/profiles/'.$staff->user->image)))
                                                {{ asset('storage/uploads/images/profiles/thumbnails/'.$staff->user->image) }}
                                            @else
                                                {{ asset('assets/images/avatar/avatar.png') }}
                                            @endif
                                            "
                                            style="height: 100%; width: 100px;" alt="staff image">
                                        <div class="media-body">
                                            <h4 class="text-capitalize font-weight-bold text-primary mb-3">
                                                {{ $staff->user->designation->designation.' '
                                                    .$staff->user->first_name.' '
                                                    .$staff->user->middle_name.' '
                                                    .$staff->user->last_name
                                                }}
                                            </h4>
                                            <div class="row">
                                                @if ($staff->disputes->count())
                                                <div class="col-md-3">
                                                    <h6>{{ __('Assigned') }}
                                                        <span class="badge badge-secondary px-2">
                                                            {{ $dispute_total }}
                                                        </span>
                                                    </h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6>{{ __('Resolved') }}
                                                        <span class="badge badge-info px-2">
                                                            {{ $dispute_resolved }}
                                                        </span>
                                                    </h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6>{{ __('Proceeding') }}
                                                        <span class="badge badge-warning px-2">
                                                            {{ $dispute_proceed }}
                                                        </span>
                                                    </h6>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6>{{ __('Success') }}
                                                        <span class="badge badge-success px-2">
                                                            {{ $success_rate.'%' }}
                                                        </span>
                                                    </h6>
                                                </div>
                                            @else
                                                <div class="col-md-3">
                                                    <h6>{{ __('Assigned Disputes') }}
                                                        <span class="badge badge-secondary">{{ '0' }}</span>
                                                    </h6>
                                                </div>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-3">
                                            <label for="username" class="font-weight-bold">{{ __('Username') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="username">@</span>
                                                </div>
                                                <input type="text" readonly class="form-control border-append-primary" id="username"
                                                    value="{{ $staff->user->name }}" aria-describedby="username">
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="first_name" class="font-weight-bold">{{ __('First Name') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" value="{{ $staff->user->first_name }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="middle_name" class="font-weight-bold">{{ __('Middle Name') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" value="{{ $staff->user->middle_name }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="last_name" class="font-weight-bold">{{ __('Last Name') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" value="{{ $staff->user->last_name }}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-3">
                                            <label for="tel_no" class="font-weight-bold">{{ __('Tel') }}</label>
                                            <input type="tel" readonly class="form-control  border-input-primary" value="{{ $staff->user->tel_no }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="email" class="font-weight-bold">{{ __('Email Address') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" value="{{ $staff->user->email }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="office" class="font-weight-bold">{{ __('Office') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" value="{{ $staff->office }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="assignment" class="font-weight-bold">{{ __('Assignment') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" value="{{ ($staff->is_assigned == 1) ? __('assigned') : __('unassigned') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a class="btn text-white light-custom-color float-right" href="{{ route('staff.edit', [app()->getLocale(), $staff]) }}" type="button">
                                        {{ __('Update Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Edit legal aid provider area end -->

    <div class="row">
        <!-- legal aid provider dispute list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">{{ __('Dispute List') }}</div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table table-striped progress-table text-center">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Beneficiary') }}</th>
                                    <th>{{ __('Type of Service') }}</th>
                                    <th>{{ __('Type of Case') }}</th>
                                    <th>{{ __('Dispute Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($disputes->count())
                                    @foreach ($disputes as $dispute)
                                    <tr>
                                        <td>{{ $dispute->id }}</td>
                                        <td>
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}">
                                                {{ $dispute->dispute_no }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                                            {{ $dispute->reportedBy->first_name.' '
                                                .$dispute->reportedBy->middle_name.' '
                                                .$dispute->reportedBy->last_name
                                            }}
                                        </a>
                                    </td>
                                        <td>{{ $dispute->typeOfService->type_of_service }}</td>
                                        <td>{{ $dispute->typeOfCase->type_of_case }}</td>
                                        <td>
                                            {{-- TODO:Add a column color scheme in status table and compare here--}}
                                            <span class="
                                                @if ( $dispute->disputeStatus->dispute_status  === 'resolved')
                                                    text-success
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'pending')
                                                    text-warning font-italic
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'proceeding')
                                                    text-primary
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'continue')
                                                    text-info
                                                @elseif ( $dispute->disputeStatus->dispute_status  === 'referred')
                                                    text-secondary
                                                @else
                                                    text-danger
                                                @endif
                                            ">
                                            {{ $dispute->disputeStatus->dispute_status }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" title="{{ __('Edit Dispute') }}">
                                                <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                            </a> /
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" title="{{ __('View Dispute') }}">
                                                <i class="fas fa-eye fa-fw text-success"></i>
                                            </a>
                                            @can('isSuperAdmin')
                                            /
                                            <a class="link" data-toggle="modal" data-target="#confirmtrash" title="{{ __('Delete Dispute') }}">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="p-1" colspan="7">{{  __('No disputes associated to legal aid provider found') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{ $disputes->count() ? $disputes->links() : ''}}
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection


