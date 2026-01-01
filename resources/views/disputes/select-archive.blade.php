@extends('layouts.disputes.archive')

@section('subsection')
    <div class="card-body" style="width: 100%;">
        <div class="table-responsive">
            <table class="table table-striped progress-table text-center">
                <thead class="text-capitalize text-white light-custom-color">
                    <tr>
                        <th>{{ __('Id') }}</th>
                        <th>{{ __('Dispute No') }}</th>
                        <th>{{ __('Case Type') }}</th>
                        <th>{{ __('Beneficiary') }}</th>
                        <th>{{ __('Legal Aid Provider') }}</th>
                        <th>{{ __('Date Reported') }}</th>
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
                            {{ $dispute->dispute_no }}
                        </td>
                        <td>
                            {{ __($dispute->typeOfCase->type_of_case) }}
                        </td>
                        <td>
                            <a href="{{ route('beneficiary.show', [app()->getLocale(), $dispute->beneficiary_id]) }}">
                            {{ $dispute->reportedBy->first_name.' '
                                .$dispute->reportedBy->middle_name.' '
                                .$dispute->reportedBy->last_name
                            }}
                            </a>
                        </td>
                        <td>
                            @if (is_null($dispute->staff_id))
                                @canany(['isSuperAdmin', 'isAdmin'])
                                <a href="{{ route('dispute.assign', [app()->getLocale(), $dispute]) }}" class="text-danger" title="{{  __('Click to assigned legal aid provider') }}">
                                    {{ __('Unassigned') }}
                                </a>
                                @elsecanany(['isClerk', 'isStaff'])
                                <a>
                                    {{ __('Unassigned') }}
                                </a>
                                @endcanany
                            @else
                                <a href="{{ route('staff.show', [app()->getLocale(), $dispute->staff_id]) }}" title="{{  __('Click to view assigned legal aid provider') }}">
                                    {{ $dispute->assignedTo->first_name.' '
                                        .$dispute->assignedTo->middle_name.' '
                                        .$dispute->assignedTo->last_name
                                    }}
                                </a>
                            @endif
                        </td>
                        <td>{{ Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') }}</td>
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
                            {{ __($dispute->disputeStatus->dispute_status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('dispute.create.archive', [app()->getLocale(), $dispute->id]) }}" title="{{  __('Continue') }}">
                                <i class="fas fa-chevron-circle-right fa-fw text-success"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
