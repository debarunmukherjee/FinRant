<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlanUserInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'sent_to',
        'sent_by',
        'has_accepted',
        'is_rejected'
    ];

    public static function getInviteCountOfUser($userId)
    {
        return self::where([
            ['sent_to', $userId],
            ['has_accepted', 0],
            ['is_rejected', 0]
        ])->count();
    }

    public static function acceptUserInvite($inviterUserId, $userId, $planId): bool
    {
        return DB::transaction(function () use ($inviterUserId, $userId, $planId) {
            $invite = self::where([
                ['sent_by', $inviterUserId],
                ['sent_to', $userId],
                ['plan_id', $planId]
            ])->first();

            $invite->has_accepted = 1;
            $result = $invite->save();

            $result = $result && PlanDebt::createEntryPlanDebtForNewUser($planId, $userId);

            $planMember = new PlanMember;
            $planMember->plan_id = $planId;
            $planMember->user_id = $userId;

            return $result && $planMember->save() && self::logUserAcceptInviteActivity($userId, $planId);
        });
    }

    public static function rejectUserInvite($inviterUserId, $userId, $planId): bool
    {
        $invite = self::where([
            ['sent_by', $inviterUserId],
            ['sent_to', $userId],
            ['plan_id', $planId]
        ])->first();

        $invite->is_rejected = 1;
        return $invite->save();
    }

    /**
     * Returns an array of all the pending invites for a user.<br/>Return array format:<br/>
     * ```
     * ['planName' => planName, 'name' => inviterUserFullName, 'email' => inviterUserEmail, 'planId' => invitedPlanId]
     * ```
     * @param $userId
     * @return array
     */
    public static function getAllInvitesForUser($userId): array
    {
        $invitesData = self::select('first_name', 'last_name', 'email', 'plans.name as plan_name', 'plans.id as plan_id')
            ->join('users', 'users.id', '=', 'plan_user_invites.sent_by')
            ->join('plans', 'plans.id', '=', 'plan_user_invites.plan_id')
            ->where([
                ['sent_to', $userId],
                ['has_accepted', 0],
                ['is_rejected', 0],
            ])
            ->get();
        $invitesList = [];
        foreach ($invitesData as $invite) {
            $res_item['planName'] = $invite->plan_name;
            $res_item['name'] = $invite->first_name . ' ' . $invite->last_name;
            $res_item['email'] = $invite->email;
            $res_item['planId'] = $invite->plan_id;
            $invitesList[] = $res_item;
        }

        return $invitesList;
    }

    /**
     * Log user accepting invite activity message
     * @param $userId
     * @param $planId
     * @return bool
     */
    public static function logUserAcceptInviteActivity($userId, $planId): bool
    {
        $userDetails = User::getUserDetails($userId);
        $messages = [
            PlanActivity::getFormattedUserFullnameForActivityMessage($userDetails['full_name']) . " just joined the plan.",
            "We wish you happy savings!"
        ];
        return PlanActivity::createPlanActivity($planId, $messages);
    }
}
