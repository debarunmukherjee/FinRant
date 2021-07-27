<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PlanController extends Controller
{
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
