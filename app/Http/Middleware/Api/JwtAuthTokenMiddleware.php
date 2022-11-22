<?php

namespace App\Http\Middleware\Api;

use Closure;
use Exception;
use App\Token\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use App\Http\Middleware\Api\ValidateHeaderTrait;

class JwtAuthTokenMiddleware
{
    use ValidateHeaderTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = $this->validateHeaderAuthorization($request);
            
            $decodedToken = Token::decode($token);
            $uuid = $decodedToken->data;
            
            $user = User::where('uuid', $uuid)->first();
            if ($user) {
                $request->merge([
                    'user_id' => $user->id,
                ]);
                return $next($request);
            }

            return response()->json([
                'message' => 'Invalid access token!'
            ], 400);

        } catch(ExpiredException $e) {
            try {
                $refreshedToken = Token::refreshedToken($token);            
                return response()->json([
                    'access_token' => $refreshedToken
                ], 401);
                
            } catch (ExpiredException $e) { // Token could not be refreshed
                return response()->json([
                    'message' => 'Token could not be refreshed. Please request a new one',
                    'access_token' => null,
                ], 403);
            }
        } catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
