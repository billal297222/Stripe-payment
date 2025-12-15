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

    public function handleWebhook(Request $request)
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // Update your order or booking here
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                // Handle failed payment
                break;
        }

        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

}

