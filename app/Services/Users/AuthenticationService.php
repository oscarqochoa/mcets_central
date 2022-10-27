<?php

namespace App\Services\Users;

use App\Helpers\ExceptionHelper;
use App\Helpers\JwtAuthHelper;
use TheSeer\Tokenizer\Exception;

class AuthenticationService
{

    public function signIn(
        string $username,
        string $password,
        bool $getData = null
    )
    {
        try {

            $jwtAuth = new JwtAuthHelper();

            $pwd = hash("sha256", $password);

            $signup = $jwtAuth->signup($username, $pwd);

            if (!empty($getData) && $getData) {
                $signup = $jwtAuth->signup($username, $pwd, true);
            }

            return $signup;
        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

}
