<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedExpenseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'user_id',
        'amount',
        'is_equal_distribution'
    ];

    /**
     * Save a list of user amount data against a given batch id. The `$userAmountData` param is an associative array of the format [userId => amount]
     * @param string $batchId
     * @param array $userAmountData
     * @return bool
     */
    public static function saveSharedExpenseDetail(string $batchId, array $userAmountData, bool $isEqualDistribution): bool
    {
        $result = true;
        foreach ($userAmountData as $userId => $amount) {
            $shareDetail = new SharedExpenseDetail;
            $shareDetail->batch_id = $batchId;
            $shareDetail->user_id = $userId;
            $shareDetail->amount = $amount;
            $shareDetail->is_equal_distribution = $isEqualDistribution ? 1 : 0;
            $result = $result && $shareDetail->save();
        }
        return $result;
    }

    /**
     * Returns the shared expense details as an array.
     * Return array format:
     *
     * `[['userIdWhoPaid' => userId, 'amount' => amount]]`
     * @param $batchId
     * @return array
     */
    public static function getSharedExpenseDetails($batchId): array
    {
        return self::where('batch_id', $batchId)->get(['user_id as userIdWhoPaid', 'amount', 'is_equal_distribution'])->toArray();
    }
}
