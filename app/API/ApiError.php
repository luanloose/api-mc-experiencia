<?php


namespace App\API;

use Exception;

class ApiError
{

    public static function errorMessage($message, $code)
    {
        return [
            'error' => $message,
            'extra' => "",
            'ans' => "",
            'id' => $code
        ];
    }

    public function getError($browser, Exception $e)
    {

        $browser->quit();
        return response()->json($this->errorMessage($e->getMessage(), 1010), 500);
    }

}
