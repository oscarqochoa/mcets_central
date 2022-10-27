<?php

namespace App\Http\Controllers\Users;

use App\Helpers\ExceptionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Exception;

use App\Helpers\HttpCodesHelper;
use App\Services\Users\AuthenticationService;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        try {

            $body = $request->all();

            $validate = Validator::make($body, [
                "username" => "required",
                "password" => "required",
                "get_identity" => "required|boolean"
            ]);

            if ($validate->fails()) {
                throw new Exception($validate->errors(), HttpCodesHelper::BAD_REQUEST);
            }

            $authenticationService = new AuthenticationService();

            $response = $authenticationService->signIn(
                $body["username"],
                $body["password"],
                $body["get_identity"]
            );

            return response()->json($response);

        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }
}
