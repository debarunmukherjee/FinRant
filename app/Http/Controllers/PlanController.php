<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Plan;
use App\Models\PlanCategoryBudget;
use App\Models\PlanMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return Inertia::render('Plans/ViewPlan', ['planDetails' => $plan, 'budgetList' => $budgetList, 'categoryList' => $categoryList]);
    }

    public function createPlan(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:255']
        ]);
        Plan::create([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'created_by' => Auth::id(),
        ]);
        return Redirect::back()->with('success', 'Plan successfully created!');
    }
}
