<?php

namespace App\Http\Controllers;

use Hash;
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
    public function postRegister(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'  => $request->email,
            'password'  => password_hash($request->password, PASSWORD_DEFAULT),
            'api_token' => $this->generateToken(),
        ]);

        if ($user) {
            return response()->json(['message' => 'Registration was successful, your token is: ' . $user->api_token], 201);
        }

        return response()->json(['message' => 'Oops, Registration was Unsuccessful'], 400);
    }

    /**
     * Generate a token for user.
     *
     * @return string
     */
    public function generateToken()
    {
        $appSecret    = getenv('APP_SECRET');
        $jwtAlgorithm = getenv('JWT_ALGORITHM');
        $timeIssued   = time();
        $serverName   = getenv('SERVERNAME');
        $tokenId      = base64_encode(getenv('TOKENID'));
        $token        = [
            'iss'  => $serverName,    //Issuer: the server name
            'iat'  => $timeIssued,    // Issued at: time when the token was generated
            'jti'  => $tokenId,      // Json Token Id: an unique identifier for the token
            'nbf'  => $timeIssued,   //Not before time
            'exp'  => $timeIssued //+ 60 * 60 * 24 * 30, // expires in 30 days
        ];
        dd(JWT::encode($token, $appSecret, $jwtAlgorithm));
        return JWT::encode($token, $appSecret, $jwtAlgorithm);
    }
    
    public function createAdminUser(Request $request)
    {
        $user = User::where('username', $request['username'])->first();

        if (is_null($user)) {
            return response()->json(['message' => 'Oops, The username does not exist'], 400);
        }

        $user->increment('role_id');

        return response()->json(['message' => 'user role successfully updated'], 400);
    }
}
