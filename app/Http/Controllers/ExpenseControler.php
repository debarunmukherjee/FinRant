<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use App\Models\Expense;
use App\Models\Plan;
use App\Models\PlanMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class ExpenseControler extends Controller
{
    public function createExpense(Request $request)
    {
        $userId = Auth::id();
        $categoryList = array_column(ExpendCategory::getAllCategoriesForCurrentUser(), 'name');
        $request->validate([
            'isSharedExpense' => ['required', 'boolean'],
            'category' => ['required', 'string', Rule::in($categoryList)],
            'amount' => ['required', 'numeric', 'gt:0'],
            'planId' => [
                'required',
                'numeric',
                'exists:plans,id',
                function ($attribute, $value, $fail) use($userId) {
                    if (!(Plan::isUserPlanCreator($value, $userId) || PlanMember::isUserPlanMember($value, $userId))) {
                        $fail("You don't have access to this resource");
                    }
                },
            ],
        ]);

        $expense = new Expense;
        $expense->plan_id = $request->post('planId');
        $expense->category_id = ExpendCategory::getCategoryIdFromName($request->post('category'));
        $expense->user_id = Auth::id();
        $expense->amount = $request->post('amount');
        $expense->is_shared = $request->post('isSharedExpense');
        if ($expense->save()) {
            return Redirect::back()->with('success', 'Your expense is recorded!');
        }
        return Redirect::back()->with('error', 'Could not save expense');
    }
}
