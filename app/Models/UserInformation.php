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
        'user_id',
        'phone_number',
        'pan',
        'dob_year',
        'dob_month',
        'dob_day',
        'gender'
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

    public static function getUserDataForFusion($userId)
    {
        $user = User::where('id', $userId)->first();
        $result = [];
        if (empty($user)) {
            return $result;
        }
        $userInformation = self::where('user_id', $userId)->first();
        $result['firstName'] = $user->first_name;
        $result['lastName'] = $user->last_name;
        $result['email'] = $user->email;
        $result['phoneNumber'] = $userInformation->phone_number;
        $result['gender'] = $userInformation->gender;
        $result['pan'] = $userInformation->pan;
        $result['dob_year'] = $userInformation->dob_year;
        $result['dob_month'] = $userInformation->dob_month;
        $result['dob_day'] = $userInformation->dob_day;

        return $result;
    }
}
