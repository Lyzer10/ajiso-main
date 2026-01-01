@extends('layouts.base')

@php
    $title = __('Disputes') 
@endphp
@section('title', 'LAIS | '.$title)

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
            <li><span>{{ __('LAAC Clinic Visit Sheet') }}</span></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="container mt-4">
            @include('includes.errors-statuses')
        </div>
        <!-- View Dispute area start -->
        <div class="col-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h4 class="header-title">{{ __('LAAC Clinic Visit Sheet')}}
                        <a href="{{ route('disputes.list', [app()->getLocale()]) }}"
                            class="btn btn-sm btn-primary pull-right text-white">{{ __('Disputes List') }}
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    @if ($sheet->count())
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">{{ __('Visit Info') }}</h6>
                                    @php
                                        $date = Carbon\Carbon::parse($sheet->attended_at)->format('d-m-Y') ?? 'N/A';
                                        $time_in = Carbon\Carbon::parse($sheet->time_in)->format('H:i A') ?? '00:00';
                                        $time_out = Carbon\Carbon::parse($sheet->time_out)->format('H:i A') ?? '00:00';
                                    @endphp
                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="disputeNo" class="font-weight-bold">{{ __('Session Id') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text  border-prepend-primary bg-prepend-primary" id="dispute">#</span>
                                                </div>
                                                <input type="text" class="form-control border-append-primary" id="disputeNo"
                                                    value="{{ $sheet->id }}" name="dispute_no" readonly aria-describedby="dispute">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="date" class="font-weight-bold">{{ __('Date Attended') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" id="date" value="{{ $date }}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="time_in" class="font-weight-bold">{{ __('Time In') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" id="time_in" value="{{ $time_in }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="time_out" class="font-weight-bold">{{ __('Time Out') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" id="time_out" value="{{ $time_out }}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="appointment" class="font-weight-bold">{{ __('Appointment Type') }}</label>
                                            <input type="text" readonly class="form-control  border-input-primary" id="appointment" 
                                                value="{{ ((bool) $sheet->is_open == true) ? __("Open") : __('Close') }}">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="problem_description" class="font-weight-bold">{{ __('Steps/Advice Given') }}</label>
                                            <textarea class="form-control border-text-primary" readonly
                                                name="problem_description" required style="width: 100%;">{{ $sheet->advice_given }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-bordered mb-3">
                                <div class="card-body">
                                    <h6 class="card-text mb-3 font-weight-bold">
                                        {{ __('Dispute Attachments') }} 
                                        <span class="badge badge-success">
                                            {{  ($sheet->files->count()) ??  0 }}
                                        </span>
                                    </h6>
                                    <div class="alert-items">
                                        @if ( $sheet->files->count())
                                            @foreach ($sheet->files as $file)
                                                <div class="alert alert-info lead" role="alert">
                                                    <strong>{{ '#'.$file->id }}</strong> | 
                                                    {{ Str::ucfirst($file->name) }} |
                                                    {{  Str::upper($file->file_type) }} | 
                                                    2 MB |
                                                    <a href="{{ asset(str_replace('public/', 'storage/', $file->path)) }}">
                                                        {{ __('Download / View') }}
                                                    </a>
                                                </div>
                                            @endforeach
                                            @else
                                            <div class="alert alert-danger lead" role="alert">
                                                <strong><i class="fas fa-exclamation-triangle text-warning"></i></strong> 
                                                {{ __('Files Not Found.') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="container">
                        {{ __('No information found on this sheet, please try again.') }}
                    </div>
                    @endif
                </div>
                <!-- View Dispute area end -->
            </div>
        </div>
    </div>
@endsection
{{-- TODO : Remove these if not applicable --}}
@push('scripts')

    @include('dates.js')

    {{-- Select2 --}}
    <script type="text/javascript">
        $(function() {
            $('.select2').select2();
        });
    </script>

    {{-- Date Picker --}}
    <script type="text/javascript">
        $(function() {
            $('#attended_at').datetimepicker({
                format: 'L',
                viewMode: 'years'
            });

        });
    </script>
@endpush
