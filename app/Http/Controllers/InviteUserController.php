<?php

namespace App\Http\Controllers;

use App\Models\PlanUserInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class InviteUserController extends Controller
{
    public function createInvite(Request $request)
    {
        $userId = Auth::id();
        $planId = $request->post('planId');
        $request->validate([
            'planId' => ['required', 'exists:plans,id'],
            'inviteEmail' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use($userId, $planId) {
                    $doesExistInUser = User::where([
                        ['email', $value],
                        ['id', '!=', $userId]
                    ])->exists();
                    if (!$doesExistInUser) {
                        $fail('The given email is invalid.');
                    } else {
                        $invitedUserId = User::getUserIdFromEmail($value);
                        $isAlreadyInvited = PlanUserInvite::where([
                            ['sent_by', $userId],
                            ['sent_to', $invitedUserId],
                            ['plan_id', $planId]
                        ])->exists();
                        if ($isAlreadyInvited) {
                            $fail('The user is already invited by you.');
                        }
                    }
                }
            ]
        ]);
        $inviteEmail = $request->post('inviteEmail');
        logger($inviteEmail);
        $sentToUserId = User::getUserIdFromEmail($inviteEmail);
        $invite = new PlanUserInvite;
        $invite->plan_id = $planId;
        $invite->sent_by = $userId;
        $invite->sent_to = $sentToUserId;
        $result = $invite->save();
        if (!$result) {
            return Redirect::back()->with('error', 'Could not set invite');
        }
        return Redirect::back()->with('success', 'Your invite request has been sent!');
    }

    public function viewInvites()
    {

    }
}
