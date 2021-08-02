<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getUserIdFromEmail($email)
    {
        $result = self::select('id')->where('email', $email)->first();
        return empty($result) ? 0 : $result->id;
    }

    /**
     * Returns the full name, email and avatar of user with id `$userId`
     * @param $userId
     * @return array
     */
    public static function getUserDetails($userId)
    {
        $result = self::join('user_information', 'user_information.user_id', '=', 'users.id')
                    ->where('users.id', $userId)
                    ->get([DB::raw("concat(first_name, ' ', last_name) as full_name"), 'email', 'profile_picture as avatar'])
                    ->toArray();
        return empty($result) ? [] : $result[0];
    }
}
