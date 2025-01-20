<?php

namespace App\Ninja\Repositories;

use App\Models\Company;

class NinjaRepository
{
    public function updatePlanDetails($clientPublicId, $data): void
    {
        $company = company::whereId($clientPublicId)->first();

        if (!$company) {
            return;
        }

        $companyPlan = $company->companyPlan;
        $companyPlan->fill($data);
        $companyPlan->plan_expires = $companyPlan->plan_expires ?: null;
        $companyPlan->save();
    }
}
