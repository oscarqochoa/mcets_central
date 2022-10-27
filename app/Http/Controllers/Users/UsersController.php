<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Exception;

use App\Helpers\ExceptionHelper;
use App\Helpers\HttpCodesHelper;
use App\Helpers\PaginateHelper;
use App\Helpers\ResponseMessagesHelper;
use App\Services\Users\UsersService;

class UsersController extends Controller
{

    public function getUsers(Request $request)
    {
        try {

            $validate = Validator::make($request->input(), [
                "per_page" => "required",
                "page" => "required"
            ]);

            if ($validate->fails()) {
                throw new Exception($validate->errors(), HttpCodesHelper::BAD_REQUEST);
            }

            $usersService = new UsersService();

            $response = $usersService->find(
                $request->input("id"),
                $request->input("fullname"),
                $request->input("email"),
                $request->input("user_type"),
                $request->input("per_page"),
                $request->input("page")
            );

            return PaginateHelper::build($response, $request->input("per_page"));
        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

    public function getUser()
    {
    }

    public function register(Request $request)
    {
        try {

            $body = $request->all();

            $validate = Validator::make($body, [
                "fullname" => "required",
                "email" => "required|unique:users,email",
                "user_type" => "required",
                "username" => "required|min:6",
                "password" => "required|min:8"
            ]);

            if ($validate->fails()) {
                throw new Exception($validate->errors(), HttpCodesHelper::BAD_REQUEST);
            }

            $usersService = new UsersService();

            $response = $usersService->saveUser(
                $body["fullname"],
                $body["email"],
                $body["user_type"],
                $body["username"],
                $body["password"],
                $request->getAuthUser()
            );

            return response()->json([
                "message" => ResponseMessagesHelper::REGISTERED,
                "id" => $response->id
            ], HttpCodesHelper::CREATED);

        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            if ($id == null || $id == 0) {
                throw new Exception(ResponseMessagesHelper::INVALID_PARAMETER, HttpCodesHelper::BAD_REQUEST);
            }

            $body = $request->all();

            $validate = Validator::make($body, [
                "fullname" => "required",
                "email" => "required",
                "user_type" => "required"
            ]);

            if ($validate->fails()) {
                throw new Exception($validate->errors(), HttpCodesHelper::BAD_REQUEST);
            }

            $usersService = new UsersService();

            $usersService->updateUser(
                $id,
                $body["fullname"],
                $body["email"],
                $body["user_type"]
            );

            return response()->json([
                "message" => ResponseMessagesHelper::UPDATED,
                "id" => $id
            ], HttpCodesHelper::OK);
        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            if ($id == null || $id == 0) {
                throw new Exception(ResponseMessagesHelper::INVALID_PARAMETER, HttpCodesHelper::BAD_REQUEST);
            }

            $usersService = new UsersService();

            $usersService->deleteUser(
                $id,
                $request->getAuthUser()
            );

            return response()->json([
                "message" => ResponseMessagesHelper::DELETED,
                "id" => $id
            ], HttpCodesHelper::OK);
        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

}
