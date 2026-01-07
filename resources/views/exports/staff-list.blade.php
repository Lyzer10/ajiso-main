<h2 style="text-align: center;">AJISO Legal Aid System</h2>
<h3 style="text-align: center;">Legal Aid Providers List</h3>

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
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Office</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($staff as $index => $member)
            @php
                $fullName = trim(implode(' ', array_filter([
                    $member->user->first_name ?? '',
                    $member->user->middle_name ?? '',
                    $member->user->last_name ?? '',
                ])));
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $member->user->name ?? '' }}</td>
                <td>{{ $fullName }}</td>
                <td>{{ $member->user->email ?? '' }}</td>
                <td>{{ optional($member->center)->location ?? '' }}</td>
                <td>{{ ($member->is_assigned == 1) ? 'assigned' : 'unassigned' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No legal aid providers found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
