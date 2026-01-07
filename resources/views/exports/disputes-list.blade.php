<h2 style="text-align: center;">AJISO Legal Aid System</h2>
<h3 style="text-align: center;">Disputes List</h3>

<p>
    Generated at: {{ $generatedAt ?? '' }}
    @if (!empty($filters))
        | {{ $filters }}
    @endif
</p>

<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <thead style="background-color: #0c466d; color: #fff;">
        <tr>
            <th>S/N</th>
            <th>Dispute No</th>
            <th>Case Type</th>
            <th>Beneficiary</th>
            <th>Legal Aid Provider</th>
            <th>Reported</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($disputes as $index => $dispute)
            @php
                $beneficiary = trim(implode(' ', array_filter([
                    optional($dispute->reportedBy)->first_name,
                    optional($dispute->reportedBy)->middle_name,
                    optional($dispute->reportedBy)->last_name,
                ])));
                $staff = $dispute->staff_id
                    ? trim(implode(' ', array_filter([
                        optional($dispute->assignedTo)->first_name,
                        optional($dispute->assignedTo)->middle_name,
                        optional($dispute->assignedTo)->last_name,
                    ])))
                    : 'Unassigned';
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $dispute->dispute_no }}</td>
                <td>{{ optional($dispute->typeOfCase)->type_of_case ?? '' }}</td>
                <td>{{ $beneficiary }}</td>
                <td>{{ $staff }}</td>
                <td>{{ $dispute->reported_on ? Carbon\Carbon::parse($dispute->reported_on)->format('d-m-Y') : '' }}</td>
                <td>{{ optional($dispute->disputeStatus)->dispute_status ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No disputes found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
