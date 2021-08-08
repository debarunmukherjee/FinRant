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

    /**
     * Returns the budget list of a plan for the currently authorised users in the form of an eloquent object
     * @param $planId
     * @return mixed
     */
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

    /**
     * Logs an activity message when a user sets a budget
     * @param $categoryName
     * @param $amount
     * @param $planId
     * @param $userId
     * @return bool
     */
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
