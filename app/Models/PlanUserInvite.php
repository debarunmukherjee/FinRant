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
}
