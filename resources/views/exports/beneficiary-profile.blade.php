<h2 style="text-align: center;">AJISO Legal Aid System</h2>
<h3 style="text-align: center;">Beneficiary Profile</h3>

@php
    $fullName = trim(implode(' ', array_filter([
        $beneficiary->user->first_name ?? '',
        $beneficiary->user->middle_name ?? '',
        $beneficiary->user->last_name ?? '',
    ])));
@endphp

<p>
    Name: {{ $fullName }} | File No: {{ $beneficiary->user->user_no ?? '' }}
</p>
<p>Generated at: {{ $generatedAt ?? '' }}</p>

<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <thead style="background-color: #0c466d; color: #fff;">
        <tr>
            <th width="30%">Field</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row[0] }}</td>
                <td>{{ $row[1] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
