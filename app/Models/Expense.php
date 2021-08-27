<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $expense->save() && self::logUnsharedExpenseActivityMessageForPlan($planId, $userId, $amount, $categoryId, $transactionId);
    }

    /**
     * Create an expense for each member of a plan based on `$planExpenseDistribution`.
     * @param $planId
     * @param $categoryId
     * @param $amount
     * @param $sharedExpenseBatchId
     * @return bool
     */
    public static function createSharedExpenseForPlanMembers($planId, $categoryId, $planExpenseDistribution, $sharedExpenseBatchId): bool
    {
        $result = true;
        foreach ($planExpenseDistribution as $planMemberUserId => $amount) {
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
            $isEqualDistribution = false;
            $shareDetails = [];
            $distributionDetails = [];
            if ($isShared) {
                $shareDetails = SharedExpenseDetail::getSharedExpenseDetails($expenseDatum['shared_expense_batch_id']);
                $isEqualDistribution = (int)($shareDetails[0]['is_equal_distribution']) === 1;
                if (!$isEqualDistribution) {
                    $distributionDetails = ExpenseDistribution::getExpenseDistributionDetails($expenseDatum['shared_expense_batch_id']);
                }
            }

            $result[] = [
                'categoryName' => $expenseDatum['categoryName'],
                'createdAt' => $expenseDatum['created_at'],
                'amount' => $expenseDatum['amount'],
                'isShared' => (int)$isShared === 1,
                'shareDetails' => $shareDetails,
                'isEqualDistribution' => $isEqualDistribution,
                'distributionDetails' => $distributionDetails
            ];
        }

        return $result;
    }

    /**
     * Returns the total expense of a user in a plan.
     * @param $planId
     * @param $userId
     * @return float
     */
    public static function getTotalExpenseOfUserInPlan($planId, $userId): float
    {
        return self::where([
            ['user_id', $userId],
            ['plan_id', $planId],
        ])->sum('amount');
    }

    /**
     * Returns an array of the month wise total expenses of the user, for the last 5 months.
     * @param $userId
     * @return array
     */
    public static function getUserExpenseDataForLastFiveMonths($userId): array
    {
        $current = Carbon::now();
        $result = [];
        $count = 1;
        while ($count <= 5) {
            $month = $current->month;
            $year = $current->year;
            $fromDate = $current->startOfMonth()->toDateString();
            $tillDate = $current->endOfMonth()->toDateString();
            $current->startOfMonth();
            $totalAmount = self::where([
                ['user_id', $userId],
            ])->whereBetween(DB::raw('date(created_at)'), [$fromDate, $tillDate])->sum('amount');
            $result[] = ['year' => $year, 'month' => $month, 'amount' => $totalAmount];
            $current->subMonth();
            $count++;
        }

        return $result;
    }

    /**
     * Returns the sum total of all the expenses made by a user.
     * @param $userId
     * @return float
     */
    public static function getTotalExpenseOfUserAcrossAllPlans($userId): float
    {
        return self::where('user_id', $userId)->sum('amount');
    }

    /**
     * Log activity message for an unshared expense.
     * @param $planId
     * @param $userId
     * @param $amount
     * @param $categoryId
     * @param string $transactionId
     * @return bool
     */
    public static function logUnsharedExpenseActivityMessageForPlan($planId, $userId, $amount, $categoryId, $transactionId = ''): bool
    {
        $srcUserDetails = User::getUserDetails($userId);
        $categoryName = ExpendCategory::getCategoryNameFromId($categoryId);
        if (!empty($transactionId)) {
            $transactionDetails = Transaction::getTransactionDetails($transactionId);
            $destUserDetails = User::getUserDetails($transactionDetails['to_user_id']);
            $messages = [
                PlanActivity::getFormattedUserFullnameForActivityMessage($srcUserDetails['full_name']) . " transfered an amount of  <b>$amount " .
                UserInformation::getUserCurrencyCode($userId) . "</b> to " .
                PlanActivity::getFormattedUserFullnameForActivityMessage($destUserDetails['full_name']) .
                " as unshared expense under the category $categoryName."
            ];
        } else {
            $messages = [
                PlanActivity::getFormattedUserFullnameForActivityMessage($srcUserDetails['full_name']) . " recorded an amount of  <b>$amount " .
                UserInformation::getUserCurrencyCode($userId) . "</b> as unshared expense under the category $categoryName."
            ];
        }
        return PlanActivity::createPlanActivity($planId, $messages);
    }

    /**
     * Log activity message for a shared expense.
     * @param $expenseCreatorId
     * @param $planId
     * @param $categoryId
     * @param $amount
     * @return bool
     */
    public static function logSharedExpenseActivityMessageForPlan($expenseCreatorId, $planId, $categoryId, $amount): bool
    {
        $expenseCreatorDetails = User::getUserDetails($expenseCreatorId);
        $categoryName = ExpendCategory::getCategoryNameFromId($categoryId);
        $messages = [
            PlanActivity::getFormattedUserFullnameForActivityMessage($expenseCreatorDetails['full_name']) . " recorded an amount of  <b>$amount " .
            UserInformation::getUserCurrencyCode($expenseCreatorId) . "</b> as shared expense, under the category $categoryName."
        ];
        return PlanActivity::createPlanActivity($planId, $messages);
    }
}
