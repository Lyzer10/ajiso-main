@extends('layouts.base')

@php
    $title = __('Beneficiaries') 
@endphp
@section('title', 'LAIS | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Beneficiaries') }}</h4>
            <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Beneficiary Profile') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    @if ($beneficiary->count())
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
        <!-- Edit Beneficiary area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">
                        {{ __('Beneficiary Profile') }}
                        @cannot('isStaff')
                        <a href=" {{ route('beneficiaries.list', app()->getLocale())}}" class="btn btn-sm text-white light-custom-color pull-right">
                            {{ __('Beneficiaries List') }}
                        </a>
                        @endcannot
                    </h4>
                </div>
                @if ($beneficiary->count())
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="media mb-5">
                                        <img class="img-fluid mr-4"
                                            src="
                                            @if (File::exists('storage/uploads/images/profiles/'.$beneficiary->user->image))
                                                {{ asset('storage/uploads/images/profiles/thumbnails/'.$beneficiary->user->image) }}
                                            @else
                                                {{ asset('assets/images/avatar/avatar.png') }}
                                            @endif
                                            "
                                            style="height: 100%; width: 100px; " alt="beneficiary image">
                                        <div class="media-body">
                                            <h4 class="text-capitalize font-weight-bold text-primary mb-3">
                                                {{ $beneficiary->user->first_name.' '
                                                    .$beneficiary->user->middle_name.' '
                                                    .$beneficiary->user->last_name
                                                }}
                                            </h4>
                                            @php
                                                $date_reported = Carbon\Carbon::parse($beneficiary->created_at)->format('d, F Y') ?? '0';
                                            @endphp

                                            @if(app()->isLocale('en'))
                                                {{ $beneficiary->user->first_name.' is a '.$beneficiary->age.' years old' }}
                                                @if ($beneficiary->gender === 'male')
                                                    {{ 'man,' }}
                                                    @php $gen = 'He'; @endphp
                                                @else
                                                    {{ 'woman,' }}
                                                    @php $gen = 'She'; @endphp
                                                @endif
                                                {{ 'currently living at '
                                                    .$beneficiary->district->district.', in '.$beneficiary->region->region.' region.'
                                                    .$gen.' first engaged our services on '
                                                    .$date_reported
                                                    .'. Here is a bit more information about '
                                                    .$beneficiary->user->first_name
                                                }}

                                            @else
                                                {{ $beneficiary->user->first_name.' ni' }}
                                                @if ($beneficiary->gender === 'male')
                                                    @if ($beneficiary->age > 19)
                                                        {{ 'mwanume' }}
                                                    @else
                                                        {{ 'mvulana' }}
                                                    @endif
                                                    @php $gen = 'He'; @endphp
                                                @else
                                                    @if ($beneficiary->age > 19)
                                                        {{ 'mwanamke' }}
                                                    @else
                                                        {{ 'msichana' }}
                                                    @endif
                                                    @php $beneficiary->gen = 'She'; @endphp
                                                @endif
                                                {{ ' mwenye umri wa miaka '.$beneficiary->age.
                                                    '. kwa sasa anaishi '.$beneficiary->district->district.
                                                    ', Mkoani '.$beneficiary->region->region.
                                                    '. Alifuata huduma zetu kwa mara ya kwanza mnamo '.$date_reported.
                                                    '. Hizi ni taarifa zaidi kuhusu '.$beneficiary->user->first_name
                                                }}

                                            @endif
                                            {{ '...' }}
                                            <div class="row">
                                                @if ($beneficiary->disputes->count())
                                                    @foreach ($beneficiary->disputes->unique('type_of_case_id') as $dispute)
                                                        <span class="badge badge-success p-1 mx-2">
                                                            {{ __($dispute->typeOfCase->type_of_case) }}
                                                            <i class="fas fa-check ml-1"></i> </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <form class="needs-validation" novalidate="">
                                        <fieldset class="form-group border p-2">
                                            <legend class="w-auto pl-2 h6 font-weight-bold">{{ __('~ Personal Info')}}</legend>
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="user_no" class="font-weight-bold">{{ __('File No') }}<sup class="text-danger">*</sup></label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="inputGroupUserNo">#</span>
                                                        </div>
                                                        <input readonly type="text" class="form-control border-append-primary" id="user_no" placeholder="User Id"
                                                            value="{{ $beneficiary->user->user_no }}" aria-describedby="inputGroupUser" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="first_name" class="font-weight-bold">{{ __('First Name') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ $beneficiary->user->first_name }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="middle_name" class="font-weight-bold">{{ __('Middle Name') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ $beneficiary->user->middle_name }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="last_name" class="font-weight-bold">{{ __('Last Name') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ $beneficiary->user->last_name }}">
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
                                                            value="{{ $beneficiary->user->name }}" aria-describedby="username">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="gender" class="font-weight-bold">{{ __('Gender') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->gender) }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="date_of_birth" class="font-weight-bold">{{ __('Age') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary"
                                                        value="{{ $beneficiary->age }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="disabled" class="font-weight-bold">{{ __('Disabled') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->disabled) }}">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label class="education_level font-weight-bold">{{ __('Education Level') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->educationLevel->education_level) }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="tel_no" class="font-weight-bold">{{ __('Telephone No') }}</label>
                                                    <input type="tel" readonly class="form-control  border-input-primary" value="{{ $beneficiary->user->tel_no }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="tel_no" class="font-weight-bold">{{ __('Telephone No 2') }}</label>
                                                    <input type="tel" readonly class="form-control  border-input-primary" value="{{ $beneficiary->user->mobile_no }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="status" class="font-weight-bold">{{ __('Status') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ ((bool) $beneficiary->user->is_active == true) ? __('Active') : __('Inactive') }}">
                                                </div>    
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label class="region font-weight-bold">{{ __('Region') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ $beneficiary->region->region }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="district font-weight-bold">{{ __('District') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ $beneficiary->district->district }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="ward" class="font-weight-bold">{{ __('Ward') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ $beneficiary->ward }}">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group border p-2">
                                            <legend class="w-auto pl-2 h6 font-weight-bold"> {{ __('~ Marital Information') }}</legend>
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label class="marital_status font-weight-bold">{{ __('Maritial Status') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->maritalStatus->marital_status) }}">
                                                </div>

                                                {{-- <div class="col-md-3 mb-3">
                                                    <label for="marriage_form" class=" font-weight-bold">{{ __('Form of Marriage') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->marriageForm->marriage_form) }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="marriage_date" class="font-weight-bold">{{ __('Marriage Date') }}</label>
                                                   <input type="text" readonly class="form-control border-input-primary" 
       value="{{ ($beneficiary->marriage_date && $beneficiary->marriage_date !== 'N/A') ? \Carbon\Carbon::parse($beneficiary->marriage_date)->format('d/m/Y') : 'N/A' }}">

                                                </div> --}}

                                                <div class="col-md-3 mb-3">
                                                    <label for="no_of_children" class="font-weight-bold">{{ __('Number of Children') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary"
                                                        value="{{ ($beneficiary->no_of_children != 0) ? $beneficiary->no_of_children : 'N/A' }}">
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-group border p-2">
                                            <legend class="w-auto pl-2 h6 font-weight-bold">{{ __('~ Financial Details') }}</legend>
                                            <div class="form-row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="financial_capability" class="font-weight-bold">{{ __('Financial Capability') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->financial_capability) }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="employment_status" class="font-weight-bold">{{ __('Employment Status') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" value="{{ __($beneficiary->employmentStatus->employment_status) }}">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="occupation_business" class="font-weight-bold">{{ __('Occupation / Business') }}</label>
                                                    <input type="text" readonly class="form-control  border-input-primary" id="occupation" value="{{ $beneficiary->occupation_business }}">
                                                </div>
                                            </div>
                                        </fieldset>
                                </div>
                                @cannot('isStaff')
                                    <div class="card-footer">
                                        <a class="btn text-white light-custom-color float-right" href="{{ route('beneficiary.edit', [app()->getLocale(), $beneficiary]) }}" type="button">{{ __('Update Details') }}</a>
                                    </div>
                                @endcannot
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Edit Beneficiary area end -->
    @endif
    <div class="row">
        <!-- beneficiary dispute list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="header-title clearfix">{{ __('Disputes List') }}</div>
                </div>
                <div class="card-body" style="width: 100%;">
                    <div class="table-responsive">
                        <table class="table table-striped progress-table text-center">
                            <thead class="text-capitalize text-white light-custom-color">
                                <tr>
                                    <th>{{ __('Id') }}</th>
                                    <th>{{ __('Dispute No') }}</th>
                                    <th>{{ __('Type of Service') }}</th>
                                    <th>{{ __('Type of Case') }}</th>
                                    <th>{{ __('Dispute Status') }}</th>
                                    <th>{{ __('Legal Aid Provider') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($beneficiary->disputes->count())
                                    @foreach ($beneficiary->disputes as $dispute)
                                    <tr>
                                        <td>{{ $dispute->id }}</td>
                                        <td>
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}">
                                                {{ $dispute->dispute_no }}
                                            </a>
                                            </td>
                                        <td>{{ $dispute->typeOfService->type_of_service }}</td>
                                        <td>{{ $dispute->typeOfCase->type_of_case }}</td>
                                        <td>
                                            @php
                                                $statusSlug = \Illuminate\Support\Str::slug($dispute->disputeStatus->dispute_status);
                                            @endphp
                                            <span class="badge-status status-{{ $statusSlug }}">
                                                {{ $dispute->disputeStatus->dispute_status }}
                                            </span>
                                        </td>
                                    <td>
                                        @if (is_null($dispute->assignedTo))
                                            <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                                {{ __('Unassigned') }}
                                            </a>
                                        @else
                                            <a href="{{ route('staff.show', [app()->getLocale(), $dispute->staff_id]) }}">
                                                {{ $dispute->assignedTo->first_name.' '
                                                    .$dispute->assignedTo->middle_name.' '
                                                    .$dispute->assignedTo->last_name
                                                }}
                                            </a>
                                        @endif
                                    </td>
                                    </td>
                                        <td>
                                            <a href="{{ route('dispute.edit', [app()->getLocale(), $dispute->id]) }}" title="{{ __('Edit Dispute') }}">
                                                <i class="fas fa-pencil-square-o fa-fw text-warning"></i>
                                            </a> /
                                            <a href="{{ route('dispute.show', [app()->getLocale(), $dispute->id]) }}" title="{{ __('View Dispute') }}">
                                                <i class="fas fa-eye fa-fw text-success"></i>
                                            </a>
                                            @canany(['isSuperAdmin', 'isAdmin'])
                                                /
                                                <a class="link" data-toggle="modal" data-target="#confirmtrash" title="{{ __('Delete Dispute') }}">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </a>
                                            @endcanany
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="p-1" colspan="7">
                                        {{ __('No disputes associated to beneficiary found') }}
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection
