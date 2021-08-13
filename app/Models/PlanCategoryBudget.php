<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Returns the budget and expense data for the budget category items set in a plan.<Br/>Return array format:<br/>
     * ```
     * [
     *      ['budget' => budgetAmount, 'expense' => expenseAmount, 'name' => categoryName]
     * ]
     * ```
     * @param $userId
     * @param $planId
     * @return array
     */
    public static function getBudgetVsExpenseDataForCategories($userId, $planId): array
    {
        return self::leftJoin('expenses', function ($join) {
                        $join->on('expenses.category_id', '=', 'plan_category_budgets.category_id')
                             ->on('expenses.plan_id', '=', 'plan_category_budgets.plan_id');
                    })->join('expend_categories', 'expend_categories.id', '=', 'plan_category_budgets.category_id')
                      ->where([
                          ['plan_category_budgets.user_id', $userId],
                          ['plan_category_budgets.plan_id', $planId],
                          ['expenses.is_shared', 0]
                      ])
                      ->groupBy('plan_category_budgets.category_id', 'plan_category_budgets.amount', 'plan_category_budgets.plan_id')
                      ->get(['plan_category_budgets.amount as budget', DB::raw("ifnull(sum(expenses.amount), 0) as expense"), 'expend_categories.name'])
                      ->toArray();
    }
}
