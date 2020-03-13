<?php

namespace App\Http\Controllers\Api\saudecaixa;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use App\API\Retorno;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;

class Autorizacao extends Browser
{
    private $numeroGuia, $situação, $status, $driver, $senha, $dataValSenha, $procedimentos, $nomePaciente;
    protected $path;

    const CONVENIO = '',
        CONSULTAR = '',
        AUTORIZACAO = self::CONVENIO . '';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\saudecaixa\\');
        $this->driver = $this->browser();
    }

    public function autorizar(Request $request)
    {
        try {

            $this->driver->get(self::CONVENIO);

            $this->logar(
                $request->covenant['credentials']['user'],
                $request->covenant['credentials']['password']
            );

            $this->retorno();

            $this->kill();

            return response()->json(Retorno::retorno(
                $this->senha,
                $this->status,
                $this->situação,
                $this->numeroGuia,
                $this->dataValSenha,
                $this->nomePaciente,
                $this->procedimentos
            ), 200);

        } catch (\Exception $e) {

            $erro = new Erro;
            return $erro->getErroSulAmerica($this->driver, $e);
        }
    }

    private function logar($code, $senha)
    {
        //preenche o campo email e realiza login
    }

    private function retorno()
    {
        
    }

    private function kill()
    {
        $this->driver->quit();
    }
}
