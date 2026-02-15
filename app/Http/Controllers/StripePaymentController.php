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

                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;

                break;
        }

        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
  }


}


//  1.createSubscription

// 2.cancelSubscription

// 3.subscriptionStatus

// 4.changePlan

// 5.webhook handler



// Database schema (for reference):Products, Orders, Payments, Users tables with relevant fields and relationships.

// Table users {
//   id int [pk, increment]
//   name varchar
//   email varchar
//   password varchar
//   created_at datetime
//   updated_at datetime
// }

// Table products {
//   id int [pk, increment]
//   name varchar
//   description text
//   price decimal(10,2)
//   created_at datetime
//   updated_at datetime
// }

// Table orders {
//   id int [pk, increment]
//   user_id int [ref: > users.id]    // who bought
//   product_id int [ref: > products.id]
//   amount decimal(10,2)            // order amount
//   status enum('pending','paid','failed','cancelled','expired') [default: 'pending']
//   stripe_payment_intent_id varchar // link to Stripe
//   created_at datetime
//   updated_at datetime
// }

// Table payments {
//   id int [pk, increment]
//   order_id int [ref: > orders.id]
//   stripe_payment_intent_id varchar
//   amount decimal(10,2)
//   currency varchar
//   status enum('pending','succeeded','failed')
//   created_at datetime
//   updated_at datetime
// }





