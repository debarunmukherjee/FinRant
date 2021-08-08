<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InviteUserController;
use App\Http\Controllers\PlanActivityController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/plan/{planId}', [PlanController::class, 'getPlan'])->name('plan.view');
    Route::get('/plans', [PlanController::class, 'index'])->name('plans');
    Route::post('/create-plan', [PlanController::class, 'createPlan']);
    Route::post('/category/add', [CategoryController::class, 'createCategory']);
    Route::post('/budget/add', [BudgetController::class, 'addBudget']);
    Route::put('/budget/edit', [BudgetController::class, 'updateBudget']);
    Route::post('/budget/delete', [BudgetController::class, 'deleteBudget']);
    Route::get('/user/profile', [UserProfileController::class, 'viewProfile'])->name('user.profile');
    Route::post('/user/update', [UserProfileController::class, 'updateProfile']);
    Route::post('/invite/user', [InviteUserController::class, 'createInvite']);
    Route::get('/view/invites', [InviteUserController::class, 'viewInvites'])->name('invites.view');
    Route::put('/invite/accept', [InviteUserController::class, 'acceptInvite']);
    Route::put('/invite/reject', [InviteUserController::class, 'rejectInvite']);
    Route::post('/expense/add', [ExpenseController::class, 'createExpense']);
    Route::post('/settle-dues', [TransactionController::class, 'clearDebt']);
    Route::post('/user/plan/fund-transfer', [TransactionController::class, 'interPlanFundTransfer']);
    Route::get('/plan-activities', [PlanActivityController::class, 'getActivities']);
});

require __DIR__.'/auth.php';
