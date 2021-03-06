<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Expense;
use App\Models\Fusion;
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
            'upiId' => ['required', 'string'],
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
        $destUserAccountId = Fusion::getUserAccountId($destUserId);
        $srcUserAccountId = Fusion::getUserAccountId($srcUserId);
        if (empty($destUserAccountId)) {
            return Redirect::back()->with('error', 'The destination user has not yet setup a Fusion account!');
        }
        if (empty($srcUserAccountId)) {
            return Redirect::back()->with('error', 'You have not yet setup a Fusion account! You can do so by visiting your profile and submitting the required details.');
        }
        // The work being done below this comment, should ideally be done we get a confirmation that the transaction was successful.

        $result = DB::transaction(
            function () use ($planId, $amount, $srcUserId, $destUserId) {
                [$transactionUniqueKey, $fusionTransactionId] = Fusion::transferFundsToUser($destUserId, $amount);
                $transactionId = Transaction::recordUserTransaction($destUserId, $srcUserId, $amount, $transactionUniqueKey, $fusionTransactionId);
                if (empty($transactionId)) {
                    return false;
                }
                $result = PendingPlanDebt::settleDebtBetweenUsers($planId, $srcUserId, $destUserId);
                $result = $result && PendingPlanDebt::logUserDebtClearanceActivity($planId, $amount, $srcUserId, $destUserId);
                return $result && PlanDebtTransaction::savePlanDebtTransaction($planId, $transactionId);
            }
        );

        if ($result) {
            return Redirect::back()->with('success', 'Your debt is cleared!');
        }
        return Redirect::back()->with('error', 'Some error occurred');
    }

    public function interPlanFundTransfer(Request $request): RedirectResponse
    {
        $userId = (int)Auth::id();
        $planId = (int)$request->post('planId');
        $destUserId = User::getUserIdFromEmail($request->post('selectedUserEmail'));
        $amount = $request->post('amount');
        $categoryId = ExpendCategory::getCategoryIdFromName($request->post('category'), $userId);
        $request->validate([
            'planId' => ['required', 'numeric', 'exists:plans,id'],
            'upiId' => ['required', 'string'],
            'selectedUserEmail' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use($userId, $planId) {
                    $destUserId = User::getUserIdFromEmail($value);
                    if (!Plan::usersCanExchangeFundsInPlan($userId, $destUserId, $planId)) {
                        $fail('You cannot pay this user.');
                    }
                }
            ],
            'category' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use($categoryId) {
                    if (empty($categoryId)) {
                        $fail('Invalid Category.');
                    }
                }
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'password' => ['required', 'current-password']
        ]);

        // todo: At this point the user should be redirected to the payment gateway to transfer money to the other user.
        $destUserAccountId = Fusion::getUserAccountId($destUserId);
        $srcUserAccountId = Fusion::getUserAccountId($userId);
        if (empty($destUserAccountId)) {
            return Redirect::back()->with('error', 'The destination user has not yet setup a Fusion account!');
        }
        if (empty($srcUserAccountId)) {
            return Redirect::back()->with('error', 'You have not yet setup a Fusion account! You can do so by visiting your profile and submitting the required details.');
        }

        // On successful transfer, we will record the expense and transaction - the stuff being done below.

        $result = DB::transaction(
            function () use ($planId, $userId, $destUserId, $amount, $categoryId) {
                [$transactionUniqueKey, $fusionTransactionId] = Fusion::transferFundsToUser($destUserId, $amount);
                $transactionId = Transaction::recordUserTransaction($destUserId, $userId, $amount, $transactionUniqueKey, $fusionTransactionId);
                if (empty($transactionId)) {
                    return false;
                }

                return Expense::createUnsharedExpenseForUser($userId, $planId, $categoryId, $amount, $transactionId);
            }
        );

        if ($result) {
            return Redirect::back()->with('success', 'Your transaction is successful and expense is recorded!');
        }
        return Redirect::back()->with('error', 'Some error occurred');
    }
}
