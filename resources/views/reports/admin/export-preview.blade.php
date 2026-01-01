<h1 style="text-align: center;"><span style="color: blue;"><b>AL</b></span>AS</h1>
<h2 style="text-align: center;">{{ __('AJISO Legal Aid System') }}</h2>
<h3 style="text-align: center;">{{ __('General Report')}}</h3>
    @if ($disputes->count())
        <div>
            <h4 >
                {{ __('Results Summary')}}
            </h4>
            <table order="1" width="100%" cellpadding="10" style="border:1px solid blue;">
                <thead >
                    <tr>
                        <th width="25%">
                            <b>{{ __('Dates') }}</b></span>
                        </th>
                        <th width="15%">
                            <b>{{ __('Filter') }}</b></span>
                        </th>
                        <th width="25%">
                            <b>{{ __('Query') }}</b></span>
                        </th>
                        <th width="15%">
                            <span><b>{{ __('Disputes Found') }}</b></span>
                        </th>
                        <th width="15%">
                            <span ><b>{{ __('Resolved Disputes') }}</b></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="text-align: center;">
                        <td>
                            {{ $date_raw ?? 'N/A' }}
                        </td>
                        <td>
                            {{ __($filter_by) ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $filter_val ?? 'N/A' }}
                        </td>
                        <td>
                            <span  style="color: blue;">{{ $disputes->count() ?? '0'}}</span>
                        </td>
                        <td>
                            <span  style="color: blue;">{{ $resolved_count ?? '0'}}</span>
                        </td>
                    </tr>
                </tbody>
                    <br/>
            </table>
        </div>
        <div>
            <table order="1" width="100%" cellpadding="5">
                <thead style="background-color: blue;">
                    <tr>
                        <th width="5%">{{ __('Id') }}</th>
                        <th width="10%">{{ __('Dispute No') }}</th>
                        <th width="6%">{{ __('Service') }}</th>
                        <th width="9%">{{ __('Case') }}</th>
                        <th width="22%">{{ __('Beneficiary') }}</th>
                        <th width="22%">{{ __('Legal Aid Provider') }}</th>
                        <th width="10%">{{ __('Reported') }}</th>
                        <th width="20%">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($disputes ?? '' as $dispute)
                    <tr>
                        <td>{{ '#'.$dispute->id }}</td>
                        <td>
                            <a  style="color: blue;">
                                {{ $dispute->dispute_no }}
                            </a>
                        </td>
                        <td>{{ $dispute->typeOfService->type_of_service }}</td>
                        <td>{{ $dispute->typeOfCase->type_of_case }}</td>
                        <td>
                            <a  style="color: blue;">
                                {{ $dispute->reportedBy->first_name.' '
                                    .$dispute->reportedBy->middle_name.' '.
                                    $dispute->reportedBy->last_name
                                }}
                            </a>
                        </td>
                        <td>
                            @if (is_null($dispute->staff_id))
                                <a style="color: red;">
                                    {{ __('Unassigned') }}
                                </a>
                            @else
                                <a style="color: blue;">
                                    {{ $dispute->assignedTo->first_name.' '
                                        .$dispute->assignedTo->middle_name.' '
                                        .$dispute->assignedTo->last_name
                                    }}
                                </a>
                            @endif
                        </td>
                        <td>{{ Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') }}</td>
                        <td>
                            <span
                            @if ( $dispute->disputeStatus->dispute_status  === 'resolved')
                                style="color: green;"
                            @elseif ( $dispute->disputeStatus->dispute_status  === 'pending')
                                style="color: yellow;"
                            @elseif ( $dispute->disputeStatus->dispute_status  === 'proceeding')
                                style="color: blue;"
                            @elseif ( $dispute->disputeStatus->dispute_status  === 'continue')
                                style="color: skyblue;"
                            @elseif ( $dispute->disputeStatus->dispute_status  === 'referred')
                                style="color: #524847fd;"
                            @else
                                style="color: red;"
                            @endif
                            >
                            {{ $dispute->disputeStatus->dispute_status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br/>
            <small style="text-align: right;">
                {{ __('Generated at') }}: {{ Carbon\Carbon::parse(now())->format('d-m-Y H:m') }}
            </small>
        </div>
    @endif

