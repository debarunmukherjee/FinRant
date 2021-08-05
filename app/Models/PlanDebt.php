<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
