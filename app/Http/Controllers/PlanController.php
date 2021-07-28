<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::where('created_by', Auth::id())->orderByDesc('created_at')->get();
        return Inertia::render('Plans/Plans', ['plans' => $plans]);
    }

    public function getPlan($planId)
    {
        $plan = Plan::where([['created_by', Auth::id()], ['id', $planId]])->first();
        if (empty($plan)) {
            abort(404);
        }
        return Inertia::render('Plans/ViewPlan', ['planDetails' => $plan]);
    }

    public function createPlan(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:50']
        ]);
        Plan::create([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'created_by' => Auth::id(),
        ]);
        return Redirect::route('plans');
    }
}
