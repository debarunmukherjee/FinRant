<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMonthlyBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'user_id',
        'amount'
    ];

    /**
     * Returns the monthly budget set by the user.
     * @param $userId
     * @return float
     */
    public static function getUserBudgetForCurrentMonth($userId): float
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $record = self::where([
            ['year', $currentYear],
            ['month', $currentMonth],
            ['user_id', $userId]
        ])->first();

        return empty($record) ? 0 : (float)$record->amount;
    }

    /**
     * Saves the budget set for the current month by the user. Inserts a new record if one does not exist.
     * @param $userId
     * @param $amount
     * @return bool
     */
    public static function saveUserBudgetForCurrentMonth($userId, $amount): bool
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $record = self::updateOrCreate(
            ['year' => $currentYear, 'month' => $currentMonth, 'user_id' => $userId],
            ['amount' => $amount]
        );

        return !empty($record);
    }

    /**
     * Returns an array of the last 5 monthly budget amounts set by the user.
     * @param $userId
     * @return array
     */
    public static function getUserBudgetDataForLastFiveMonths($userId): array
    {
        $current = Carbon::now();
        $result = [];
        $count = 1;
        while ($count <= 5) {
            $month = $current->month;
            $year = $current->year;
            $record = self::where([
                ['month', $month],
                ['year', $year],
                ['user_id', $userId]
            ])->first();
            if (empty($record)) {
                $result[] = ['year' => $year, 'month' => $month, 'amount' => 0];
            } else {
                $result[] = ['year' => $year, 'month' => $month, 'amount' => $record->amount];
            }
            $current->subMonth();
            $count++;
        }

        return $result;
    }

    /**
     * Returns the sum total of all the monthly budget amounts set by the user.
     * @param $userId
     * @return float
     */
    public static function getTotalBudgetSetByUserAcrossAllMonths($userId): float
    {
        return self::where('user_id', $userId)->sum('amount');
    }
}
