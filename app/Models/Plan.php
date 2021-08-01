<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function getPlanCreatorUserId($planId)
    {
        return self::select('created_by')->where('id', $planId)->first()->created_by;
    }
}
