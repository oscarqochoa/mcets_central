<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\DB;

use App\Helpers\ExceptionHelper;
use App\Helpers\HttpCodesHelper;
use App\Helpers\ResponseMessagesHelper;

use App\Models\User;
use Exception;

class UsersService
{

    public function find(
        $id,
        $fullname,
        $email,
        $userType,
        $perPage,
        $page
    ): array
    {
        try {

            $statement = "CALL sp_get_users(?,?,?,?,?,?)";
            $parameters = [$id, $fullname, $email, $userType, $perPage, $page];

            $users = DB::select($statement, $parameters);

            print_r($users);
            exit();

            return $users;

        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

    public function findOne(int $id)
    {



    }

    public function saveUser(
        string $fullname,
        string $email,
        string $userType,
        string $username,
        string $password,
        int $userRecord
    ): object
    {
        try {
            $pwd = hash("sha256", $password);

            $User = new User();
            $User->fullname = $fullname;
            $User->email = $email;
            $User->user_type = $userType;
            $User->username = $username;
            $User->password = $pwd;
            $User->created_by = $userRecord;

            $response = $User->save();

            if (!$response) {
                throw new Exception(
                    ResponseMessagesHelper::NOT_REGISTERED,
                    HttpCodesHelper::INTERNAL_SERVER_ERROR
                    );
            }

            return (object) array(
                "id" => $User->id
            );

        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }

    }

    public function updateUser(
        int $id,
        string $fullname,
        string $email,
        string $userType
    )
    {
        try {

            $response = DB::table("users")
                ->where("id", $id)
                ->update([
                    "fullname" => $fullname,
                    "email" => $email,
                    "user_type" => $userType,
                ]);

            if (!$response) {
                throw new Exception(
                    ResponseMessagesHelper::NOT_UPDATED,
                    HttpCodesHelper::INTERNAL_SERVER_ERROR
                    );
            }
        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

    public function deleteUser(
        int $id,
        int $userRecord
    )
    {
        try {

            $response = DB::table("users")
                ->where("id", $id)
                ->update([
                    "status" => false,
                    "deleted_by" => $userRecord
                ]);

            if (!$response) {
                throw new Exception(
                    ResponseMessagesHelper::NOT_UPDATED,
                    HttpCodesHelper::INTERNAL_SERVER_ERROR
                    );
            }
        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

}
