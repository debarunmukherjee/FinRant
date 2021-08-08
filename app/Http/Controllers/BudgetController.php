<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Plan;
use App\Models\PlanCategoryBudget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class BudgetController extends Controller
{
    private function getBudgetAmountKey($isInsert): string
    {
        return $isInsert ? 'amount' : 'editAmount';
    }

    private function performBudgetStateChangeValidations($categoryId, $planId, $userId, $request, $isInsert): void
    {
        $amountKey = $this->getBudgetAmountKey($isInsert);

        $request->validate(
            [
                'planId' => [
                    'required',
                    'numeric',
                    'exists:plans,id',
                    function ($attribute, $value, $fail) use($userId) {
                        if (!Plan::userHasPlanExpenseAccess($userId, $value)) {
                            $fail("You don't have access to this resource.");
                        }
                    }
                ],
                'budgetCategoryName' => [
                    'required',
                    'string',
                    'exists:expend_categories,name',
                    function ($attribute, $value, $fail) use($planId, $userId, $categoryId, $isInsert) {
                        if (empty($categoryId)) {
                            $fail('Invalid Category.');
                        }
                        $checkUniqueTripletExistence = PlanCategoryBudget::
                            where('category_id', $categoryId)
                            ->where('plan_id', $planId)
                            ->where('user_id', $userId)
                            ->exists();
                        if ($isInsert && $checkUniqueTripletExistence) {
                            $fail('You have already set a budget for this category.');
                        } elseif (!$isInsert && !$checkUniqueTripletExistence) {
                            $fail('You cannot update a budget that is not created!');
                        }
                    },
                ],
                $amountKey => ['required', 'integer', 'gt:0'],
            ]
        );
    }

    private function getBudgetStateChangeRequestParams(Request $request, $isInsert): array
    {
        $categoryName = $request->post('budgetCategoryName');
        $amount = $request->post($this->getBudgetAmountKey($isInsert));
        $planId = $request->post('planId');
        $userId = Auth::id();
        $categoryId = ExpendCategory::getCategoryIdFromName($categoryName, $userId);

        return [$categoryId, $planId, $userId, $amount];
    }

    public function addBudget(Request $request): RedirectResponse
    {
        [$categoryId, $planId, $userId, $amount] = $this->getBudgetStateChangeRequestParams($request, true);

        $this->performBudgetStateChangeValidations($categoryId, $planId, $userId, $request, true);

        $budget = new PlanCategoryBudget;
        $budget->category_id = $categoryId;
        $budget->plan_id = $planId;
        $budget->user_id = $userId;
        $budget->amount = $amount;
        $result = $budget->save();

        if (!$result) {
            return Redirect::back()->with('error', 'Could not set budget');
        }
        return Redirect::back()->with('success', 'Your budget is set!');
    }

    public function updateBudget(Request $request): RedirectResponse
    {
        [$categoryId, $planId, $userId, $amount] = $this->getBudgetStateChangeRequestParams($request, false);
        $this->performBudgetStateChangeValidations($categoryId, $planId, $userId, $request, false);

        $budget = PlanCategoryBudget::where([
            ['category_id', $categoryId],
            ['plan_id', $planId],
            ['user_id', $userId]
        ])->first();

        $budget->amount = $amount;
        $result = $budget->save();
        if (!$result) {
            return Redirect::back()->with('error', 'Could not update budget');
        }
        return Redirect::back()->with('success', 'Your budget is updated!');
    }

    public function deleteBudget(Request $request): RedirectResponse
    {
        [$categoryId, $planId, $userId, $amount] = $this->getBudgetStateChangeRequestParams($request, false);
        $budgetsDeleted = PlanCategoryBudget::where([
            ['category_id', $categoryId],
            ['plan_id', $planId],
            ['user_id', $userId]
        ])->delete();
        if (!empty($budgetsDeleted)) {
            return Redirect::back()->with('success', 'Budget item successfully deleted');
        }
        return Redirect::back()->with('error', 'Budget item could not deleted');
    }
}
