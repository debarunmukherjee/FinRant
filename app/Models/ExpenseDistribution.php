<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'user_id',
        'amount'
    ];

    public static function saveExpenseDistributionDetail(string $batchId, array $userAmountData): bool
    {
        $result = true;
        foreach ($userAmountData as $userId => $amount) {
            $expenseDistribution = new ExpenseDistribution;
            $expenseDistribution->batch_id = $batchId;
            $expenseDistribution->user_id = $userId;
            $expenseDistribution->amount = $amount;
            $result = $result && $expenseDistribution->save();
        }
        return $result;
    }

    public static function getExpenseDistributionDetails($batchId)
    {
        return self::where('batch_id', $batchId)->get(['user_id as distributionUserId', 'amount'])->toArray();
    }
}
