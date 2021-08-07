<?php

namespace App\Http\Controllers;

use App\Models\PendingPlanDebt;
use App\Models\Plan;
use App\Models\PlanDebtTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TransactionController extends Controller
{
    public function clearDebt(Request $request): RedirectResponse
    {
        $planId = $request->post('planId');
        $amount = $request->post('amount');
        $destUserId = User::getUserIdFromEmail($request->post('destUserEmail'));
        $srcUserId = Auth::id();
        $request->validate([
            'planId' => [
                'numeric',
                'required',
                'exists:plans,id',
                function($attribute, $value, $fail) use($srcUserId) {
                    if (!Plan::userHasPlanExpenseAccess($srcUserId, $value)) {
                        $fail("You don't have access to this resource.");
                    }
                }
            ],
            'destUserEmail' => [
                'email',
                'required',
                'exists:users,email',
                function($attribute, $value, $fail) use($destUserId, $planId) {
                    if (!Plan::userHasPlanExpenseAccess($destUserId, $planId)) {
                        $fail("Invalid data");
                    }
                }
            ],
            'amount' => [
                'numeric',
                'required',
                function($attribute, $value, $fail) use($destUserId, $srcUserId, $planId) {
                    $dueAmount = PendingPlanDebt::getAmountUserNeedsToPayToAnotherUser($planId, $srcUserId, $destUserId);
                    if ($dueAmount !== (float)$value) {
                        $fail("Invalid data");
                    }
                }
            ],
            'password' => ['required', 'current-password']

        ]);

        // todo: At this point the user should be redirected to the payment gateway to transfer money to the other user.

        // The work being done below this comment, should ideally be done we get a confirmation that the transaction was successful.

        $result = DB::transaction(
            function () use ($planId, $amount, $srcUserId, $destUserId) {
                $transactionId = Transaction::recordUserTransaction($srcUserId, $destUserId, $amount);
                if (empty($transactionId)) {
                    return false;
                }
                $result = PendingPlanDebt::settleDebtBetweenUsers($planId, $srcUserId, $destUserId);
                return $result && PlanDebtTransaction::savePlanDebtTransaction($planId, $transactionId);
            }
        );

        if ($result) {
            return Redirect::back()->with('success', 'Your debt is cleared!');
        }
        return Redirect::back()->with('error', 'Some error occurred');
    }
}
