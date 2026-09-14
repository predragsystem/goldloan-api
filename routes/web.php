<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoanController;
use Illuminate\Support\Facades\Route;

// --- Public ---
Route::get('/', function () {
    return view('landing', [
        'plans' => \App\Models\SubscriptionPlan::where('is_active', true)->get(),
    ]);
});

Route::middleware('guest')->group(function () {
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/otp/{phone}/{purpose}', [AuthController::class, 'showOtp'])->name('otp.show');
    Route::post('/otp/request', [AuthController::class, 'requestOtp'])->name('otp.request');
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// --- Authenticated app, mounted at /app on the same domain ---
Route::prefix('app')->middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/billing', [BillingController::class, 'show'])->name('billing.show');
    Route::post('/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');

    // Everything below actually requires an active subscription.
    Route::middleware('subscription.active')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['edit', 'update', 'destroy']);

        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{loan}/pay', [LoanController::class, 'pay'])->name('loans.pay');
        Route::post('/loans/{loan}/top-up', [LoanController::class, 'topUp'])->name('loans.topUp');
        Route::post('/loans/{loan}/close', [LoanController::class, 'close'])->name('loans.close');
    });
});
