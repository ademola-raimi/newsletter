<?php

namespace App\Http\Helpers\Utility;

class Utility
{

    public function decodeToken($token)
    {
        try {
            return JWT::decode($token, getenv('APP_SECRET'), [getenv('JWT_ALGORITHM')]);
        } catch(ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
    }
}