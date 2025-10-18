<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function index()
    {
        return view('stripe');
    }

    public function processPayment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            Charge::create([
                'amount' => 100 * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Test payment from Laravel 11',
            ]);

            return back()->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function createPayment(Request $request){


        // Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $request->validate(([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
        ]));
         Stripe::setApiKey(config('stripe.secret'));

         $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount*100,
            'currency'=> $request->currency,
            'payment_method_types' =>['card'],
         ]);
         return response()->json(['client_secret'=>$paymentIntent->client_secret]);

    }
}

