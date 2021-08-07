<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanDebtTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'transaction_id'
    ];

    /**
     * Create a new plan debt transaction record. This keeps a record of all the transactions made in a plan to settle pending dues.
     * @param $planId
     * @param $transactionId
     * @return bool
     */
    public static function savePlanDebtTransaction($planId, $transactionId): bool
    {
        $record = new PlanDebtTransaction;
        $record->plan_id = $planId;
        $record->transaction_id = $transactionId;
        return $record->save();
    }
}
