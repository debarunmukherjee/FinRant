<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function viewDashboard()
    {
        $plans = Plan::getCreatedPlansDetails(Auth::id());
        $memberPlans = PlanMember::getMemberPlansDetails(Auth::id());
        return Inertia::render('Dashboard', ['createdPlans' => $plans, 'memberPlans' => $memberPlans]);
    }
}
