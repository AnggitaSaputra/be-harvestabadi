<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;


class CustomAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            $token = str_replace('Bearer ', '', $authHeader);
            $tokenInstance = PersonalAccessToken::findToken($token);

            if ($tokenInstance) {
                // Jika token valid, lanjutkan ke request berikutnya
                return $next($request);
            }
        }

        // Jika token tidak valid atau tidak ada, kembalikan respon unauthorized
        return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
    }
}
