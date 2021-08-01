<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $invite = self::where([
            ['sent_by', $inviterUserId],
            ['sent_to', $userId],
            ['plan_id', $planId]
        ])->first();

        $invite->has_accepted = 1;
        $result = $invite->save();

        $planMember = new PlanMember;
        $planMember->plan_id = $planId;
        $planMember->user_id = $userId;
        return $result && $planMember->save();
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
}
