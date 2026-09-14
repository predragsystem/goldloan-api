<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\JewelleryQualityController;
use App\Http\Controllers\Api\JewelleryTypeController;
use App\Http\Controllers\Api\LoanController;
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
        Route::apiResource('customers', CustomerController::class)->except(['destroy']);
        Route::get('/customers/{customer}/loans', [CustomerController::class, 'loans']);

        Route::get('/loans', [LoanController::class, 'index']);
        Route::post('/loans', [LoanController::class, 'store']);
        Route::get('/loans/{loan}', [LoanController::class, 'show']);
        Route::get('/loans/{loan}/transactions', [LoanController::class, 'transactions']);
        Route::get('/loans/{loan}/interest-preview', [LoanController::class, 'interestPreview']);
        Route::post('/loans/{loan}/payments', [LoanController::class, 'pay']);
        Route::post('/loans/{loan}/top-up', [LoanController::class, 'topUp']);
        Route::post('/loans/{loan}/close', [LoanController::class, 'close']);

        Route::apiResource('jewellery-types', JewelleryTypeController::class)->except(['show']);
        Route::apiResource('jewellery-qualities', JewelleryQualityController::class)->except(['show']);
    });
});
