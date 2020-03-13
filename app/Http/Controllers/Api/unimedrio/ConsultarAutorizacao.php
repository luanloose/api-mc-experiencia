<?php

namespace App\Http\Controllers\Api\unimedrio;

use App\API\Browser;
use App\API\ApiError as Erro;
use App\API\Retorno;
use Facebook\WebDriver\WebDriverBy as By;
use Illuminate\Http\Request;

class ConsultarAutorizacao extends Browser
{
    private $numeroGuia, $situação, $status, $driver, $senha, $dataValSenha, $procedimentos, $nomePaciente;
    protected $path;

    const CONVENIO = 'https://producaoonline.unimedrio.com.br/prestador',
        CONSULTAR = self::CONVENIO . '/Autorizacao/EnvioConsultaPreAutorizacao';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\unimedrio\\');
        $this->driver = $this->browser();
    }

    /**
     * @method  consultar -> Método para preencher todos os campos para consulta
     * @param   Request $request -> recebe json via post
     * @see     App\API\Browser
     * @return  response -> Json com dados referente a guia ou erro
     */
    public function consultar(Request $request)
    {
        try {
            $this->driver->get(self::CONVENIO);

            $this->logar(
                $request->covenant['credentials']['user'],
                $request->covenant['credentials']['password']
            );

            //Vai direto para a pagina de consulta
            $this->driver->get(self::CONSULTAR);

            $this->buscarGuia($request->guide_number);

            //preenche os atributos da classe para retornar no Json
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
            return $erro->getErroUnimedRio($this->driver, $e);
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
        $this->driver->findElement(By::id('numeroGuiaPrestador'))
            ->sendKeys($guia);

        $buscar = $this->driver->findElement(
            By::cssSelector('input[type="image"]')
        );
        $buscar->click();
    }

    private function retorno()
    {
        $this->situação = $this->driver->findElement(
            By::cssSelector('label:nth-child(2)')
        )->getText();

        if ($this->situação == "Aprovada") {

            $this->status = "true";
        } else {

            $this->status = "false";
        }

        $guia = $this->driver->findElements(
            By::cssSelector('td.titulo')
        );

        $this->numeroGuia = $guia[2]->getText();

        $this->nomePaciente = $this->driver->findElement(
            By::cssSelector('td.conteudo[style*="width: 92.50mm; height: 6.35mm;"')
        )->getText();

        $this->dataValSenha = $this->driver->findElement(
            By::cssSelector('td.conteudo[style*="width: 47.50mm; height: 6.35mm;"')
        )->getText();

        $procedimentos = $this->driver->findElements(
            By::cssSelector('td.conteudo_min')
        );

        $cont = (count($procedimentos) - 1) / 7;

        $l = 0; //numero para encontrar os elementos dinamicamente
        $this->procedimentos = [];

        for ($i = 1; $i <= $cont; $i++) {

            array_push($this->procedimentos, [
                "tuss" => $procedimentos[1 + $l]->getText(),
                "desc" => $procedimentos[2 + $l]->getText(),
                "amount" => $procedimentos[3 + $l]->getText(),
                "authorized_amount" => $procedimentos[4 + $l]->getText()
            ]);

            $l += 7;
        }

        $this->senha = $this->driver->findElement(
            By::cssSelector('td[style*="width: 25.40mm; height: 6.35mm;"')
        )->getText();
    }

    private function kill()
    {

        $this->driver->quit();
        
    }
}
