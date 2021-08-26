<?php

namespace App\Http\Controllers;

use App\Models\Fusion;
use App\Models\UserInformation;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class FusionController extends Controller
{
    public function createFusionAccount(Request $request): RedirectResponse
    {
        if (!empty(Fusion::getUserAccountId(Auth::id()))) {
            return Redirect::back()->with('message', 'You Fusion account has already been created.');
        }

        $request->validate([
            'phone' => ['required', 'numeric', 'unique:user_information,phone_number'],
            'pan' => ['required', 'string', 'unique:user_information,pan'],
            'gender' => ['required', 'string', Rule::in(['Male', 'Female', 'Other'])],
            'dobYear' => ['required', 'numeric', 'gt:0'],
            'dobMonth' => ['required', 'numeric', 'gt:0'],
            'dobDay' => ['required', 'numeric', 'gt:0']
        ]);
        $userId = Auth::id();
        DB::beginTransaction();
        try {
            $result = (bool) UserInformation::where('user_id', $userId)->update([
                'phone_number' => $request->post('phone'),
                'pan' => $request->post('pan'),
                'gender' => $request->post('gender'),
                'dob_year' => $request->post('dobYear'),
                'dob_month' => $request->post('dobMonth'),
                'dob_day' => $request->post('dobDay'),
            ]);
            $result = $result && Fusion::createAccountHolder($userId);
            $result && Fusion::issueBundle($userId);
            DB::commit();
        } catch (Exception $exception) {
            $result = false;
            $message = $exception->getMessage();
            DB::rollBack();
        }

        if ($result) {
            return Redirect::back()->with('success', 'You Fusion account has been successfully created!');
        }

        return Redirect::back()->with('error', empty($message) ? 'You Fusion account could not be created.' : $message);
    }
}
