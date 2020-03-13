<?php

namespace App\Http\Controllers\Api\unimedrio;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use App\API\Retorno;


class Autorizacao extends Browser
{
    private $numeroGuia, $situação, $status, $driver, $senha, $dataValSenha, $procedimentos, $nomePaciente;
    protected $path;

    const CONVENIO = 'https://producaoonline.unimedrio.com.br/prestador',
        AUTORIZACAO = self::CONVENIO . '/Autorizacao/EnvioPreAutorizacao/0',
        SOLICITAR = self::CONVENIO . '/Autorizacao/EnvioSadt/0';

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
    public function autorizar(Request $request)
    {
        try {

            $this->driver->get(self::CONVENIO);

            $this->logar(
                $request->covenant['credentials']['user'],
                $request->covenant['credentials']['password']
            );

            //Vai direto para a pagina de autorização ou procedimento
            if ($request->procedure_type == 'Execucao') {
                $this->driver->get(self::SOLICITAR);
            } else {
                $this->driver->get(self::AUTORIZACAO);
            }

            $this->preencherGuia(
                $request->patient['card_number'],
                $request->patient['name'], //usado apenas em procedimento
                $request->patient['newborn'],
                $request->provider_requester['cnes'],
                $request->procedure_type
            );

            $this->preencherSolicitante(
                $request->medic_requester['number_council'],
                $request->medic_requester['name'],
                $request->medic_requester['cnes'],
                $request->medic_requester['type_council'],
                $request->medic_requester['region']
            );

            sleep(1);

            $this->preencherAtendimento(
                $request->clinical_indication,
                $request->service_character, //usado apenas em procedimento
                $request->type_service,
                $request->procedures,
                $request->procedure_type
            );

            $this->enviarForm();

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

    private function preencherGuia($carteirinha, $nome, $recemNato, $cnesExec, $service)
    {
        //Beneficiario
        if ($service == 'Execucao') {
            $this->driver->executeScript('document.getElementById("numeroCarteira").value = "' . $carteirinha . '";');
            $this->driver->executeScript('document.getElementById("nomeBeneficiario").value = "' . $nome . '";');
        } else {
            $this->driver->findElement(By::id('numeroCarteira'))
                ->sendKeys($carteirinha);
        }

        $select = new Select($this->driver->findElement(By::id('recemNato')));

        if ($recemNato == 'true') {

            $recemNato = 'Sim';
        } else {

            $recemNato = 'Não';
        }

        $select->selectByVisibleText($recemNato);

        //executante
        $this->driver->findElement(By::id('numeroCNESExe'))
            ->sendKeys($cnesExec);
    }

    private function preencherSolicitante($crm, $solicitante, $cnesPro, $conselho, $estado)
    {

        $solicitanteCheck = 0; //flag para saber se solitante é o mesmo que executante

        //solicitante
        if ($solicitanteCheck == 1) {
            $marcar = $this->driver->findElement(
                By::id('checkSolicitante')
            );
            $marcar->click();
        } else {
            $this->driver->findElement(By::id('numeroConsProfissionalSolic'))
                ->sendKeys($crm);

            $verificaNome = $this->driver->findElement(By::id('nomeProfissionalSolic'))->getText();

            if ($verificaNome == '') {

                $this->driver->executeScript('document.getElementById("nomeProfissionalSolic").value = "' . $solicitante . '";');

                $this->driver->findElement(By::id('cnesContratadoSolic'))
                    ->sendKeys($cnesPro);
            }

            $selectConselho = new Select($this->driver->findElement(By::id('DDLconselhoProfissionalSolic')));
            $selectConselho->selectByVisibleText($conselho);

            $selectEstado = new Select($this->driver->findElement(By::id('DDLufConsProfissionalSolic')));
            $selectEstado->selectByVisibleText($estado);

            $this->driver->findElement(By::id('nomeContratadoSolic'))
                ->sendKeys($solicitante);
        }
    }

    private function preencherAtendimento($indicacaoClin, $caraterSoli, $tipoAtend, $procedures, $service)
    {
        $this->driver->findElement(By::id('indicacaoClinica'))
            ->sendKeys($indicacaoClin);

        //caraterSolicitacao
        if ($service == 'Execucao') {
            $selectAtend = new Select($this->driver->findElement(By::id('caraterSolicitacao')));

            if ($caraterSoli == 'ELT') {

                $caraterSoli = '1 - Eletivo';
            } else {

                $caraterSoli = '2 - Urgência/Emergência';
            }

            $selectAtend->selectByVisibleText($caraterSoli);

            $tagTipoAtend = 'tipoAtendimento';

            //cid
            //codigo
        } else {

            $tagTipoAtend = 'tpAtendimentos';
        }

        $select = new Select($this->driver->findElement(By::id($tagTipoAtend)));

        if ($tipoAtend == 'SADT') {
            $tipoAtend = 'Exames';
        }

        $select->selectByVisibleText($tipoAtend);

        sleep(1);

        foreach ($procedures as $elemento) {
            $this->driver->findElement(By::id('procedimento'))
                ->sendKeys($elemento['tuss']);

            $this->driver->executeScript('document.getElementById("solicitadosProc").value = "' . $elemento['amount'] . '";');

            sleep(1);

            $addExame = $this->driver->findElement(
                By::id('button')
            );
            $addExame->click();
        }
    }

    private function enviarForm()
    {

        $enviarForm = $this->driver->findElement(
            By::cssSelector('input.enviar')
        );
        $enviarForm->click();

        $this->driver->findElement(By::id('spanMsg'))->isDisplayed();

        $confirma = $this->driver->findElements(
            By::cssSelector('button.ui-state-default.ui-corner-all')
        );

        foreach ($confirma as $elemento) {
            $texto = $elemento->getText();

            if ($texto == 'Confirma') {

                $elemento->click();
                break;
            }
        }
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
