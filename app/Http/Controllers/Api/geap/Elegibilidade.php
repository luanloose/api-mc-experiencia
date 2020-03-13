<?php

namespace App\Http\Controllers\Api\geap;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use App\API\Retorno;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;

class Elegibilidade extends Browser
{
    private $id, $status, $driver;
    protected $path;

    const CONVENIO = 'https://www2.geap.com.br/auth/prestador.asp';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\geap\\');
        $this->driver = $this->browser();
    }

    /**
     * @method  consultar -> Método para preencher campos e verificar elegibilidade
     * @param   Request $request -> recebe json via post
     * @see     App\API\Browser
     * @return  response -> Json com dados referente a carteirinha
     */
    public function consultar(Request $request)
    {
        try {

            $this->driver->get(self::CONVENIO);

            $this->logar(
                $request->covenant['credentials']['code'],
                $request->covenant['credentials']['password']
            );

            //902002364260050
            $this->consultarCarencia($request->patient['card_number']);

            $this->retorno();

            $this->kill();

            return response()->json(Retorno::retornoElegibilidade(
                $this->status,
                $this->id,
                $request->patient['name']

            ), 200);
        } catch (\Exception $e) {

            $erro = new Erro;
            return $erro->getErroSulAmerica($this->driver, $e);
        }
    }

    private function logar($code, $senha)
    {
        //preenche o campo email e realiza login
        $this->driver->findElement(By::id('login_code'))
            ->sendKeys($code);

        $this->driver->findElement(By::id('login_password'))
            ->sendKeys($senha);

        $this->driver->findElement(By::id('btnLogin'))
            ->click();
    }

    public function consultarCarencia($carteirinha)
    {   

        sleep(2);

        $this->driver->findElement(
            By::cssSelector('a[href*="/PRESTADOR/consultar-beneficiario.asp"]')
        )->click();

        $this->driver->findElement(By::id('NroCarteira'))
            ->sendKeys($carteirinha);

        $this->driver->findElement(By::id('btn-submit-carteira'))
            ->click();
    }

    private function retorno()
    {
        $ativo = $this->driver->findElement(By::cssSelector("i[class='icon-like']"));

        if ($ativo) {

            $this->status = "true";
        } else {

            $this->status = "false";
        }
    }

    private function kill()
    {
        $this->driver->quit();
    }
}
