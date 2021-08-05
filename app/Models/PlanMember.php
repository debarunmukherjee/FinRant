<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlanMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'user_id'
    ];

    /**
     * Returns the details of all the plans which the user with id `$userId` is a member of.
     * @param $userId
     * @return mixed
     */
    public static function getMemberPlansDetails($userId)
    {
        return Plan::join('plan_members', 'plan_members.plan_id', '=', 'plans.id')
            ->where('plan_members.user_id', $userId)
            ->get(['plans.*', DB::raw("'member' as role")])
            ->toArray();
    }

    public static function isUserPlanMember($planId, $userId)
    {
        return self::where([
            ['plan_id', $planId],
            ['user_id', $userId]
        ])->exists();
    }

    /**
     * Returns an associative array (with userId as key) containing details (full name, email, userId and avatar) of members of a plan with id `$planId`.
     *
     * It does not include the details of the plan creator.
     * @param $planId
     * @return array
     */
    public static function getPlanMembersDetailsList($planId): array
    {
        $records = self::join('users', 'users.id', '=', 'plan_members.user_id')
                    ->join('user_information', 'user_information.user_id', '=', 'plan_members.user_id')
                    ->where('plan_id', $planId)
                    ->get([DB::raw("concat(users.first_name, ' ', users.last_name) as full_name"), 'users.id as userId', 'users.email', 'user_information.profile_picture as avatar', 'user_information.country', DB::raw("'member' as role")])
                    ->toArray();
        $result = [];
        foreach ($records as $record) {
            $result[(int)$record['userId']] = $record;
        }

        return $result;
    }

    /**
     * Returns the member count of a plan including it's creator.
     * @param $planId
     * @return int
     */
    public static function getMemberCount($planId): int
    {
        return (int)(self::where('plan_id', $planId)->count() + 1);
    }

    /**
     * Returns a list of all the users id's in a given plan.
     * @param $planId
     * @param bool $includeCreator
     * @return array
     */
    public static function getAllPlanMemberUserIds($planId, bool $includeCreator = true): array
    {
        $members = self::where('plan_id', $planId)->get(['user_id'])->toArray();
        $membersArray = [];
        foreach ($members as $member) {
            $membersArray[] = $member['user_id'];
        }
        if ($includeCreator) {
            $membersArray[] = Plan::getPlanCreatorUserId($planId);
        }
        return $membersArray;
    }
}
