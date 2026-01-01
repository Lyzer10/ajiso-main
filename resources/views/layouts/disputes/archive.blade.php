@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'AJISO | '.$title)

@push('styles')
    @include('dates.css')
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
            <li><span>{{ __('Confirm Archived Dispute') }}</span></li>
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
        <!-- beneficiary list area start -->
        <div class="col-md-12 mt-5 mb-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="header-title">
                        @if(Request::is('disputes/archive/select') )
                            {{ __('Search Archived Dispute')}}
                        @else
                            {{ __('Confirm Archived Dispute')}}
                        @endif
                        <a href="{{ route('disputes.list', app()->getLocale()) }}"
                            class="btn btn-sm text-white light-custom-color pull-right text-white">
                            {{ __('Disputes List') }}
                        </a>
                    </h4>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <form action="{{ route('dispute.search.archive', app()->getLocale()) }}" method="GET" role="search">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group mt-3">
                                    <select id="dispute" class="livesearch form-control border-prepend-primary py-2 @error('reported_on') is-invalid @enderror" name="dispute"
                                    required autocomplete="dispute" autofocus></select>
                                    <div class="input-group-append">
                                        <button class="input-group-text  border-append-primary bg-prepend-primary py-2" type="submit">{{ __('Search') }} </button>
                                    </div>
                                </div>
                                @error('dispute')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                @yield('subsection')
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

    {{-- Dispute search from archive --}}
    <script type="text/javascript">
        $(function() {
            $('.livesearch').select2({
                placeholder: '{{ __("Search case by dispute number or beneficiary number...") }}',
                ajax: {
                    url: '/en/disputes/archive/search/live',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: 'Dispute #'+item.dispute_no+' | Reported by '+item.reported_by.first_name+
                                            ' '+item.reported_by.middle_name+' '+item.reported_by.last_name+
                                            ' | On : '+item.reported_on.toLocaleString('en-US')+' | With status : '+item.dispute_status.dispute_status,
                                    id: item.id

                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush
