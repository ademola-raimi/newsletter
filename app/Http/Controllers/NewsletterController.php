<?php

namespace App\Http\Controllers;

use Hash;
use App\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * The newsletter instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $newsletter;

    /**
     * Create an instance of newsletter.
     *
     * @param  \Illuminate\Contracts\Auth\Newsletter  $newsletter
     * @return void
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }
    /**
     * create user.
     *
     * @return string containing token
     */
    public function newsletter(Request $request)
    {
        $this->validate($request, [
            'title'     => 'required',
            'description' => 'required',
        ]);

        $user = User::create([
            'title'      => $request->name,
            'description'  => $request->email,
            'user_id'  => $request->userId,
        ]);

        if ($user) {
            return response()->json(['message' => 'Registration was successful, your token is: ' . $user->api_token], 201);
        }

        return response()->json(['message' => 'Oops, Registration was Unsuccessful'], 400);
    }
}