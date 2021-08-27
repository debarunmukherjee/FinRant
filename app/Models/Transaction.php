<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_unique_key',
        'to_user_id',
        'from_user_id',
        'amount',
        'fusion_transaction_id'
    ];

    /**
     * Creates a new entry in the transactions table to record a user transaction.<br/>
     * Returns the transaction id, if the record was created successfully otherwise false.
     * @param $toUserId
     * @param $fromUserId
     * @param $amount
     * @param $transactionUniqueKey
     * @param $fusionTransactionId
     * @return false|string
     */
    public static function recordUserTransaction($toUserId, $fromUserId, $amount, $transactionUniqueKey, $fusionTransactionId)
    {
        $record = self::create([
            'transaction_unique_key' => $transactionUniqueKey,
            'to_user_id' => $toUserId,
            'from_user_id' => $fromUserId,
            'fusion_transaction_id' => $fusionTransactionId,
            'amount' => $amount
        ]);
        return empty($record) ? false : $record->id;
    }

    /**
     * Returns the transactions details from the transaction id.
     * @param $transactionId
     * @return array
     */
    public static function getTransactionDetails($transactionId): array
    {
        $record = self::where('id', $transactionId)->first();
        return empty($record) ? [] : $record->toArray();
    }
}
