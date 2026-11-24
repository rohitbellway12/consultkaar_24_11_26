<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User; // Make sure to import the User model

class CustomJwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $tokenFromHeader = JWTAuth::getToken();


            $user = JWTAuth::toUser($tokenFromHeader);
            
            // Retrieve the current route path
             $currentPath = request()->path(); 

            if($user instanceof \App\Models\User){
                $customerId = $user->id;
                $authTokenFromDB = $user->auth_token;
                if ($tokenFromHeader == $authTokenFromDB) {                
                    // Tokens match, continue with the request
                    return $next($request);
                }
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Token mismatch'], 401);
            } else {
                return response()->json(['status' => 'error', 'code' => 404, 'message' => 'User not found'], 404);
            }

            
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 'error', 'code' => 401, 'message' => 'Token is invalid'], 401);
        } catch (TokenExpiredException $e) {
            try {
                $token = JWTAuth::parseToken()->refresh();
                auth()->onceUsingId($token->getClaim('sub'));
            } catch (JWTException $e) {
                return response()->json(['status' => 'error', 'code' => 401, 'message' => 'JWTException: Unable to refresh token'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'code' => 401, 'message'  => 'JWTException: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
