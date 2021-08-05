<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use UserAmountMaxHeap;

class PlanDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'amount'
    ];

    /**
     * Returns the current outstanding amount of a user in a plan.
     * @param $planId
     * @param $userId
     * @return float|int
     */
    public static function getCurrentDebtForUser($planId, $userId)
    {
        $record = self::where([
            ['plan_id', $planId],
            ['user_id', $userId]
        ])->first();

        return empty($record) ? 0 : (float)$record->amount;
    }

    /**
     * Update the debt amount for a user in a plan.
     * @param $planId
     * @param $userId
     * @param $amount
     * @return bool
     */
    public static function saveDebtAmountForUser($planId, $userId, $amount): bool
    {
        $record = self::where([
            ['plan_id', $planId],
            ['user_id', $userId]
        ])->first();

        if (empty($record)) {
            return false;
        }

        $record->amount = $amount;
        return $record->save();
    }

    /**
     * Create an entry in the plan debt table for new users. It should be called when a user creates a plan or joins a plan as member via invite.
     * @param $planId
     * @param $userId
     * @return bool
     */
    public static function createEntryPlanDebtForNewUser($planId, $userId): bool
    {
        $record = new PlanDebt;
        $record->plan_id = $planId;
        $record->user_id = $userId;
        $record->amount = 0;

        return $record->save();
    }

    /**
     * Returns an associative array containing data of users who are in debt and need to give money to other members of the plan to settle it. Format of every array element is ['userId' => userId, 'amount' => amount]
     * @param $planId
     * @return array
     */
    public static function getPlanMembersDataWhoOwe($planId): array
    {
        $result = self::where([
            ['plan_id', $planId],
            ['amount', '>', 0],
        ])->get(['user_id', 'amount'])->toArray();

        $resultArray = [];
        foreach ($result as $item) {
            $resultArray[] = [
                'userId' => (int)$item['user_id'],
                'amount' => abs((float) $item['amount'])
            ];
        }

        return $resultArray;
    }

    /**
     * Returns an associative array containing data of users who are owed money and will receive money from other members of the plan to settle it. Format of every array element is ['userId' => userId, 'amount' => amount]
     * @param $planId
     * @return array
     */
    public static function getPlanMembersDataWhoAreOwed($planId): array
    {
        $result = self::where([
            ['plan_id', $planId],
            ['amount', '<', 0],
        ])->get(['user_id', 'amount'])->toArray();

        $resultArray = [];
        foreach ($result as $item) {
            $resultArray[] = [
                'userId' => (int)$item['user_id'],
                'amount' => abs((float) $item['amount'])
            ];
        }

        return $resultArray;
    }

    /**
     * Returns an optimised set of transactions for a subset of members of a plan, to settle all financial lending.
     * It uses 2 max heaps - one for the users who owe and another for the users who are owed. Then it iteratively
     * generates a set of transactions until both the heaps are empty. Worst case time complexity is `O(n*log(n))`, where
     * `n` is the number of members in the plan.
     *
     * Return array format : [
     *      [userId => ['type' => 'pay|receive', 'otherUserId' => otherUserId, 'amount' => amount]]
     * ]
     * @param $planId
     * @return array
     */
    public static function getOptimisedTransactionsDataToSettlePlanDebt($planId): array
    {
        $debtUserMaxHeap = new UserAmountMaxHeap;
        $debtUserData = self::getPlanMembersDataWhoOwe($planId);
        foreach ($debtUserData as $debtUserDatum) {
            $debtUserMaxHeap->insert($debtUserDatum);
        }

        $owedUserMaxHeap = new UserAmountMaxHeap;
        $owedUserData = self::getPlanMembersDataWhoAreOwed($planId);
        foreach ($owedUserData as $owedUserDatum) {
            $owedUserMaxHeap->insert($owedUserDatum);
        }

        $optimisedTransactions = [];

        while (!$debtUserMaxHeap->isEmpty() && !$owedUserMaxHeap->isEmpty()) {
            $currMaxDebt = $debtUserMaxHeap->extract();
            $currMaxOwed = $owedUserMaxHeap->extract();
            if ($currMaxOwed['amount'] > $currMaxDebt['amount']) {
                $newCurrentDebt = 0;
                $newCurrentOwe = round($currMaxOwed['amount'] - $currMaxDebt['amount'], 2);
                $moneyToBePaid = $currMaxDebt['amount'];
            } else {
                $newCurrentOwe = 0;
                $newCurrentDebt = round($currMaxDebt['amount'] - $currMaxOwed['amount'], 2);
                $moneyToBePaid = $currMaxOwed['amount'];
            }
            $areBothHeapsEmpty = $debtUserMaxHeap->isEmpty() && $owedUserMaxHeap->isEmpty();
            if ($newCurrentOwe > 0) {
                if (!$areBothHeapsEmpty) {
                    $owedUserMaxHeap->insert(['userId' => $currMaxOwed['userId'], 'amount' => $newCurrentOwe]);
                } else {
                    // Handling edge case where a fraction amount of money is unsettled due to improper division
                    $moneyToBePaid += $newCurrentOwe;
                }
            }
            if ($newCurrentDebt > 0) {
                if (!$areBothHeapsEmpty) {
                    $debtUserMaxHeap->insert(['userId' => $currMaxDebt['userId'], 'amount' => $newCurrentDebt]);
                } else {
                    // Handling edge case where a fraction amount of money is unsettled due to improper division
                    $moneyToBePaid += $newCurrentDebt;
                }
            }

            if ($moneyToBePaid > 0) {
                $optimisedTransactions[$currMaxDebt['userId']][] = [
                    'type' => 'pay',
                    'otherUserId' => $currMaxOwed['userId'],
                    'amount' => $moneyToBePaid
                ];
                $optimisedTransactions[$currMaxOwed['userId']][] = [
                    'type' => 'receive',
                    'otherUserId' => $currMaxDebt['userId'],
                    'amount' => $moneyToBePaid
                ];
            }
        }

        return $optimisedTransactions;
    }
}
