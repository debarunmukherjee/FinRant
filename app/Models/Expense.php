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
     * @param string $transactionId
     * @return bool
     */
    public static function createUnsharedExpenseForUser($userId, $planId, $categoryId, $amount, $transactionId = ''): bool
    {
        $expense = new Expense;
        $expense->category_id = $categoryId;
        $expense->user_id = $userId;
        $expense->amount = $amount;
        $expense->is_shared = false;
        $expense->plan_id = $planId;

        if (!empty($transactionId)) {
            $expense->transaction_id = $transactionId;
        }
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

    /**
     * Returns the category name and amount for all the unshared expenses of a user in a plan.<br/>
     * Return array format:
     *
     * `[['categoryName' => categoryName, 'amount' => amount]]`
     * @param $planId
     * @param $userId
     * @return array
     */
    public static function getAllUnsharedUserExpenses($planId, $userId): array
    {
        return self::join('expend_categories', 'expend_categories.id', '=', 'expenses.category_id')
                ->where([
                    ['user_id', $userId],
                    ['plan_id', $planId],
                    ['is_shared', 0]
                ])
                ->orderByDesc('expenses.created_at')
                ->get(['expend_categories.name as categoryName', 'amount'])->toArray();
    }

    /**
     * Returns the category name, amount and share details for all the shared expenses of a user in a plan.<br/>
     * <br/>Return array format:
     * ```
     * [
     *      [
     *          'categoryName' => categoryName,
     *          'amount' => amount,
     *          'shareDetails` => [['userIdWhoPaid' => userId, 'amount' => amount]]
     *      ]
     * ]
     * ```
     * @param $planId
     * @param $userId
     * @return array
     */
    public static function getAllSharedUserExpenses($planId, $userId): array
    {
        $expenseData = self::join('expend_categories', 'expend_categories.id', '=', 'expenses.category_id')
            ->where([
                ['user_id', $userId],
                ['plan_id', $planId],
                ['is_shared', 1]
            ])
            ->orderByDesc('expenses.created_at')
            ->get(['expend_categories.name as categoryName', 'amount', 'shared_expense_batch_id'])->toArray();

        $result = [];

        foreach ($expenseData as $expenseDatum) {
            $result[] = [
                'categoryName' => $expenseDatum['categoryName'],
                'amount' => $expenseDatum['amount'],
                'shareDetails' => SharedExpenseDetail::getSharedExpenseDetails($expenseDatum['shared_expense_batch_id']),
            ];
        }

        return $result;
    }

    /**
     * Returns details for all the shared and unshared expenses of a user in a plan in the below format.<br/>
     * ```
     * [
     *      [
     *          'categoryName' => categoryName,
     *          'amount' => amount,
     *          'isShared' => true|false,
     *          'createdAt' => createdAtTimestamp
     *          'shareDetails` => [['userIdWhoPaid' => userId, 'amount' => amount]]
     *      ]
     * ]
     * ```
     * @param $planId
     * @param $userId
     * @return array
     */
    public static function getAllUserExpenses($planId, $userId): array
    {
        $expenseData = self::join('expend_categories', 'expend_categories.id', '=', 'expenses.category_id')
            ->where([
                ['user_id', $userId],
                ['plan_id', $planId]
            ])
            ->orderByDesc('expenses.created_at')
            ->get(['expend_categories.name as categoryName', 'amount', 'shared_expense_batch_id', 'is_shared', 'shared_expense_batch_id', 'expenses.created_at'])->toArray();

        $result = [];

        foreach ($expenseData as $expenseDatum) {
            $isShared = $expenseDatum['is_shared'];
            $shareDetails = [];
            if ($isShared) {
                $shareDetails = SharedExpenseDetail::getSharedExpenseDetails($expenseDatum['shared_expense_batch_id']);
            }

            $result[] = [
                'categoryName' => $expenseDatum['categoryName'],
                'createdAt' => $expenseDatum['created_at'],
                'amount' => $expenseDatum['amount'],
                'isShared' => (int)$isShared === 1,
                'shareDetails' => $shareDetails
            ];
        }

        return $result;
    }

    public static function getTotalExpenseOfUserInPlan($planId, $userId)
    {
        return self::where([
            ['user_id', $userId],
            ['plan_id', $planId],
        ])->sum('amount');
    }
}
