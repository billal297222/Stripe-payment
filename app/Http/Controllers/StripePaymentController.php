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
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
            'name' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // cents
                'currency' => $request->currency,
                'payment_method_types' => ['card'],
            ]);

            $paymentIntent->confirm([
                'payment_method' => $request->stripeToken
            ]);

            return back()->with('success', 'Payment successful!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function createPayment(Request $request){

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
        ]);
       Stripe::setApiKey(config('services.stripe.secret'));

         $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount*100,
            'currency'=> $request->currency,
            'payment_method_types' =>['card'],
         ]);
         return response()->json(['client_secret'=>$paymentIntent->client_secret]);

    }
}

