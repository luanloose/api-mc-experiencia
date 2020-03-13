<?php

namespace App\Http\Controllers\Api\unimedrio;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;
use Illuminate\Http\Request;
use App\API\Retorno;

class CancelarGuia extends Browser
{
    private $numeroGuia, $situação, $status, $driver, $nomePaciente;
    protected $path;

    const CONVENIO = 'https://producaoonline.unimedrio.com.br/prestador',
        CANCELAMENTO = self::CONVENIO . '/Autorizacao/EnvioCancelamento';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\unimedrio\\');
        $this->driver = $this->browser();
    }

    /**
     * @method  cancelarGuia -> Método para preencher campos e cancelar guia
     * @param   Request $request -> recebe json via post
     * @see     App\API\Browser
     * @return  response -> Json com confirmação do cancelamento ou erro
     */
    public function cancelarGuia(Request $request)
    {
        try {
            $this->driver->get(self::CONVENIO);

            $this->logar(
                $request->covenant['credentials']['user'],
                $request->covenant['credentials']['password']
            );

            //Vai direto para a pagina de cancelamento
            $this->driver->get(self::CANCELAMENTO);

            $this->buscarGuia($request->guide_number);

            $this->cancelaGuia();

            $this->retorno($request->guide_number, $request->patient['name']);

            $this->kill();

            return response()->json(Retorno::retorno(
                $this->status,
                $this->situação,
                $this->numeroGuia,
                $this->nomePaciente
            ), 200);

        } catch (\Exception $e) {

            $erro = new Erro;
            return $erro->getErroUnimedRio($this->driver,$e);

        }
    }

    private function logar($login, $senha)
    {
        //preenche o campo email e realiza login
        $this->driver->findElement(By::id('login'))
            ->sendKeys($login);

        $this->driver->findElement(By::id('pass'))
            ->sendKeys($senha);

        $entrar = $this->driver->findElement(
            By::cssSelector('input[type="image"]')
        );
        $entrar->click();
    }

    private function buscarGuia($guia)
    {
        //preenche o campo email e realiza login
        $this->driver->findElement(By::id('numeroGuiaPrestador'))
            ->sendKeys($guia);

        $buscar = $this->driver->findElement(
            By::cssSelector('input[type="image"]')
        );
        $buscar->click();

        $element = $this->driver->findElement(By::id('guiasTable'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );

        $detalhe = $this->driver->findElement(
            By::cssSelector('a[title="Ver detalhes"]')
        );

        $detalhe->click();
    }

    private function cancelaGuia()
    {

        $cancelar = $this->driver->findElement(
            By::cssSelector('img[alt*="Cancelar Guia"]')
        );
        $cancelar->click();

        $this->driver->findElement(By::id('msgConfirm'))->isDisplayed();

        $confirma = $this->driver->findElements(
            By::cssSelector('button.ui-state-default.ui-corner-all')
        );

        foreach ($confirma as $elemento) {
            $texto = $elemento->getText();

            if ($texto == 'Sim') {

                $elemento->click();
                break;
            }
        }
    }

    private function retorno($guia, $nome)
    {

        $this->situação = 'Guia Cancelada';
        $this->status = 'true';
        $this->numeroGuia = $guia;
        $this->nomePaciente = $nome;

    }

    private function kill()
    {
        $this->driver->quit();
    }
}
