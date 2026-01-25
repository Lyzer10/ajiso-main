@extends('layouts.base')

@php
    $title = __('Notifications') 
@endphp
@section('title', 'AJISO | '.$title)

@section('breadcrumb')
    <div class="breadcrumbs-area clearfix">
        <h4 class="page-title pull-left">{{ __('Notifications') }}</h4>
        <ul class="breadcrumbs pull-left">
                @canany(['isSuperAdmin','isAdmin', 'isClerk'])
                    <li><a href="{{ route('admin.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
                @elsecanany(['isStaff'])
                    <li><a href="{{ route('staff.home', app()->getLocale())}}">{{ __('Home') }}</a></li>
                @endcanany
            <li><span>{{ __('My Notifications') }}</span></li>
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
                <div class="card-header d-flex justify-content-between">
                    <h4 class="header-title ">{{  __('My Notifications') }}</h4>
                    @can('isSuperAdmin')
                    <form action="{{  route('notification.delete', app()->getLocale()) }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-sm light-custom-color pull-right text-white">
                            {{ __('Clean Notifications') }}
                        </button>
                    </form>
                    @endcan
                </div>
                <div class="card-body mb-3">
                    @forelse($notifications as $notification)
                        <div class="alert alert-info alert-dismissible">
                            {{ data_get($notification->data, 'message', __('Notification')).' | '.Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            <a href="#" class="mark-as-read float-right text-secondary" data-id="{{ $notification->id }}">
                                {{  __('Mark as Read') }}
                            </a>
                        </div>
                        @if ($loop->last)
                            <a href="#" id="mark-all">
                                {{ __('Mark all as read') }}
                            </a>
                        @endif
                    @empty
                        <div class="alert alert-info alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ __('There are no new notifications') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- dispute list area end -->
    </div>
@endsection

@push('scripts')

    {{-- Send AJAX calls to mark notifications as read --}}
    <script type="text/javascript">
        function sendMarkRequest(id = null) {
            return $.ajax("{{ route('notification.mark', app()->getLocale()) }}", {
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id":id,
                },
            });
        }
        
        $(function() {
            $('.mark-as-read').click(function() {

                let request = sendMarkRequest($(this).data('id'));

                request.done(() => {
                    $(this).parents('div.alert').remove();
                });
            });

            $('#mark-all').click(function(){
                let request = sendMarkRequest();

                request.done(() => {
                    $('div.alert').remove();
                });
            });
        });
    </script>
    
@endpush
