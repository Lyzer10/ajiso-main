<h2 style="text-align: center;">AJISO Legal Aid System</h2>
<h3 style="text-align: center;">Paralegals List</h3>

<p>
    Generated at: {{ $generatedAt ?? '' }}
    @if (!empty($search))
        | Search: {{ $search }}
    @endif
    @if (!empty($organizationName))
        | Organization: {{ $organizationName }}
    @endif
</p>

<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <thead style="background-color: #0c466d; color: #fff;">
        <tr>
            <th>S/N</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Organization</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $index => $user)
            @php
                $fullName = trim(implode(' ', array_filter([
                    $user->first_name ?? '',
                    $user->middle_name ?? '',
                    $user->last_name ?? '',
                ])));
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name ?? '' }}</td>
                <td>{{ $fullName }}</td>
                <td>{{ $user->email ?? '' }}</td>
                <td>{{ optional($user->organization)->name ?? '' }}</td>
                <td>{{ ((bool) $user->is_active === true) ? 'Active' : 'Inactive' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No paralegals found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
