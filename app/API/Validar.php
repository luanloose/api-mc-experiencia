<?php


namespace App\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Validar
{

    public static function testar(Request $request, $rules, RemoteWebDriver $driver = null)
    {
        $data = json_encode($request->all());
        $data = json_decode($data, true);

        $validator = Validator::make($data, $rules, config('messagesValidation'));

        if ($validator->fails()) {
            $msgs = $validator->errors()->all();

            $msg = "";
            foreach ($msgs as $m) {
                $msg = $msg ." ". $m; 
            }
            
            if($driver){

                $driver->quit();
            }

            return $msg;
        }

        return null;
    }
}
