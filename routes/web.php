<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;

// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('/', [StripePaymentController::class, 'index']);
Route::post('stripe', [StripePaymentController::class, 'processPayment'])->name('stripe.store');
Route::post('stripe/webhook', [StripePaymentController::class, 'handleWebhook'])->name('stripe.webhook');
