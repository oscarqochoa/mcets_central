<?php

namespace App\Helpers;

use Exception;

class PaginateHelper
{

    public static function build(array $data, int $perPage): object
    {
        try {

            if (empty($data)) {
                return (object) array();
            }

            if (!property_exists($data[0], "from_page")) {
                throw new Exception("from_page property doesn't exists", HttpCodesHelper::FORBIDDEN);
            }

            if (!property_exists($data[0], "to_page")) {
                throw new Exception("to_page property doesn't exists", HttpCodesHelper::FORBIDDEN);
            }

            if (!property_exists($data[0], "cc")) {
                throw new Exception("cc property doesn't exists", HttpCodesHelper::FORBIDDEN);
            }

            $validate = [
                "from" => $data[0]->from_page,
                "to" => $data[0]->to_page,
                "per_page" => $perPage,
                "total" => $data[0]->cc,
                "data" => $data
            ];

            return response()->json($validate, HttpCodesHelper::OK);

        } catch (Exception $ex) {
            return ExceptionHelper::show($ex);
        }
    }

}
