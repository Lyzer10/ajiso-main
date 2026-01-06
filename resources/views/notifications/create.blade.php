@extends('layouts.base')

@php
    $title = __('Notifications') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    @include('dates.css')
@endpush

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Notifications') }}</h4>
        <ul class="breadcrumbs pull-left">
            @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @elsecanany(['isStaff'])
                <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
            @endcanany
            <li><span>{{ __('Create Notification') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- beneficiary list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="header-title">
                        {{ __('Publish Notification') }}
                        <a href="{{ route('notifications.list', app()->getLocale()) }}" class="btn btn-sm text-white light-custom-color pull-right text-white">
                            {{ __('Notifications List') }}
                        </a>
                    </h4>
                </div>
                <form action="{{ route('notification.store', app()->getLocale()) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="font-weight-bold">{{ __('Notification Title') }}<sup class="text-danger">*</sup></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control border-append-primary" id="title" placeholder="Title" 
                                                name="title" aria-describedby="notification_title" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="priority" class="font-weight-bold">{{ __('Priority') }}<sup class="text-danger">*</sup></label>
                                        <select  id="priority" class="select2 select2-container--default border-input-primary py-2 @error('priority') is-invalid @enderror" name="priority"
                                            required autocomplete="priority" autofocus style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose priority') }}</option>
                                            <option value="high">{{ __('High') }}</option></option>
                                            <option value="medium">{{ __('Medium') }}</option>
                                            <option value="low">{{ __('Low') }}</option>
                                        </select>
                                        @error('priority')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="publish_to" class="font-weight-bold">{{ __('Publish To') }}<sup class="text-danger">*</sup></label>
                                        <select  id="publish_to" class="select2 select2-container--default border-input-primary py-2 @error('publish_to') is-invalid @enderror"
                                            name="publish_to" value="{{ old('publish_to') }}"required autocomplete="publish_to" autofocus style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose a recipient group') }}</option>
                                            <option value="allUsers">{{ __('All Users') }}</option>
                                            <option value="allLegalAidProviders">{{ __('All Legal Aid Providers') }}</option>
                                            <option value="allBeneficiaries">{{ __('All Beneficiaries') }}</option>
                                            <option value="targetlLegalAidProvider">{{ __('Target Legal Aid Provider') }}</option>
                                            <option value="targetBeneficiary">{{ __('Target Beneficiary') }}</option>
                                        </select>
                                        @error('publish_to')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="allUsers">
                                        <label for="all_users" class="font-weight-bold">{{ __('All Users') }}<sup class="text-danger">*</sup></label>
                                        <select id="all_users"
                                            class="select2 select2-container--default border-input-primary  @error('all_users') is-invalid @enderror"
                                            name="all_users" disabled required autocomplete="all_users" style="width: 100%;">
                                            <option hidden disabled value>{{ __('All') }}</option>
                                        </select>
                                        @error('all_users')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="allLegalAidProviders">
                                        <label for="all_laps" class="font-weight-bold">{{ __('All Legal Aid Providers') }}<sup class="text-danger">*</sup></label>
                                        <select id="all_laps"
                                            class="select2 select2-container--default border-input-primary  @error('all_laps') is-invalid @enderror"
                                            name="all_laps" disabled required autocomplete="all_laps" style="width: 100%;">
                                            <option hidden disabled value>{{ __('All Legal Aid Providers') }}</option>
                                        </select>
                                        @error('all_laps')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="allBeneficiaries">
                                        <label for="all_beneficiaries" class="font-weight-bold">{{ __('All Beneficiaries') }}<sup class="text-danger">*</sup></label>
                                        <select id="all_beneficiaries"
                                            class="select2 select2-container--default border-input-primary  @error('all_beneficiaries') is-invalid @enderror"
                                            name="all_beneficiaries" disabled required autocomplete="all_beneficiaries" style="width: 100%;">
                                            <option hidden disabled value>{{ __('All Beneficiaries') }}</option>
                                        </select>
                                        @error('all_beneficiaries')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="targetlLegalAidProvider">
                                        <label for="target_legal_aid_provider" class="font-weight-bold">{{ __('Legal Aid Provider') }}<sup class="text-danger">*</sup></label>
                                        <select id="target_legal_aid_provider" aria-describedby="selectLAP"
                                            class="select2 select2-container--default border-input-primary @error('legal aid provider') is-invalid @enderror"
                                            name="legal_aid_provider" required autocomplete="legal_aid_provider" style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose legal aid provider') }}</option>
                                                @if ($staff->count())
                                                    @foreach ($staff as $staf)
                                                        <option value="{{ $staf->id }}">
                                                            {{ $staf->user->first_name.' '
                                                                .$staf->user->middle_name.' '
                                                                .$staf->user->last_name.' | '
                                                                .$staf->center->location
                                                            }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option>{{ __('No legal aid provider found') }}</option>
                                                @endif
                                        </select>
                                        @error('legal_aid_provider')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6" id="targetBeneficiary">
                                        <label for="target_beneficiary" class="font-weight-bold">{{ __('Beneficiary') }}<sup class="text-danger">*</sup></label>
                                        <select id="target_beneficiary" aria-describedby="selectBeneficiary"
                                            class="select2 select2-container--default  border-input-primary @error('beneficiary') is-invalid @enderror"
                                            name="beneficiary" required autocomplete="beneficiary"  style="width: 100%;">
                                            <option hidden disabled selected value>{{ __('Choose beneficiary') }}</option>
                                            @if ($beneficiaries->count())
                                                @foreach ($beneficiaries as $beneficiary)
                                                    <option value="{{ $beneficiary->id }}">
                                                        {{ $beneficiary->user->user_no.' | '
                                                            .$beneficiary->user->first_name.' '
                                                            .$beneficiary->user->middle_name.' '
                                                            .$beneficiary->user->last_name
                                                        }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option>{{ __('No beneficiaries found') }}</option>
                                            @endif
                                        </select>
                                        @error('beneficiary')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="message" class="font-weight-bold">{{ __('Message') }}<sup class="text-danger">*</sup></label>
                                <textarea class="form-control border-text-primary @error('message') is-invalid @enderror"
                                    name="message" value="{{ old('message') }}" required autocomplete="message" style="width: 100%;"></textarea>
                                @error('message')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-2">
                        <button class="btn text-white light-custom-color float-right" type="submit">{{ __('Publish') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- dispute list area end -->
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

        {{-- Report filters --}}
        <script>
            $(function () {
    
                $('#allLegalAidProviders').prop("hidden", true);
                $('#allBeneficiaries').prop("hidden", true);
                $('#targetlLegalAidProvider').prop("hidden", true);$('#target_legal_aid_provider').prop("disabled", true);
                $('#targetBeneficiary').prop("hidden", true);$('#target_beneficiary').prop("disabled", true);
    
                $('select#publish_to').on('input', function (){
    
                var filter = $(this).find(":selected").val();
    
                if (filter === 'allUsers') {
    
                    $('#allUsers').prop("hidden", false);
                    $('#allLegalAidProviders').prop("hidden", true);
                    $('#allBeneficiaries').prop("hidden", true);
                    $('#targetallBeneficiarieslLegalAidProvider').prop("hidden", true);
                    $('#targetBeneficiary').prop("hidden", true);
    
                }else if (filter === 'allLegalAidProviders') {
    
                    $('#allLegalAidProviders').prop("hidden", false);
                    $('#allUsers').prop("hidden", true);
                    $('#allBeneficiaries').prop("hidden", true);
                    $('#targetlLegalAidProvider').prop("hidden", true);
                    $('#targetBeneficiary').prop("hidden", true);
    
                }else if (filter === 'allBeneficiaries'){
    
                    $('#allBeneficiaries').prop("hidden", false);
                    $('#allLegalAidProviders').prop("hidden", true);
                    $('#allUsers').prop("hidden", true);
                    $('#targetlLegalAidProvider').prop("hidden", true);
                    $('#targetBeneficiary').prop("hidden", true);
    
                }if (filter === 'targetlLegalAidProvider') {

                    $('#targetlLegalAidProvider').prop("hidden", false);
                    $('#target_legal_aid_provider').prop("disabled", false);
                    $('#target_beneficiary').prop("disabled", true);
                    $('#allLegalAidProviders').prop("hidden", true);
                    $('#allBeneficiaries').prop("hidden", true);
                    $('#allUsers').prop("hidden", true);
                    $('#targetBeneficiary').prop("hidden", true);
    
                }else if (filter === 'targetBeneficiary'){
    
                    $('#targetBeneficiary').prop("hidden", false);
                    $('#target_beneficiary').prop("disabled", false);
                    $('#target_legal_aid_provider').prop("disabled", true);
                    $('#allLegalAidProviders').prop("hidden", true);
                    $('#allBeneficiaries').prop("hidden", true);
                    $('#targetlLegalAidProvider').prop("hidden", true);
                    $('#allUsers').prop("hidden", true);
    
                }
    
                });
            });
    
        </script>

@endpush
