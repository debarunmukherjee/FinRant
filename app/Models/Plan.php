<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    public static function getPlanIdFromName($name): int
    {
        $result = self::select('id')->where('name', $name)->first();
        return empty($result) ? 0 : (int)$result->id;
    }

    public static function getPlanDetails($planId)
    {
        return self::where('id', $planId)->first();
    }

    public static function getPlanCreatorUserId($planId): int
    {
        $result = self::select('created_by')->where('id', $planId)->first();
        return empty($result) ? 0 : (int)$result->created_by;
    }

    public static function isUserPlanCreator($planId, $userId): bool
    {
        return (int)$userId === self::getPlanCreatorUserId($planId);
    }

    /**
     * Returns the details of all the plans created by the user with id `$userId`.
     * @param $userId
     * @return mixed
     */
    public static function getCreatedPlansDetails($userId)
    {
        return self::where('created_by', $userId)->orderByDesc('created_at')->get(['plans.*', DB::raw("'creator' as role")])->toArray();
    }

    /**
     * Returns whether a user has access to record an expense or be involved in a transaction in a plan.<br/>
     * It can also be used wherever we want to check if a user is a creator or member of a plan.
     * @param $userId
     * @param $planId
     * @return bool
     */
    public static function userHasPlanExpenseAccess($userId, $planId): bool
    {
        return (self::isUserPlanCreator($planId, $userId) || PlanMember::isUserPlanMember($planId, $userId));
    }

    /**
     * Returns whether 2 users can exchange funds in a given plan.
     * @param $srcUserId
     * @param $destUserId
     * @param $planId
     * @return bool
     */
    public static function usersCanExchangeFundsInPlan($srcUserId, $destUserId, $planId): bool
    {
        return (
            $srcUserId !== $destUserId &&
            self::userHasPlanExpenseAccess($srcUserId, $planId) &&
            self::userHasPlanExpenseAccess($destUserId, $planId)
        );
    }

    /**
     * Returns an array of messages that needs to be logged as activity when a new plan is created.
     * @param $planId
     * @param $userId
     * @return string[]
     */
    public static function getActivityMessagesForNewPlanCreation($planId, $userId): array
    {
        $userDetails = User::getUserDetails($userId);
        $planDetails = self::getPlanDetails($planId);
        return [
            'Ahoy! ' . PlanActivity::getFormattedUserFullnameForActivityMessage($userDetails['full_name']) . " created the plan <i><b>" . $planDetails->name . "</b></i>",
            'You can now set budgets for different categories for the plan, invite and collaborate with other people, share and track your expenses and much more!',
            'The <b>FinRant</b> bot 🤖 wishes you happy savings!'
        ];
    }
}
