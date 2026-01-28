<?php

namespace App\Modules\Beneficiaries\Services;

use App\Models\District;
use App\Models\EducationLevel;
use App\Models\EmploymentStatus;
use App\Models\MarriageForm;
use App\Models\MaritalStatus;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Religion;
use App\Models\SurveyChoice;
use App\Models\Tribe;
use App\Models\User;
use Illuminate\Support\Str;

class BeneficiaryCreateService
{
    public function buildFormData(?int $organizationId, bool $isParalegal): array
    {
        $marital_statuses = MaritalStatus::get(['id', 'marital_status']);
        $districts = District::get(['id', 'district', 'region_id']);
        $regions = Region::get(['id', 'region']);
        $education_levels = EducationLevel::get(['id', 'education_level']);
        $tribes = Tribe::get(['id', 'tribe']);
        $religions = Religion::get(['id', 'religion']);
        $marriage_forms = MarriageForm::get(['id', 'marriage_form']);
        $employment_statuses = EmploymentStatus::get(['id', 'employment_status']);
        $survey_choices = SurveyChoice::get(['id', 'survey_choice']);

        $defaultRegionId = null;
        $defaultDistrictId = null;
        if ($isParalegal && $organizationId) {
            $org = Organization::find($organizationId);
            if ($org) {
                $defaultRegionId = $org->region_id;
                $defaultDistrictId = $org->district_id;
            }
        }

        $fileNo = $this->generateFileNo($organizationId, $isParalegal);

        return compact(
            'marital_statuses',
            'regions',
            'districts',
            'education_levels',
            'survey_choices',
            'tribes',
            'religions',
            'marriage_forms',
            'employment_statuses',
            'fileNo',
            'defaultRegionId',
            'defaultDistrictId'
        );
    }

    public function generateFileNo(?int $organizationId, bool $isParalegal): string
    {
        $currentYear = date('Y');
        $prefix = $this->resolvePrefix($organizationId, $isParalegal);

        $lastUserQuery = User::where('user_role_id', 4)
            ->whereYear('created_at', $currentYear)
            ->where('user_no', 'like', $prefix . '/' . $currentYear . '/%');

        if ($isParalegal && $organizationId) {
            $lastUserQuery->where('organization_id', $organizationId);
        }

        $lastUser = $lastUserQuery->orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($lastUser && $lastUser->user_no) {
            $lastNumber = (int) Str::afterLast($lastUser->user_no, '/');
            $nextNumber = $lastNumber + 1;
        }

        do {
            $candidate = $prefix . '/' . $currentYear . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = User::where('user_no', $candidate)->exists();
            $nextNumber++;
        } while ($exists);

        return $candidate;
    }

    private function resolvePrefix(?int $organizationId, bool $isParalegal): string
    {
        $prefix = 'AJISO';
        if (!$isParalegal || !$organizationId) {
            return $prefix;
        }

        $org = Organization::find($organizationId);
        if (!$org) {
            return $prefix;
        }

        $initials = $this->getOrganizationInitials($org->name);
        return $initials !== '' ? $initials : $prefix;
    }

    private function getOrganizationInitials($name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '';
        }

        $parts = preg_split('/[^A-Za-z0-9]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return '';
        }

        $initials = '';
        foreach ($parts as $part) {
            $initials .= Str::upper(Str::substr($part, 0, 1));
        }

        if (Str::length($initials) >= 3) {
            return $initials;
        }

        $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $name);
        if ($cleanName === '' || $cleanName === null) {
            return $initials;
        }

        return Str::upper(Str::substr($cleanName, 0, 3));
    }
}
