<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Expense;
use App\Models\PendingPlanDebt;
use App\Models\Plan;
use App\Models\PlanCategoryBudget;
use App\Models\PlanDebt;
use App\Models\PlanMember;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::getCreatedPlansDetails(Auth::id());
        $memberPlans = PlanMember::getMemberPlansDetails(Auth::id());
        return Inertia::render('Plans/Plans', ['createdPlans' => $plans, 'memberPlans' => $memberPlans]);
    }

    public function getPlan($planId)
    {
        $planCreatorId = Plan::getPlanCreatorUserId($planId);
        $isUserPlanMember = PlanMember::isUserPlanMember($planId, Auth::id());
        if (empty($planCreatorId) || ($planCreatorId !== (int)Auth::id() && !$isUserPlanMember)) {
            abort(404);
        }
        $plan = Plan::getPlanDetails($planId);
        $budgetList = PlanCategoryBudget::getBudgetListForCurrentPlan($planId);
        $categoryList = ExpendCategory::getAllCategoriesForCurrentUser();
        $planRole = $isUserPlanMember ? 'member' : 'creator';
        $planMemberList = PlanMember::getPlanMembersDetailsList($planId);
        $creatorUserDetails = User::getUserDetails($planCreatorId);
        $planMemberList[$planCreatorId] = [
            'full_name' => $creatorUserDetails['full_name'],
            'email' => $creatorUserDetails['email'],
            'avatar' => $creatorUserDetails['avatar'],
            'role' => 'creator',
            'userId' => $planCreatorId
        ];
        $formattedPlanMemberList = [];
        foreach ($planMemberList as $member) {
            $res['fullName'] = $member['full_name'];
            $res['email'] = $member['email'];
            $res['avatar'] = asset('storage/images/' . $member['avatar']);
            $res['role'] = $member['role'];
            $formattedPlanMemberList[] = $res;
        }

        // Get all the pending transactions for current user to settle debts
        $pendingTransactions = PendingPlanDebt::getAllPendingPlanTransactionsForUser($planId, Auth::id());
        $userPendingTransactionsResponseData = [];
        foreach ($pendingTransactions as $transaction) {
            $userPendingTransactionsResponseData[] = [
                'otherUserEmail' => $planMemberList[$transaction['otherUserId']]['email'],
                'action' => $transaction['action'],
                'amount' => $transaction['amount'],
            ];
        }

        // Get all the expenses data for the user in the plan
        $allExpensesData = Expense::getAllUserExpenses($planId, Auth::id());
        $formattedAllExpensesData = [];
        foreach ($allExpensesData as $allExpensesDatum) {
            $formattedSharedExpenseDetails = [];
            if ($allExpensesDatum['isShared']) {
                foreach ($allExpensesDatum['shareDetails'] as $shareDetail) {
                    $formattedSharedExpenseDetails[] = [
                        'userEmailWhoPaid' => $planMemberList[$shareDetail['userIdWhoPaid']]['email'],
                        'amount' => $shareDetail['amount']
                    ];
                }
            }
            $createdAt = new DateTime($allExpensesDatum['createdAt']);
            $createdAt->setTimezone(new DateTimeZone('Asia/Kolkata'));
            $formattedAllExpensesData[] = [
                'categoryName' => $allExpensesDatum['categoryName'],
                'amount' => $allExpensesDatum['amount'],
                'isShared' => $allExpensesDatum['isShared'],
                'shareDetails' => $formattedSharedExpenseDetails,
                'createdAt' => $createdAt->format('D, Y-m-d H:i:s')
            ];
        }

        return Inertia::render('Plans/ViewPlan',
            [
                'planDetails' => $plan,
                'budgetList' => $budgetList,
                'categoryList' => $categoryList,
                'planRole' => $planRole,
                'planMemberList' => $formattedPlanMemberList,
                'userPendingTransactions' => $userPendingTransactionsResponseData,
                'allUserExpenses' => $formattedAllExpensesData
            ]
        );
    }

    public function createPlan(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:255']
        ]);
        $result = DB::transaction(function () use ($request) {
            $plan = Plan::create([
                'name' => $request->post('name'),
                'description' => $request->post('description'),
                'created_by' => Auth::id(),
            ]);
            return !empty($plan) && PlanDebt::createEntryPlanDebtForNewUser($plan->id, Auth::id());
        });
        if ($result) {
            return Redirect::back()->with('success', 'Plan successfully created!');
        }
        return Redirect::back()->with('error', 'Plan could not be created!');
    }
}
