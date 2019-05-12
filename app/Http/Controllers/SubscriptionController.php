<?php

namespace App\Http\Controllers;

use Hash;
use App\Subscription;
use App\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * The subscription instance.
     *
     * @var \App\subscription
     * @var \App\Newsletter
     */
    protected $subscription;
    protected $newsletter;

    /**
     * Create an instance of subscription.
     *
     * @param  \App\Subscription  $subscription
     * @param  \App\Newsletter  $subscription
     * 
     * @return void
     */
    public function __construct(Subscription $subscription, Newsletter $newsletter)
    {
        $this->subscription = $subscription;
        $this->newsletter = $newsletter;
    }

    /**
     * create subscription.
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request)
    {
        $newsletter = $this->newsletter->find($request->newsletterId);

        if (!$newsletter) {
            return response()->json(['message' => 'Cannot find newsletter'], 404);
        }

        $this->validate($request, [
            'email'     => 'required|email',
            'newsletterId' => 'required',
        ]);

        $user = $this->subscription->create([
            'email'      => $request->email,
            'newsletter_id'  => $request->newsletterId,
            'confirmation_id' => str_random(32),
            'confirmed_at' => null
        ]);

        $this->sendEmail($user, $request);

        if ($user) {
            return response()->json(['message' => 'You have succesfully subscribed for this newsletter, please check your email and approve for confirmation'], 201);
        }

        return response()->json(['message' => 'Oops, Something went wrong, please try again later'], 500);
    }

    /**
     * Send mail to user
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    private function sendEmail($user, $request)
    {
        $link = $request->root() . "/api/v1/confirm/subscription/" . $user->confirmation_id;
         Mail::raw('Thank you for subscribing, click this link to confirm ' . $link, function($msg) use ($user) {
            $msg->to([$user->email]); 
        });
    }

    /**
     * confirm subscription.
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function confirmSubscription(Request $request)
    {
        $subscription = $this->subscription->where('confirmation_id', $request->confirmationId)->first();
        if ($subscription) {
            $subscription->confirmed_at = Carbon::now();
            $subscription->confirmation_id = null;
            $response = $subscription->save();
            if ($response) {
                return response()->json(['message' => 'You have succesfully confirmed your subscription'], 200);
            }

            return response()->json(['message' => 'Oops, Something went wrong, please try again later'], 500);
        }

        return response()->json(['message' => 'We cannot confirm your subscription either because you have already confirmed it or the link is broken'], 400);
    }

    /**
     * delete subscription.
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteSubscription(Request $request)
    {
        $subscription = $this->subscription->find($request->id);
        if (!$subscription) {
            return response()->json(['message' => 'Cannot find subscription'], 404);
        }
        if (is_null($request->email) || empty($request->email)) {
            return response()->json(['message' => 'email cannot be empty, Please pass email as a query'], 401);
        }

        $subscription = $this->subscription->where(['id' => $request->id, 'email' => $request->email])->first();

        if ($subscription) {
            $result = $subscription->delete();
            if ($result) {
                return response()->json(['message' => 'subscription successful deleted'], 200);
            } 
            return response()->json(['message' => 'something went wrong, please try again later'], 500);
        }

        return response()->json(['message' => 'You cannot delete a subscription that you do not own'], 401);
    }
}