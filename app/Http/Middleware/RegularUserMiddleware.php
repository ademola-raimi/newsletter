<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class RegularUserMiddleware
{
    /**
     * declaring constant signifying to compare to the role_id
     *
    */
    const REGULAR_USER = '1';
    const ADMIN_USER   = '2';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private function decodeToken($token)
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

    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('authorization');


        if (!empty($authHeader)) {
            $credentials = $this->decodeToken($authHeader);
            if (credentials) {
                if ($credentials->role_id === self::ADMIN_USER || $credentials->role_id === self::REGULAR_USER) {
                    $request->userId = $credentials->user_id;
                    $request->roleId = $credentials->role_id;

                    return $next($request);
                }

                return response()->json(['message' => 'User unauthorized due to access level, only admin user can perform this action.'], 401);
            }
        }

        return response()->json(['message' => 'User unauthorized due to empty token'], 401);
    }
}

