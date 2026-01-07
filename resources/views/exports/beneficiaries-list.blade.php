<h2 style="text-align: center;">AJISO Legal Aid System</h2>
<h3 style="text-align: center;">Beneficiaries List</h3>

<p>
    Generated at: {{ $generatedAt ?? '' }}
    @if (!empty($search))
        | Search: {{ $search }}
    @endif
</p>

<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <thead style="background-color: #0c466d; color: #fff;">
        <tr>
            <th>S/N</th>
            <th>File No</th>
            <th>Full Name</th>
            <th>Telephone</th>
            <th>District</th>
            <th>Region</th>
            <th>Enrolled On</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($beneficiaries as $index => $beneficiary)
            @php
                $fullName = trim(implode(' ', array_filter([
                    $beneficiary->user->first_name ?? '',
                    $beneficiary->user->middle_name ?? '',
                    $beneficiary->user->last_name ?? '',
                ])));
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $beneficiary->user->user_no ?? '' }}</td>
                <td>{{ $fullName }}</td>
                <td>{{ $beneficiary->user->tel_no ?? '' }}</td>
                <td>{{ optional($beneficiary->district)->district ?? '' }}</td>
                <td>{{ optional(optional($beneficiary->district)->region)->region ?? '' }}</td>
                <td>{{ $beneficiary->created_at ? Carbon\Carbon::parse($beneficiary->created_at)->format('d-m-Y') : '' }}</td>
                <td>{{ ($beneficiary->user && $beneficiary->user->is_active) ? 'Active' : 'Inactive' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No beneficiaries found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
