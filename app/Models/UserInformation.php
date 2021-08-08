<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PragmaRX\Countries\Package\Countries;

class UserInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_picture',
        'country',
        'user_id'
    ];

    public static function getUserCurrencyCode($userId): string
    {
        $record = self::where('user_id', $userId)->first();
        if (empty($record)) {
            return '';
        }
        $countries = new Countries();
        return $countries->where('name.common', $record->country)->first()->currencies->toArray()[0];
    }
}
