<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\PendingPlanDebt;
use App\Models\Plan;
use App\Models\PlanMember;
use App\Models\UserMonthlyBudget;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function viewDashboard()
    {
        $plans = Plan::getCreatedPlansDetails(Auth::id());
        $memberPlans = PlanMember::getMemberPlansDetails(Auth::id());
        $savings = UserMonthlyBudget::getTotalBudgetSetByUserAcrossAllMonths(Auth::id()) - Expense::getTotalExpenseOfUserAcrossAllPlans(Auth::id());
        return Inertia::render(
            'Dashboard',
            [
                'createdPlans' => $plans,
                'memberPlans' => $memberPlans,
                'lastFiveMonthExpenses' => Expense::getUserExpenseDataForLastFiveMonths(Auth::id()),
                'lastFiveMonthBudget' => UserMonthlyBudget::getUserBudgetDataForLastFiveMonths(Auth::id()),
                'totalSavings' => $savings,
                'totalPending' => PendingPlanDebt::getTotalPendingDebtAcrossAllPlans(Auth::id()),
                'hasUserSetBudgetForCurrentMonth' => UserMonthlyBudget::hasUserSetBudgetForCurrentMonth(Auth::id())
            ]
        );
    }
}
