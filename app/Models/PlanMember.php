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
}
