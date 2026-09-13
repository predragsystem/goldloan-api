<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // --- Public / onboarding (no auth) ---
    Route::post('/auth/signup', [AuthController::class, 'signup']);
    Route::post('/auth/otp/request', [AuthController::class, 'requestOtp']);
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/plans', [BillingController::class, 'plans']);

    // Razorpay calls this directly — no user session, verified by signature instead.
    Route::post('/billing/webhook', [BillingController::class, 'webhook']);

    // --- Authenticated, but NOT subscription-gated (so a locked-out owner can still pay) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', fn (Request $request) => $request->user()->load('tenant', 'role.permissions'));

        Route::post('/billing/checkout', [BillingController::class, 'checkout']);
        Route::get('/billing/subscription', [BillingController::class, 'subscription']);
        Route::get('/billing/history', [BillingController::class, 'history']);
    });

    // --- Authenticated AND subscription must be active: everything loan-domain ---
    Route::middleware(['auth:sanctum', 'subscription.active'])->group(function () {
        // Staff, customers, loans, master data, and reports controllers land here
        // as they're built next — same pattern as the groups above.
    });
});
