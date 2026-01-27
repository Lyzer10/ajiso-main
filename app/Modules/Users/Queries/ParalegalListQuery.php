<?php

namespace App\Modules\Users\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ParalegalListQuery
{
    public function build(?int $paralegalRoleId, ?string $search, ?int $organizationId): Builder
    {
        $query = User::with(['role:id,role_abbreviation,role_name', 'organization:id,name'])
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
                    'user_role_id',
                    'organization_id'
                ]
            )
            ->when($paralegalRoleId, function ($query, $roleId) {
                return $query->where('user_role_id', $roleId);
            })
            ->when($organizationId, function ($query, $organizationId) {
                return $query->where('organization_id', $organizationId);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $like = '%' . $search . '%';
                    $subQuery->where('name', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('middle_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('user_no', 'like', $like);
                });
            })
            ->latest();

        return $query;
    }
}
