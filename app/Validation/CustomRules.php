<?php

namespace App\Validation;

use App\Models\SubscriptionPlan;

class CustomRules
{
    /**
     * Validates that a subscription plan name is unique among active and soft-deleted-aware records.
     *
     * @param string $value
     * @param string|null $params
     * @param array|null $data
     * @return bool
     */
    public function unique_plan_name(string $value, ?string $params = null, ?array $data = null): bool
    {
        $planModel = new SubscriptionPlan();
        $builder = $planModel->builder();
        $builder->where('name', $value)
                ->where('deleted_at', null);

        if (!empty($params)) {
            $builder->where('id !=', $params);
        }

        return $builder->countAllResults() === 0;
    }
}
