<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Expense;
use App\Models\PendingPlanDebt;
use App\Models\Plan;
use App\Models\PlanDebt;
use App\Models\PlanMember;
use App\Models\SharedExpenseDetail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    private function performCreateExpenseValidations(Request $request, $categoryList): void
    {
        $userId = Auth::id();
        $isSharedExpense = $request->post('isSharedExpense');
        $planId = $request->post('planId');
        $sharedExpenseMembersPaidEqually =  $request->post('sharedExpenseMembersPaidEqually');
        $expenseAmount = (int)$request->post('amount');
        $request->validate([
            'isSharedExpense' => ['required', 'boolean'],
            'category' => ['required', 'string', Rule::in($categoryList)],
            'amount' => ['required', 'numeric', 'gt:0'],
            'planId' => [
                'required',
                'numeric',
                'exists:plans,id',
                function ($attribute, $value, $fail) use($userId) {
                    if (!Plan::userHasPlanExpenseAccess($userId, $value)) {
                        $fail("You don't have access to this resource");
                    }
                },
            ],
            'sharedExpenseMembersPaidEqually' => ['required', 'boolean'],
            'sharedExpenseMembersWhoPaid' => [
                function ($attribute, $value, $fail) use($isSharedExpense, $planId, $sharedExpenseMembersPaidEqually, $expenseAmount) {
                    if ($isSharedExpense && !$sharedExpenseMembersPaidEqually) {
                        $isValidData = true;
                        $totalAmount = 0;
                        foreach ($value as $member) {
                            $userId = User::getUserIdFromEmail($member['email']);
                            if ((int)$member['amount'] <= 0 || !Plan::userHasPlanExpenseAccess($userId, $planId)) {
                                $isValidData = false;
                                break;
                            }
                            $totalAmount += (int)$member['amount'];
                        }
                        if (!$isValidData || $totalAmount !== $expenseAmount) {
                            $fail('Invalid data of users who paid for the shared expense');
                        }
                    }
                }
            ]
        ]);
    }

    public function createExpense(Request $request)
    {
        $userId = Auth::id();
        $categoryList = array_column(ExpendCategory::getAllCategoriesForCurrentUser(), 'name');
        $isSharedExpense = $request->post('isSharedExpense');
        $planId = $request->post('planId');
        $categoryId = ExpendCategory::getCategoryIdFromName($request->post('category'), $userId);
        $sharedExpenseMembersPaidEqually =  $request->post('sharedExpenseMembersPaidEqually');
        $sharedExpenseMembersWhoPaid =  $request->post('sharedExpenseMembersWhoPaid');
        $expenseAmount = $request->post('amount');
        $totalMemberCount = PlanMember::getMemberCount($planId);
        $planMemberUserIds = PlanMember::getAllPlanMemberUserIds($planId);
        $sharedExpenseMembersUserData = [];

        if (!$sharedExpenseMembersPaidEqually) {
            foreach ($sharedExpenseMembersWhoPaid as $member) {
                $sharedExpenseMembersUserData[User::getUserIdFromEmail($member['email'])] = (float)$member['amount'];
            }
        }

        $this->performCreateExpenseValidations($request, $categoryList);

        $result = DB::transaction(
            function () use (
                $isSharedExpense,
                $expenseAmount,
                $userId,
                $planId,
                $categoryId,
                $totalMemberCount,
                $sharedExpenseMembersUserData,
                $planMemberUserIds,
                $sharedExpenseMembersPaidEqually
            ) {
                if (!$isSharedExpense) {
                    if (Expense::createUnsharedExpenseForUser($userId, $planId, $categoryId, round($expenseAmount, 2))) {
                        return true;
                    }
                    return false;
                }

                $eachAmount = round($expenseAmount / $totalMemberCount, 2);

                // Making a record of the shared expense details
                $sharedExpenseBatchId = Str::random(64);
                $userDataToBeSavedInSharedExpenseDetailsTable = [];
                if ($sharedExpenseMembersPaidEqually) {
                    foreach ($planMemberUserIds as $id) {
                        $userDataToBeSavedInSharedExpenseDetailsTable[$id] = $eachAmount;
                    }
                } else {
                    $userDataToBeSavedInSharedExpenseDetailsTable = $sharedExpenseMembersUserData;
                }
                $result = SharedExpenseDetail::saveSharedExpenseDetail($sharedExpenseBatchId, $userDataToBeSavedInSharedExpenseDetailsTable);

                // Make records of the debt of every member in the plan debt maintaining table, in case the amount was not shared equally.
                if (!$sharedExpenseMembersPaidEqually) {
                    foreach ($planMemberUserIds as $planMemberUserId) {
                        $currentDebt = PlanDebt::getCurrentDebtForUser($planId, $planMemberUserId);
                        $amountPaid = empty($sharedExpenseMembersUserData[$planMemberUserId]) ? 0 : $sharedExpenseMembersUserData[$planMemberUserId];
                        $amountPaid = round($amountPaid, 2);
                        $result = $result && PlanDebt::saveDebtAmountForUser($planId, $planMemberUserId, $currentDebt + $eachAmount - $amountPaid);
                    }

                    // Recalculate the debts as the expense is shared equally but not every member paid equally.
                    if ($result) {
                        PendingPlanDebt::refreshPendingPlanDebts($planId);
                    }
                }

                // Lastly, make entries in the expense table for every member.
                $result = $result && Expense::createSharedExpenseForAllPlanMembers($planId, $categoryId, $eachAmount, $sharedExpenseBatchId);
                if (!$result) {
                    throw new \Exception("Couldn't save expense item");
                }
                return true;
            }
        );

        if ($result) {
            return Redirect::back()->with('success', 'Your expense is recorded!');
        }
        return Redirect::back()->with('error', 'Could not save expense');
    }

    public function getTotalUserExpenseForPlan(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $planId = $request->get('planId');

        $request->validate([
            'planId' => [
                'required',
                'numeric',
                'exists:plans,id',
                function ($attribute, $value, $fail) use ($userId) {
                    if (!Plan::userHasPlanExpenseAccess($userId, $value)) {
                        $fail('Invalid Request');
                    }
                }
            ]
        ]);

        return response()->json(['status' => 'success', 'amount' => Expense::getTotalExpenseOfUserInPlan($planId, $userId)]);
    }
}
