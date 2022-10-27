<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use App\Helpers\ExceptionHelper;
use App\Helpers\HttpCodesHelper;
use App\Helpers\JwtAuthHelper;
use App\Helpers\ResponseMessagesHelper;
use Closure;
use Exception;

class CustomAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            $token = $request->header("Authorization");

            if (!$token) {
                throw new Exception(ResponseMessagesHelper::NO_AUTHENTICATION_HEADER, HttpCodesHelper::FORBIDDEN);
            }

            $jwtAuth = new JwtAuthHelper();
            $checkToken = $jwtAuth->checkToken($token, true);

            if (!$checkToken) {
                throw new Exception(ResponseMessagesHelper::UNIDENTIFIED_USER, HttpCodesHelper::UNAUTHORIZED);
            }

            $request->setAuthUser($checkToken->sub);
            return $next($request);

        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }

    }
}
