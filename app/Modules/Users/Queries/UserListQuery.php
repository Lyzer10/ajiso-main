<?php

namespace App\Modules\Users\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserListQuery
{
    public function build(?int $beneficiaryRoleId, ?int $organizationId): Builder
    {
        $query = User::with('role:id,role_abbreviation,role_name')
            ->select(
                [
                    'id',
                    'name',
                    'user_no',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'email',
                    'is_active',
                    'user_role_id'
                ]
            )
            ->when($beneficiaryRoleId, function ($query, $roleId) {
                return $query->where('user_role_id', '!=', $roleId);
            })
            ->latest();

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        return $query;
    }
}
