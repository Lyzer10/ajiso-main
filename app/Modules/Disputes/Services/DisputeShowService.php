<?php

namespace App\Modules\Disputes\Services;

use App\Models\Dispute;
use App\Models\DisputeStatus;
use App\Models\Staff;

class DisputeShowService
{
    public function buildContext(Dispute $dispute, $user): array
    {
        $occurrences = Dispute::with('assignedTo', 'paralegalUser', 'disputeStatus')
            ->where('dispute_no', $dispute->dispute_no)
            ->get(['id', 'reported_on', 'staff_id', 'paralegal_user_id', 'dispute_status_id']);

        $dispute_statuses = DisputeStatus::get(['id', 'dispute_status']);
        $continueStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['continue'])
            ->value('id');
        $referredStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['referred'])
            ->value('id');

        $isStaffUser = $user && $user->can('isStaff');
        $isParalegalUser = $user && $user->can('isClerk');
        $isAdminUser = $user && ($user->can('isAdmin') || $user->can('isSuperAdmin'));

        $canRequestReassignment = $isAdminUser;
        $requiresTargetStaff = $isStaffUser || $isAdminUser;

        $availableStaff = collect();
        if ($requiresTargetStaff) {
            $availableStaff = Staff::has('user')
                ->with('user.designation:id,name', 'center:id,name')
                ->where('type', 'staff')
                ->whereHas('user', function ($query) {
                    $query->where('is_active', 1);
                })
                ->get(['id', 'user_id', 'center_id']);
        }

        return [
            'occurrences' => $occurrences,
            'dispute_statuses' => $dispute_statuses,
            'continueStatusId' => $continueStatusId,
            'referredStatusId' => $referredStatusId,
            'availableStaff' => $availableStaff,
            'canRequestReassignment' => $canRequestReassignment,
            'requiresTargetStaff' => $requiresTargetStaff,
            'isParalegalUser' => $isParalegalUser,
            'isAdminUser' => $isAdminUser,
        ];
    }
}
