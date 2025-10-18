<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripePaymentController;

Route::middleware('api')->group(function () {
    Route::post('/stripe/payment', [StripePaymentController::class, 'createPayment']);
});
