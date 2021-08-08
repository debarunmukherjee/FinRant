<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Plan;
use App\Models\PlanActivity;
use App\Models\PlanMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanActivityController extends Controller
{
    public function getActivities(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $planId = $request->get('planId');
        if (!Plan::userHasPlanExpenseAccess($userId, $planId)) {
            abort('403');
        }
        $activities = PlanActivity::getActivityMessagesForPlan($planId);
        return response()->json(['status' => 'success', 'activities' => $activities]);
    }
}
