<?php

namespace App\API;

use Facebook\WebDriver\Remote\RemoteWebDriver as Browser;
use Exception;

class Errors
{
    protected $path;
    private $driver;
    private $exception;
    private $fileUrl;

    /**
     * @method  __construct -> 
     * @param   Browser $driver -> recebe driver para capturar os erros e poder encerrar a instancia
     * @param   Exception $exception -> recebe exception para ser utilizado no log
     * @param   array $request -> recebe o json da request para ser utilizado no log
     */
    public function __construct(Browser $driver, Exception $exception, array $request)
    {
        $this->path = base_path('storage\\app\\public\\screenshots\\');
        $this->exception = $exception;
        $this->driver = $driver;
    }

    /**
     * @method  errorResponse -> Método gera o response e log
     * @param   String $message -> nome do convenio para screenshot
     * @return  Response $errorObj -> ErrorResource
     */
    public function errorResponse(String $message)
    {
        $message = $message ? preg_replace("/\r|\n/", ": ", $message) : $this->exception->getMessage();

        $this->driver->quit();

        $errorObj = [
            'error' => $message
        ];

        return response($errorObj);
    }

    /**
     * @method  screenshot -> Método para capturar os erros de cada portal
     * @param   String $site -> nome do site para screenshot
     * @return  Response tratato na função errorResponse 
     */
    public function screenshot(String $site)
    {
        $folder = $this->path . $site;

        if (!file_exists($this->path)) {
            mkdir($this->path, 0700);
        }

        if (!file_exists($folder)) {
            mkdir($folder, 0700);
        }

        $this->fileUrl = $folder . '\\' . date('d-M-Y-H-i-s') . '.png';
        $this->driver->takeScreenshot($this->fileUrl);
    }

    /**
     * @method  getError -> Método para capturar os erros conhecidos de cada portal
     * @return  Response tratato na função errorResponse 
     */

    public function getError()
    {
        $this->screenshot('McDonalds');
        $errorMessage = '';
        return $this->errorResponse($errorMessage);
    }
}
