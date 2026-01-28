<?php

namespace App\Modules\Users\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ParalegalListQuery
{
    public function build(
        ?array $paralegalRoleIds,
        ?string $search,
        ?int $organizationId,
        ?string $organizationName = null
    ): Builder
    {
        $organizationName = $organizationName !== null ? trim($organizationName) : null;
        $organizationName = $organizationName !== '' ? $organizationName : null;
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
            ->when($paralegalRoleIds, function ($query, $roleIds) {
                return $query->whereIn('user_role_id', $roleIds);
            })
            ->when($organizationId, function ($query, $organizationId) {
                return $query->where('organization_id', $organizationId);
            })
            ->when($organizationName, function ($query, $organizationName) {
                $like = '%' . $organizationName . '%';
                $query->whereHas('organization', function ($orgQuery) use ($like) {
                    $orgQuery->where('name', 'like', $like);
                });
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
