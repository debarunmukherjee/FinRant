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
        'amount'
    ];

    /**
     * Creates a new entry in the transactions table to record a user transaction.<br/>
     * Returns the transaction id, if the record was created successfully otherwise false.
     * @param $toUserId
     * @param $fromUserId
     * @param $amount
     * @return false|string
     */
    public static function recordUserTransaction($toUserId, $fromUserId, $amount)
    {
        $transactionUniqueKey = Str::random(64);
        $record = self::create([
            'transaction_unique_key' => $transactionUniqueKey,
            'to_user_id' => $toUserId,
            'from_user_id' => $fromUserId,
            'amount' => $amount
        ]);
        return empty($record) ? false : $record->id;
    }
}
