<?php

namespace App\Http\Controllers;

use Hash;
use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * The subscription instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $subscription;

    /**
     * Create an instance of subscription.
     *
     * @param  \Illuminate\Contracts\Auth\subscription  $subscription
     * @return void
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * create subscription.
     *
     * @return Response
     */
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required',
            'newsletterId' => 'required',
        ]);

        $user = $this->newsletter->create([
            'email'      => $request->email,
            'newsletter_id'  => $request->newsletterId,
            'confirmation_id' => str_random(32)
        ]);

        $this->sendEmail($user);

        if ($user) {
            return response()->json(['message' => 'You have succesfully subscribed for this newsletter, please check your email and approve for confirmation'], 201);
        }

        return response()->json(['message' => 'Oops, Something went wrong, please try again later'], 500);
    }

    private function sendEmail($user)
    {

    }

    /**
     * confirm subscription.
     *
     * @return Response
     */
    public function confirmSubscription(Request $request)
    {
        $subscription = $this->subscription->where('confirmation_id', $request->confirmationId)->first();
        if ($subscription) {
            $subscription->confirmed_at = date();
            $subscription->confirmation_id = null;
            $response = $subscription->save();
            if ($response) {
                return response()->json(['message' => 'You have succesfully confirmed your subscription'], 200);
            }

            return response()->json(['message' => 'Oops, Something went wrong, please try again later'], 500);
        }

        return response()->json(['message' => 'We cannot confirm your subscription because you have already confirmed it'], 400);
    }

    /**
     * delete subscription.
     *
     * @return Response
     */
    public function deleteSubscription(Request $request)
    {
        $subscription = $this->subscription->where(['id' => $request->id, 'email' => $request->email])->get();

        if ($subscription) {
            $result = $subscription->delete();
            if (result) {
                return response()->json(['message' => 'subscription successful deleted'], 200);
            } 
            return response()->json(['message' => 'something went wrong, please try again later'], 500);
        }

        return response()->json(['message' => 'You cannot delete a subscription that you do not own'], 401);
    }
}