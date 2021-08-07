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
}
