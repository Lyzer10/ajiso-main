<?php

namespace App\Modules\Users\Services;

use App\Models\User;
use App\Models\UserRole;

class UserCountsService
{
    public function getRoleCounts(array $roleAbbreviations, ?int $organizationId): array
    {
        $roleIds = UserRole::whereIn('role_abbreviation', $roleAbbreviations)
            ->pluck('id', 'role_abbreviation');

        $counts = [];
        foreach ($roleAbbreviations as $abbr) {
            $roleId = $roleIds->get($abbr);
            if (!$roleId) {
                $counts[$abbr] = 0;
                continue;
            }
            $query = User::where('user_role_id', $roleId);
            if ($organizationId) {
                $query->where('organization_id', $organizationId);
            }
            $counts[$abbr] = $query->count();
        }

        return $counts;
    }
}
