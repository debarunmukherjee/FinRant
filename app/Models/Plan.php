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

    public static function getPlanIdFromName($name)
    {
        return self::select('id')->where('name', $name)->first()->id;
    }

    public static function getPlanDetails($planId)
    {
        return self::where('id', $planId)->first();
    }

    public static function getPlanCreatorUserId($planId)
    {
        return self::select('created_by')->where('id', $planId)->first()->created_by;
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
}
