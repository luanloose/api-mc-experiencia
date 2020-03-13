<?php


namespace App\API;

use Facebook\WebDriver\WebDriverBy as By;
use Exception;

class ApiError
{
    protected $path;

    public function __construct()
    {
        $this->path = base_path('storage\\app\\screenshots\\');
    }

    public static function errorMessage($message, $code)
    {
        return [
            'error' => $message,
            'extra' => "",
            'ans' => "",
            'id' => $code
        ];
    }

    public function getErroUnimedRio($browser, Exception $e)
    {

        $erroBusca = $browser->findElements(By::id('msgErro'));
        $erroEnviar = $browser->findElements(By::cssSelector('ul.validation-summary-errors'));

        $data = date('H_i_s');

        $browser->takeScreenshot($this->path . 'unimedrio\\' . 'Erro-UnimedRio' . $data . '.png');

        if (count($erroBusca)) {

            $msgErro = $browser->findElement(By::id('msgErro'))->getText();
            $browser->quit();
            return response()->json($this->errorMessage($msgErro, 1010), 500);
        } else if (count($erroEnviar)) {

            $msgErro = $browser->findElement(By::cssSelector('ul.validation-summary-errors'))->getText();
            $browser->quit();
            return response()->json($this->errorMessage($msgErro, 1010), 500);
        }
        $browser->quit();
        return response()->json($this->errorMessage($e->getMessage(), 1010), 500);
    }

    public function getErroBradesco($browser, Exception $e)
    {
        //capturado apenas erro de login
        $erroBusca = $browser->findElements(By::id('classeErro'));
        $erroEnviar = $browser->findElements(By::cssSelector('ul.validation-summary-errors'));

        $data = date('H_i_s');

        $browser->takeScreenshot($this->path . 'bradesco\\' . 'Erro-Bradesco' . $data . '.png');

        if (count($erroBusca)) {

            $msgErro = $browser->findElement(By::id('classeErro'))->getText();
            $browser->quit();
            return response()->json($this->errorMessage($msgErro, 1010), 500);
        } else if (count($erroEnviar)) {

            $msgErro = $browser->findElement(By::cssSelector('ul.validation-summary-errors'))->getText();
            $browser->quit();
            return response()->json($this->errorMessage($msgErro, 1010), 500);
        }
        
        $browser->quit();
        return response()->json($this->errorMessage($e->getMessage(), 1010), 500);
    }


    public function getErroSulAmerica($browser, Exception $e)
    {
        //nenhum erro tratato ainda nesse convenio ainda
        $erroBusca = $browser->findElements(By::id('classeErro'));
        $erroEnviar = $browser->findElements(By::cssSelector('ul.validation-summary-errors'));

        $data = date('H_i_s');

        $browser->takeScreenshot($this->path . 'sulamerica\\' . 'Erro-SulAmerica' . $data . '.png');

        if (count($erroBusca)) {

            $msgErro = $browser->findElement(By::id('classeErro'))->getText();
            $browser->quit();
            return response()->json($this->errorMessage($msgErro, 1010), 500);
        } else if (count($erroEnviar)) {

            $msgErro = $browser->findElement(By::cssSelector('ul.validation-summary-errors'))->getText();
            $browser->quit();
            return response()->json($this->errorMessage($msgErro, 1010), 500);
        }
        
        $browser->quit();
        return response()->json($this->errorMessage($e->getMessage(), 1010), 500);
    }
}
