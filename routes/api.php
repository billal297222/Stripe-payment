<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\SubscriptionController;

Route::middleware('api')->group(function () {
    Route::post('/stripe/payment', [StripePaymentController::class, 'createPayment']);
    Route::post('/subscription/create', [SubscriptionController::class, 'createSubscription']);
    Route::post('/subscription/cancel/{id}', [SubscriptionController::class, 'cancelSubscription']);
    Route::post('/subscription/change-plan/{id}', [SubscriptionController::class, 'changePlan']);
    Route::get('/subscription/status/{id}', [SubscriptionController::class, 'subscriptionStatus']);

});
