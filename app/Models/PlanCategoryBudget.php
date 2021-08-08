<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PlanCategoryBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'category_id',
        'user_id',
        'plan_id',
    ];

    public static function getBudgetListForCurrentPlan($planId)
    {
        return self::select('name', 'amount')
            ->join('expend_categories', 'expend_categories.id', '=', 'plan_category_budgets.category_id')
            ->where([
                ['plan_category_budgets.plan_id', $planId],
                ['plan_category_budgets.user_id', Auth::id()]
            ])
            ->get();
    }

    public static function logBudgetCreationActivityMessages($categoryName, $amount, $planId, $userId): bool
    {
        $userDetails = User::getUserDetails($userId);
        $messages = [
            'Way to go!',
            PlanActivity::getFormattedUserFullnameForActivityMessage($userDetails['full_name']) . " just set a budget of <b>$amount " .
            UserInformation::getUserCurrencyCode($userId) . "</b> for <b>$categoryName</b>."
        ];

        return PlanActivity::createPlanActivity($planId, $messages);
    }
}
