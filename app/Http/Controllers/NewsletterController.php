<?php

namespace App\Http\Controllers;

use Hash;
use App\Newsletter;
use App\user;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * The newsletter instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $newsletter;
    protected $user;

    /**
     * Create an instance of newsletter.
     *
     * @param  \Illuminate\Contracts\Auth\Newsletter  $newsletter
     * @return void
     */
    public function __construct(User $user, Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
        $this->user = $user;
    }

    /**
     * create newsletter.
     *
     * @return Response
     */
    public function fetchNewsletters(Request $request)
    {
        $newsletter = $this->newsletter->all();

        if ($newsletter) {
            return response()->json($newsletter, 200);
        }

        return response()->json(['message' => 'Oops, Something went wrong, please try again later'], 400);
    } 

    /**
     * create newsletter.
     *
     * @return Response
     */
    public function createNewsletter(Request $request)
    {
        $this->validate($request, [
            'title'     => 'required',
            'description' => 'required',
        ]);

        $newsletter = $this->newsletter->create([
            'title'      => $request->title,
            'description'  => $request->description,
            'user_id'  => $request->userId,
        ]);

        if ($newsletter) {
            return response()->json(['message' => 'Newsletter was successful created'], 201);
        }

        return response()->json(['message' => 'Oops, Something went wrong, please try again later'], 400);
    }

    /**
     * delete newsletter.
     *
     * @return Response
     */
    public function deleteNewsletter(Request $request)
    {
        $newsletter = $this->newsletter->find($request->id);
        if (!$newsletter) {
            return response()->json(['message' => 'Cannot find Newsletter'], 404);
        }
        $newsletter = $this->newsletter->where(['id' => $request->id, 'user_id' => $request->userId])->first();

        if ($newsletter) {
            $result = $newsletter->delete();
            if ($result) {
                return response()->json(['message' => 'newsletter successfully deleted'], 200);
            } 
            return response()->json(['message' => 'something went wrong, please try again later'], 200);
        }

        return response()->json(['message' => 'You cannot delete a newsletter that you do not own'], 401);
    }
}