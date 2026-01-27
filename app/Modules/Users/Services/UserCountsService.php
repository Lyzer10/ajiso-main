<?php

namespace App\Modules\Users\Services;

use App\Models\User;
use App\Models\UserRole;

class UserCountsService
{
    public function getRoleCounts(array $roleAbbreviations): array
    {
        $roleIds = UserRole::whereIn('role_abbreviation', $roleAbbreviations)
            ->pluck('id', 'role_abbreviation');

        $counts = [];
        foreach ($roleAbbreviations as $abbr) {
            $roleId = $roleIds->get($abbr);
            $counts[$abbr] = $roleId ? User::where('user_role_id', $roleId)->count() : 0;
        }

        return $counts;
    }
}
