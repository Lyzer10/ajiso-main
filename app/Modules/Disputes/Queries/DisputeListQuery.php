<?php

namespace App\Modules\Disputes\Queries;

use App\Models\Dispute;
use Illuminate\Database\Eloquent\Builder;

class DisputeListQuery
{
    public function build(?int $organizationId, array $filters, ?string $periodStart, ?string $periodEnd): Builder
    {
        $query = Dispute::has('reportedBy')
            ->with(
                'assignedTo:first_name,middle_name,last_name,user_no',
                'paralegalUser:id,first_name,middle_name,last_name,user_no',
                'reportedBy:first_name,middle_name,last_name,user_no',
                'disputeStatus:id,dispute_status'
            )
            ->select(
                [
                    'id',
                    'dispute_no',
                    'beneficiary_id',
                    'staff_id',
                    'paralegal_user_id',
                    'reported_on',
                    'dispute_status_id',
                    'type_of_case_id'
                ]
            )
            ->latest();

        if ($organizationId) {
            $query->whereHas('reportedBy', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('dispute_status_id', (int) $filters['status']);
        }

        if (!empty($filters['case_type'])) {
            $query->where('type_of_case_id', (int) $filters['case_type']);
        }

        if ($periodStart && $periodEnd) {
            $query->whereBetween('reported_on', [$periodStart, $periodEnd]);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('reportedBy', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
