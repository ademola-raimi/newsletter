<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\WithoutMiddleware;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    /**
     * user created.
     *
     * @return void
     */
    public function testRegisterUserSucceed()
    {
        $this->json('POST', 'api/v1/register', ['name' => 'Sally', 'email' => 'sample@y.com', 'password' => 'sample'])
             ->seeJson([
                'message' => 'Registration was successful',
             ]);
    }

    /**
     * test email already exist.
     *
     * @return void
     */
    public function testRegisterUserfailedDueToEmailAlreadyExist()
    {
        $this->json('POST', 'api/v1/register', ['name' => 'Sally', 'email' => 'sample@y.com', 'password' => 'sample'])
            ->json('POST', 'api/v1/register', ['name' => 'Sally', 'email' => 'sample@y.com', 'password' => 'sample'])
             ->seeJson([
                'email' => ['The email has already been taken.'],
             ]);
    }

    /**
     * test fields are required.
     *
     * @return void
     */
    public function testRegisterUserfailedDueToRequiredField()
    {
        $this->json('POST', 'api/v1/register', ['name' => 'Sally'])
             ->seeJson([
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
             ]);
    }

    /**
     * test invalid email
     *
     * @return void
     */
    public function testRegisterUserfailedDueToinvalidEmail()
    {
        $this->json('POST', 'api/v1/register', ['name' => 'Sally', 'email' => 'sample.com', 'password' => 'sample'])
             ->seeJson([
                'email' => ['The email must be a valid email address.'],
             ]);
    }

    /**
     * test login
     *
     * @return void
     */
    public function testAuthenticateUser()
    {
        $this->json('POST', 'api/v1/register', ['name' => 'sally', 'email' => 'sample@y.com', 'password' => 'sample'])
            ->json('POST', 'api/v1/login', ['email' => 'sample@y.com', 'password' => 'sample'])
             ->seeStatusCode(200);
    }

    /**
     * test email does not exist.
     *
     * @return void
     */
    public function testEmailDoesNotExist()
    {
        $this->json('POST', 'api/v1/login', ['email' => 'sample@y.com', 'password' => 'sample'])
            ->seeStatusCode(400)
            ->seeJson([
                'error' => 'Email does not exist.',
             ]);
    }
}
