<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;
use App\Models\Subscription;
use App\Models\User;
use Stripe\Customer;

class SubscriptionController extends Controller
{

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
        ]);

       // Stripe::setApiKey(config('services.stripe.secret'));

        try {

            $customer = \Stripe\Customer::create([
                'email' => auth()->user()->email,
            ]);



            // Create Stripe subscription
            $subscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $request->price_id],
                ],
            ]);

            // Save subscription details in the database
            Subscription::create([
                'user_id' => auth()->id(),
                'stripe_subscription_id' => $subscription->id,
                'stripe_customer_id' => $customer->id,
                'stripe_price_id' => $request->price_id,
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ]);

            return response()->json(['message' => 'Subscription created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancelSubscription($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);

            // Cancel Stripe subscription
            \Stripe\Subscription::update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            // Update subscription status in the database
            $subscription->status = 'canceled';
            $subscription->save();

            return response()->json(['message' => 'Subscription canceled successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changePlan(Request $request, $id)
    {
        $request->validate([
            'new_price_id' => 'required|string',
        ]);

        try {
            $subscription = Subscription::findOrFail($id);

            \Stripe\Subscription::update($subscription->stripe_subscription_id, [
                'items' => [
                    [
                        'id' => $subscription->stripe_subscription_id,
                        'price' => $request->new_price_id,
                    ],
                ],
            ]);

            $subscription->stripe_price_id = $request->new_price_id;
            $subscription->save();

            return response()->json(['message' => 'Subscription plan changed successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function subscriptionStatus($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);

            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);

            return response()->json(['status' => $stripeSubscription->status]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePlan(Request $request, $id){

            $request->validate([
                'new_price_id' => 'required|string',
            ]);

            try {
                $subscription = Subscription::findOrFail($id);

                \Stripe\Subscription::update($subscription->stripe_subscription_id, [
                    'items' => [
                        [
                            'id' => $subscription->stripe_subscription_id,
                            'price' => $request->new_price_id,
                        ],
                    ],
                ]);

                $subscription->stripe_price_id = $request->new_price_id;
                $subscription->save();

                return response()->json(['message' => 'Subscription plan updated successfully!']);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
    }



}





