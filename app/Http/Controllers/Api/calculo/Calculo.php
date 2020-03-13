<?php

namespace App\Http\Controllers\Api\calculo;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;


class Calculo extends Browser
{
    private $numeroGuia, $situação, $status, $driver, $senha, $dataValSenha, $procedimentos, $nomePaciente;
    protected $path;

    const CONVENIO = 'http://perinatology.com/calculators/Due-Date.htm';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\unimedrio\\');
        $this->driver = $this->browser();
    }

    /**
     * @method  Autorizacao -> Método para preencher campos e verificar guia
     * @param   Request $request -> recebe json via post
     * @see     App\API\Browser
     * @return  response -> Json com dados referente a guia ou erro
     */
    public function calcular(Request $request)
    {
        try {

            $this->driver->get(self::CONVENIO);

            $mes = new Select($this->driver->findElement(By::cssSelector('select[name="m"]')));
            $mes->selectByVisibleText('1 January');

            
            $dia = new Select($this->driver->findElement(By::cssSelector('select[name="d"]')));
            $dia->selectByVisibleText('1');

            
            $ano = new Select($this->driver->findElement(By::cssSelector('select[name="y"]')));
            $ano->selectByVisibleText('2020');

            $submit = $this->driver->findElement(
                By::cssSelector('input[value="Calculate"]')
            );
            $submit->click();

            $texto = $this->driver->findElement(
                By::id('dates')
            );

            return [
                "status" => $texto
            ];

        } catch (\Exception $e) {

            $erro = new Erro;
            return $erro->getErroUnimedRio($this->driver, $e);
        }
    }
}
