<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PendingPlanDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'src_user_id',
        'dest_user_id',
        'action',
        'amount'
    ];

    /**
     * Rebuilds the pending transactions for every user in a plan. It deletes the current entries and inserts the updated values obtained from `getOptimisedTransactionsDataToSettlePlanDebt` function.
     *
     * This should be called when an unequally paid shared transaction is to be recorded.
     * @param $planId
     */
    public static function refreshPendingPlanDebts($planId): void
    {
        $pendingTransactionsData = PlanDebt::getOptimisedTransactionsDataToSettlePlanDebt($planId);
        DB::transaction(function () use ($planId, $pendingTransactionsData) {
            PendingPlanDebt::where('plan_id', $planId)->delete();
            foreach ($pendingTransactionsData as $userId => $pendingTransactionsDatum) {
                foreach ($pendingTransactionsDatum as $transactionsData) {
                    $pendingPlanDebt = new PendingPlanDebt;
                    $pendingPlanDebt->plan_id = $planId;
                    $pendingPlanDebt->src_user_id = $userId;
                    $pendingPlanDebt->dest_user_id = $transactionsData['otherUserId'];
                    $pendingPlanDebt->action = $transactionsData['type'];
                    $pendingPlanDebt->amount = $transactionsData['amount'];
                    $pendingPlanDebt->save();
                }
            }
        });
    }

    /**
     * Returns an array containing all the actions that the user needs to make for a plan to settle all debts.
     * Format of the array:
     *
     * `[['otherUserId' => otherUserId, 'amount' => amount, 'action' => 'pay'|'receive' ]`
     * @param $planId
     * @param $userId
     * @return array
     */
    public static function getAllPendingPlanTransactionsForUser($planId, $userId): array
    {
        return self::where([
            ['src_user_id', $userId],
            ['plan_id', $planId]
        ])->get(['dest_user_id as otherUserId', 'amount', 'action'])->toArray();
    }

    /**
     * Returns the amount that a user needs to pay to another user in a plan, to settle dues.
     * @param $planId
     * @param $srcUserId
     * @param $destUserId
     * @return float
     */
    public static function getAmountUserNeedsToPayToAnotherUser($planId, $srcUserId, $destUserId): float
    {
        $result = self::where([
            ['plan_id', $planId],
            ['src_user_id', $srcUserId],
            ['dest_user_id', $destUserId],
            ['action', 'pay'],
        ])->get(['amount'])->first();
        return empty($result) ? 0 : (float)$result->amount;
    }

    /**
     * Settles a debt between 2 users by deleting the entries from the
     * @param $planId
     * @param $srcUserId
     * @param $destUserId
     * @return bool
     */
    public static function settleDebtBetweenUsers($planId, $srcUserId, $destUserId): bool
    {
        return DB::transaction(
            function () use ($planId, $srcUserId, $destUserId) {
                // Currently for every transaction to be made, we create 2 entries are made in the pending_plan_debts table.
                // Other is pay and the other is receive. We need delete both of there when a debt is resolved.
                $dueTransactionAmount = self::getAmountUserNeedsToPayToAnotherUser($planId, $srcUserId, $destUserId);
                $result = (bool)self::where([
                    ['plan_id', $planId],
                    ['src_user_id', $srcUserId],
                    ['dest_user_id', $destUserId],
                ])->delete();
                $result = $result && (bool)self::where([
                    ['plan_id', $planId],
                    ['src_user_id', $destUserId],
                    ['dest_user_id', $srcUserId],
                ])->delete();

                $currentPendingAmountForUserWhoPaid = PlanDebt::getCurrentDebtForUser($planId, $srcUserId);
                $currentPendingAmountForUserWhoReceived = PlanDebt::getCurrentDebtForUser($planId, $destUserId);
                $result = $result && PlanDebt::saveDebtAmountForUser($planId, $srcUserId, $currentPendingAmountForUserWhoPaid - $dueTransactionAmount);
                return $result && PlanDebt::saveDebtAmountForUser($planId, $destUserId, $currentPendingAmountForUserWhoReceived + $dueTransactionAmount);
            }
        );
    }

    /**
     * Returns the total pending amount of a user across all the plans.
     * @param $userId
     * @return float
     */
    public static function getTotalPendingDebtAcrossAllPlans($userId): float
    {
        $records = self::where('src_user_id', $userId)->get(['amount', 'action'])->toArray();
        $totalPending = 0;
        foreach ($records as $record) {
            if ($record['action'] === 'receive') {
                $totalPending -= (float)$record['amount'];
            } else {
                $totalPending += (float)$record['amount'];
            }
        }
        return $totalPending;
    }
}
