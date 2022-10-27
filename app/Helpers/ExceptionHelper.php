<?php

namespace App\Helpers;

use Exception;

class ExceptionHelper
{

    public static function show(Exception $exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();

        $validateIsJson = str_contains($message, "{");

        if ($validateIsJson) {
            return response()->json(json_decode($message), $code);
        }

        return response($message, $code);
    }

}
