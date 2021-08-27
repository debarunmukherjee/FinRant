<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Expense;
use App\Models\ExpenseDistribution;
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
        $sharedExpenseMembersDistributedEqually =  $request->post('sharedExpenseMembersDistributedEqually');
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
            'sharedExpenseMembersDistributedEqually' => ['required', 'boolean'],
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
            ],
            'sharedExpenseMembersDistribution' => [
                function ($attribute, $value, $fail) use($isSharedExpense, $planId, $sharedExpenseMembersDistributedEqually, $expenseAmount) {
                    if ($isSharedExpense && !$sharedExpenseMembersDistributedEqually) {
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
                            $fail('Sum of distribution does not match total expense');
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
        $sharedExpenseMembersDistributedEqually =  $request->post('sharedExpenseMembersDistributedEqually');
        $sharedExpenseMembersDistribution =  $request->post('sharedExpenseMembersDistribution');
        $expenseAmount = $request->post('amount');
        $totalMemberCount = PlanMember::getMemberCount($planId);
        $planMemberUserIds = PlanMember::getAllPlanMemberUserIds($planId);
        $sharedExpenseMembersUserData = [];
        $sharedExpenseMembersDistributionData = [];

        if (!$sharedExpenseMembersPaidEqually) {
            foreach ($sharedExpenseMembersWhoPaid as $member) {
                $sharedExpenseMembersUserData[User::getUserIdFromEmail($member['email'])] = (float)$member['amount'];
            }
        }

        if (!$sharedExpenseMembersDistributedEqually) {
            foreach ($sharedExpenseMembersDistribution as $member) {
                $sharedExpenseMembersDistributionData[User::getUserIdFromEmail($member['email'])] = (float)$member['amount'];
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
                $sharedExpenseMembersPaidEqually,
                $sharedExpenseMembersDistributedEqually,
                $sharedExpenseMembersDistributionData
            ) {
                if (!$isSharedExpense) {
                    if (Expense::createUnsharedExpenseForUser($userId, $planId, $categoryId, round($expenseAmount, 2))) {
                        return true;
                    }
                    return false;
                }

                $eachAmount = round($expenseAmount / $totalMemberCount, 2);
                $sharedExpenseBatchId = Str::random(64);

                // Making a record of the shared expense details
                $userDataToBeSavedInSharedExpenseDetailsTable = [];
                if ($sharedExpenseMembersPaidEqually) {
                    foreach ($planMemberUserIds as $id) {
                        $userDataToBeSavedInSharedExpenseDetailsTable[$id] = $eachAmount;
                    }
                } else {
                    $userDataToBeSavedInSharedExpenseDetailsTable = $sharedExpenseMembersUserData;
                }
                $result = SharedExpenseDetail::saveSharedExpenseDetail($sharedExpenseBatchId, $userDataToBeSavedInSharedExpenseDetailsTable, $sharedExpenseMembersDistributedEqually);

                // Making a record of the distribution
                $userDataToBeSavedInExpenseDistributionTable = [];
                if ($sharedExpenseMembersDistributedEqually) {
                    foreach ($planMemberUserIds as $id) {
                        $userDataToBeSavedInExpenseDistributionTable[$id] = $eachAmount;
                    }
                } else {
                    $userDataToBeSavedInExpenseDistributionTable = $sharedExpenseMembersDistributionData;
                }
                $result = $result && ExpenseDistribution::saveExpenseDistributionDetail($sharedExpenseBatchId, $userDataToBeSavedInExpenseDistributionTable);

                // Make records of the debt of every member in the plan debt maintaining table, in case the amount was not shared equally.
                if (!($sharedExpenseMembersPaidEqually && $sharedExpenseMembersDistributedEqually)) {
                    foreach ($planMemberUserIds as $planMemberUserId) {
                        $currentDebt = PlanDebt::getCurrentDebtForUser($planId, $planMemberUserId);

                        if ($sharedExpenseMembersPaidEqually) {
                            $amountPaid = $eachAmount;
                        } else {
                            $amountPaid = empty($sharedExpenseMembersUserData[$planMemberUserId]) ? 0 : $sharedExpenseMembersUserData[$planMemberUserId];
                        }

                        if ($sharedExpenseMembersDistributedEqually) {
                            $amountOwed = $eachAmount;
                        } else {
                            $amountOwed = empty($sharedExpenseMembersDistributionData[$planMemberUserId]) ? 0 : $sharedExpenseMembersDistributionData[$planMemberUserId];
                        }

                        $amountPaid = round($amountPaid, 2);
                        $amountOwed = round($amountOwed, 2);
                        $result = $result && PlanDebt::saveDebtAmountForUser($planId, $planMemberUserId, $currentDebt + $amountOwed - $amountPaid);
                    }

                    // Recalculate the debts as the expense is shared equally but not every member paid equally.
                    if ($result) {
                        PendingPlanDebt::refreshPendingPlanDebts($planId);
                    }
                }

                // Lastly, make entries in the expense table for every member.
                $result = $result && Expense::createSharedExpenseForPlanMembers($planId, $categoryId, $userDataToBeSavedInExpenseDistributionTable, $sharedExpenseBatchId);
                // Log shared expense activity
                $result = $result && Expense::logSharedExpenseActivityMessageForPlan(Auth::id(), $planId, $categoryId, $expenseAmount);
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
