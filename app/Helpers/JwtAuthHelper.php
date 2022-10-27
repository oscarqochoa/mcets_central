<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Illuminate\Support\Facades\DB;

class JwtAuthHelper
{

    private $key;
    private $hash;

    public function __construct()
    {
        $this->key = env('APP_KEY', 'testkey');
        $this->hash = "HS256";
    }

    public function signup(string $username, string $password, $getIdentity = null)
    {

        $user = DB::table('users')
            ->where('username', '=', $username)
            ->where('password', '=', $password)
            ->where('status', '=', true)
            ->first();

        $signup = false;

        if ($user) {
            $signup = true;
        }

        if ($signup) {

            $token = array(
                "sub" => $user->id,
                "fullname" => $user->fullname,
                "user_type" => $user->user_type,
                'iat' => time(),
                'exp' => time() + (180 * 400)
            );

            $jwt = JWT::encode($token, $this->key, $this->hash);
            $decoded = JWT::decode($jwt, new Key($this->key, $this->hash));

            if (is_null($getIdentity)) {
                $data = $jwt;
            } else {
                $decoded->access_token = $jwt;
                $data = $decoded;
            }
        } else {

            $data = array(
                "status" => "error",
                "message" => "Incorrect Login"
            );
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {

        $auth = false;

        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, new Key($this->key, $this->hash));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }

    public static function checkUser($jwt, $getIdentity = true)
    {
        $auth = false;

        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, new Key(self::$key, self::$hash));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }
}
