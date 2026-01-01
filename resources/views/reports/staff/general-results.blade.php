@extends('layouts.reports.general')

@section('subsection')
    <div class="card-header">
        <h4 class="header-title">{{ __('General Report')}}
            <a href="{{ route('reports.general.staff', app()->getLocale()) }}"
                class="btn btn-sm btn-primary pull-right text-white">
                {{ __('Back') }}
            </a>
        </h4>
    </div>
@endsection
