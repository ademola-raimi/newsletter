<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use App\Http\Helpers\Utility;

class AdminUserMiddleware
{
    /**
     * declaring constant signifying to compare to the role_id
     *
    */
    const REGULAR_USER = '1';
    const ADMIN_USER   = '2';

    /**
     * The utility instance.
     *
     * @var $utility
     */
    private $utility;

    /**
     * Create an instance of Utility.
     *
     * @param $newsletter
     * @return void
     */
    public function __construct(Utility $utility)
    {
        $this->utility = $utility;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * 
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('authorization');

        if (!empty($authHeader)) {
            $credentials = $this->utility->decodeToken($authHeader);

            if ($credentials) {
                if ($credentials->role_id === self::ADMIN_USER) {
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
