<?php

namespace App\Http\Controllers;

// use Hash;
use Illuminate\Support\Facades\Hash;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * create user.
     *
     * @return string containing token
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'  => $request->email,
            'password'  => password_hash($request->password, PASSWORD_DEFAULT),
        ]);

        if ($user) {
            return response()->json(['message' => 'Registration was successful'], 201);
        }

        return response()->json(['message' => 'Oops, Registration was Unsuccessful'], 400);
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function authenticate(Request $request) {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);
        // Find the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
        // Verify the password and generate the token
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'token' => $this->generateToken($user)
            ], 200);
        }
        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }

    /**
     * Generate a token for user.
     *
     * @return string
     */
    private function generateToken($user)
    {
        $appSecret    = getenv('APP_SECRET');
        $jwtAlgorithm = getenv('JWT_ALGORITHM');
        $timeIssued   = time();
        $timeExpire   = time() + 60 * 60 * 24 * 30;
        $serverName   = getenv('SERVERNAME');
        $tokenId      = base64_encode(getenv('TOKENID'));
        $payload        = [
            'iss'  => $serverName,    //Issuer: the server name
            'iat'  => $timeIssued,    // Issued at: time when the token was generated
            'jti'  => $tokenId,      // Json Token Id: an unique identifier for the token
            'nbf'  => $timeIssued,   //Not before time
            'exp'  => $timeExpire, //+ 60 * 60 * 24 * 30, // expires in 30 days
            'user_id' => $user->id, //userid
            'role_id' => $user->role_id //roleid
        ];

        return JWT::encode($payload, $appSecret, $jwtAlgorithm);
    }
    
    /**
     * create an admin user.
     *
     * @return string
     */
    public function createAdminUser(Request $request)
    {
        $user = User::where('email', $request['email'])->first();

        if (is_null($user)) {
            return response()->json(['message' => 'Oops, The email does not exist'], 400);
        }

        $user->increment('role_id');

        return response()->json(['message' => 'user role successfully updated'], 200);
    }
}
