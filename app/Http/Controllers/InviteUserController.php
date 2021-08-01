<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanUserInvite;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class InviteUserController extends Controller
{
    public function createInvite(Request $request): RedirectResponse
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

    public function viewInvites(): Response
    {
        $invitesData = PlanUserInvite::select('first_name', 'last_name', 'email', 'plans.name as plan_name')
                                        ->join('users', 'users.id', '=', 'plan_user_invites.sent_by')
                                        ->join('plans', 'plans.id', '=', 'plan_user_invites.plan_id')
                                        ->where([
                                            ['sent_to', Auth::id()],
                                            ['has_accepted', 0],
                                            ['is_rejected', 0],
                                        ])
                                        ->get();
        $invitesList = [];
        foreach ($invitesData as $invite) {
            $res_item['planName'] = $invite->plan_name;
            $res_item['name'] = $invite->first_name . ' ' . $invite->last_name;
            $res_item['email'] = $invite->email;
            $invitesList[] = $res_item;
        }
        return Inertia::render('ViewInvites', ['invitesList' => $invitesList]);
    }

    private function performInviteActionValidations(Request $request, $userId, $planId, $inviterUserId): void
    {
        $request->validate([
            'planName' => ['required', 'exists:plans,name'],
            'email' => [
                'email',
                'required',
                function ($attribute, $value, $fail) use($userId, $planId, $inviterUserId) {
                    $doesExistInUser = PlanUserInvite::where([
                        ['sent_by', $inviterUserId],
                        ['sent_to', $userId],
                        ['plan_id', $planId],
                        ['has_accepted', 0],
                        ['is_rejected', 0]
                    ])->exists();
                    if (!$doesExistInUser) {
                        $fail('The invite has expired.');
                    }
                }
            ]
        ]);
    }

    public function acceptInvite(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $inviterUserId = User::getUserIdFromEmail($request->post('email'));
        $planId = Plan::getPlanIdFromName($request->post('planName'));

        $this->performInviteActionValidations($request, $userId, $planId, $inviterUserId);

        if (PlanUserInvite::acceptUserInvite($inviterUserId, $userId, $planId)) {
            return Redirect::back()->with('success', 'You have been successfully added to the plan!');
        }
        return Redirect::back()->with('error', 'Could not accept invite');
    }

    public function rejectInvite(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $inviterUserId = User::getUserIdFromEmail($request->post('email'));
        $planId = Plan::getPlanIdFromName($request->post('planName'));

        $this->performInviteActionValidations($request, $userId, $planId, $inviterUserId);

        if (PlanUserInvite::rejectUserInvite($inviterUserId, $userId, $planId)) {
            return Redirect::back()->with('success', 'The invite has been rejected');
        }
        return Redirect::back()->with('error', 'Could not reject invite');
    }
}
