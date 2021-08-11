<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInformation;
use App\Models\UserMonthlyBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        $avatar = asset('storage/images/' . $userInformation->profile_picture);

        return Inertia::render('UserProfile', [
            'userDetails' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'country' => $country,
                'avatar' => $avatar,
                'currency' => UserInformation::getUserCurrencyCode($userId),
                'monthlyBudget' => UserMonthlyBudget::getUserBudgetForCurrentMonth($userId)
            ],
            'countryList' => Countries::all()->pluck('name.common')->toArray()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['string', 'max:255', 'nullable'],
            'country' => ['required', Rule::in(Countries::all()->pluck('name.common')->toArray())],
            'email' => ['email', 'required', Rule::unique('users')->ignore(Auth::id())],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'monthlyBudget' => ['required', 'numeric', 'gt:0']
        ]);

        $result = DB::transaction(function () use ($request) {
            $user = User::where('id', Auth::id())->first();
            $user->first_name = $request->post('firstName');
            $user->last_name = $request->post('lastName');
            $user->email = $request->post('email');
            $result = $user->save();

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $fileName = bin2hex(random_bytes(16)) . '.' . $image->getClientOriginalExtension();
                Storage::disk('public')->put("images/$fileName", file_get_contents($image->getRealPath()));
            }

            $userInformation = UserInformation::where('user_id', Auth::id())->first();
            $userInformation->country = $request->post('country');
            if (!empty($fileName)) {
                $userInformation->profile_picture = $fileName;
            }
            $result = $result && $userInformation->save();
            return $result && UserMonthlyBudget::saveUserBudgetForCurrentMonth(Auth::id(), (float)$request->post('monthlyBudget'));
        });


        if (!$result) {
            return Redirect::back()->with('error', 'Could not save user information.');
        }
        return Redirect::back()->with('success', 'Your information is successfully updated!');
    }
}
