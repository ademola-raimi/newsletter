<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;

class NewsletterControllerTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    // public function testFetchNewletter()
    // {
    //     $user = factory('App\User')->make(
    //         ['id' => -23083333]
    //     );

    //     $newsletter = factory('App\User')->make([
    //         'user_id' => $user->id,
    //     ]);
    //     // dd($newsletter);
    //     $this->get('api/v1/newsletters');

    //     dd($this->response->getContent());
    //     $this->seeStatusCode(200);
    //     $this->assertArrayHasKey('id', $this->response->getContent());
    // }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNewsletterSucceed()
    {
        $user = factory('App\User')->make(
            ['id' => 1]
        );
        $this->json('POST', 'api/v1/newsletter', ['title' => 'newletter1', 'description' => 'this is a description', 'userId' => $user->id])
             ->seeJson([
                'message' => 'Newsletter was successful created',
             ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    // public function testRegisterUserfailedDueToEmailAlreadyExist()
    // {
    //     $this->json('POST', 'api/v1/register', ['name' => 'Sally', 'email' => 'sample@y.com', 'password' => 'sample'])
    //         ->json('POST', 'api/v1/register', ['name' => 'Sally', 'email' => 'sample@y.com', 'password' => 'sample'])
    //          ->seeJson([
    //             'email' => ['The email has already been taken.'],
    //          ]);
    // }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNewsletterfailedDueToRequiredField()
    {
        $this->json('POST', 'api/v1/newsletter', [])
             ->seeJson([
                'title' => ['The title field is required.'],
                'description' => ['The description field is required.'],
             ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    // public function testDeleteNewsletter()
    // {
    //     $user = factory('App\User')->make(
    //         ['id' => 1]
    //     );

    //     $newsletter = factory('App\User')->make([
    //         'id' => 100,
    //         'user_Id' => $user->id
    //     ]);
    //     // dd($newsletter);

    //     $this->json('DELETE', 'api/v1/newsletter/' . $newsletter->id);
    //         dd($this->response->getContent());
    //          // ->seeJson([
    //          //    'message' => 'Newsletter was successful created',
    //          // ]);
    // }

    /**
     * A basic test example.
     *
     * @return void
     */
    // public function testAuthenticateUser()
    // {
    //     $this->json('POST', 'api/v1/register', ['name' => 'sally', 'email' => 'sample@y.com', 'password' => 'sample'])
    //         ->json('POST', 'api/v1/login', ['email' => 'sample@y.com', 'password' => 'sample'])
    //          ->seeStatusCode(200);
    // }

    /**
     * A basic test example.
     *
     * @return void
     */
    // public function testEmailDoesNotExist()
    // {
    //     $this->json('POST', 'api/v1/login', ['email' => 'sample@y.com', 'password' => 'sample'])
    //         ->seeStatusCode(400)
    //         ->seeJson([
    //             'error' => 'Email does not exist.',
    //          ]);
    // }
}
