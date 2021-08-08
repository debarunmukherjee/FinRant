<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'batch_id',
        'message'
    ];

    /**
     * Returns an array of the activity messages for a plan, grouped in batches.
     * @param $planId
     * @return array
     */
    public static function getActivityMessagesForPlan($planId): array
    {
        DB::statement("SET SQL_MODE=''");
        $result = self::where('plan_id', $planId)->get(['message', 'batch_id'])->groupBy('batch_id')->toArray();
        DB::statement("SET SQL_MODE=only_full_group_by");
        return $result;
    }

    /**
     * Creates a batch of activity messages for a given array of messages and a plan
     * @param $planId
     * @param array $messages
     * @return bool
     */
    public static function createPlanActivity($planId, array $messages): bool
    {
        return DB::transaction(function () use ($planId, $messages) {
            $batchId = Str::random(64);
            $result = true;
            foreach ($messages as $message) {
                $activity = new PlanActivity;
                $activity->plan_id = $planId;
                $activity->batch_id = $batchId;
                $activity->message = $message;
                $result = $result && $activity->save();
            }
            return $result;
        });
    }

    /**
     * An util function to return the highlighted username
     * @param $fullName
     * @return string
     */
    public static function getFormattedUserFullnameForActivityMessage($fullName): string
    {
        return "<span class='bg-green-600 inline-block rounded p-1'><b>$fullName</b></span>";
    }
}
