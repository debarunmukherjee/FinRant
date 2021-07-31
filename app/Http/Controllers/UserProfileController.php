<?php

namespace App\Http\Controllers;

use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use PragmaRX\Countries\Package\Countries;

class UserProfileController extends Controller
{
    public function viewProfile(): Response
    {
        $userId = Auth::id();
        $firstName = Auth::user()->first_name;
        $lastName = Auth::user()->last_name;
        $email = Auth::user()->email;
        $userInformation = UserInformation::all()->where('user_id', $userId)->first();
        $country = $userInformation->country;
        $avatar = $userInformation->profile_picture;

        return Inertia::render('UserProfile', [
            'userDetails' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'country' => $country,
                'avatar' => $avatar
            ],
            'countryList' => Countries::all()->pluck('name.common')
        ]);
    }
}
