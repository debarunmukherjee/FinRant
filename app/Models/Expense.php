<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'category_id',
        'user_id',
        'is_shared',
        'amount',
        'transaction_id',
        'shared_expense_batch_id'
    ];

    /**
     * Create a new expense item for a user, which is unshared by other members.
     * @param $userId
     * @param $planId
     * @param $categoryId
     * @param $amount
     * @return bool
     */
    public static function createUnsharedExpenseForUser($userId, $planId, $categoryId, $amount): bool
    {
        $expense = new Expense;
        $expense->category_id = $categoryId;
        $expense->user_id = $userId;
        $expense->amount = $amount;
        $expense->is_shared = false;
        $expense->plan_id = $planId;

        return $expense->save();
    }

    /**
     * Create an equal expense of amount `$amount` for each member of a plan. Use this to save an expense that is shared by every member of a plan.
     * @param $planId
     * @param $categoryId
     * @param $amount
     * @param $sharedExpenseBatchId
     * @return bool
     */
    public static function createSharedExpenseForAllPlanMembers($planId, $categoryId, $amount, $sharedExpenseBatchId): bool
    {
        $planMemberUserIds = PlanMember::getAllPlanMemberUserIds($planId);
        $result = true;
        foreach ($planMemberUserIds as $planMemberUserId) {
            $expense = new Expense;
            $expense->category_id = $categoryId;
            $expense->user_id = $planMemberUserId;
            $expense->amount = $amount;
            $expense->is_shared = true;
            $expense->shared_expense_batch_id = $sharedExpenseBatchId;
            $expense->plan_id = $planId;

            $result = $result && $expense->save();
        }

        return $result;
    }
}
