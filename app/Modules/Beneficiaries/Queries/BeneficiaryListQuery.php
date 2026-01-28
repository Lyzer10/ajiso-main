<?php

namespace App\Modules\Beneficiaries\Queries;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Builder;

class BeneficiaryListQuery
{
    public function build(?int $organizationId, ?string $search): Builder
    {
        $query = Beneficiary::whereHas('user')
            ->with('user')
            ->withCount('disputes')
            ->latest();

        if ($organizationId) {
            $query->whereHas('user', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
