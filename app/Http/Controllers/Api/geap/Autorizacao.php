<?php

namespace App\Http\Controllers\Api\geap;

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

    const CONVENIO = 'https://www2.geap.com.br/auth/prestador.asp',
        AUTORIZACAO = 'https://www2.geap.com.br/regulacaoTiss/solicitacoes/SolicitacaoSADT.aspx';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\geap\\');
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
                $request->covenant['credentials']['code'],
                $request->covenant['credentials']['password']
            );

            $this->acessarTiss();

            $this->driver->get(self::AUTORIZACAO);

            $this->preencherAtendimento(
                $request->patient['card_number'],
                $request->patient['newborn'],
                $request->service_character
            );

            $this->preencherSolicitante(
                $request->covenant['credentials']['code'],
                $request->medic_requester['name'],
                $request->medic_requester['type_council'],
                $request->medic_requester['region'],
                $request->medic_requester['number_council'],
                $request->medic_requester['cbo']
            );

            $this->enviarForm($request->clinical_indication);

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
        $this->driver->findElement(By::id('login_code'))
            ->sendKeys($code);

        $this->driver->findElement(By::id('login_password'))
            ->sendKeys($senha);

        $this->driver->findElement(By::id('btnLogin'))
            ->click();
    }

    public function acessarTiss()
    {
        sleep(2);

        $this->driver->findElement(
            By::cssSelector('a[href*="/PRESTADOR/portal-tiss.asp"]')
        )->click();

        $this->driver->executeScript('window.open("RedirectRegulacaoTiss.asp","_self","");');
    }

    private function preencherAtendimento(
        $carteirinha,
        $recemNato,
        $caraterAtend
    ) {

        $this->driver->findElement(By::id('MenuOptionNew'))
            ->click();

        $selectPacInt = new Select($this->driver->findElement(
            By::id('TabContainerControl1_TabGeral_StaPacienteInternado')
        ));
        $selectPacInt->selectByVisibleText('NAO');

        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_NroCartao'))
            ->sendKeys($carteirinha);

        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_btnFindSolicitante'))
            ->click();

        $selectRecemNat = new Select($this->driver->findElement(
            By::id('TabContainerControl1_TabGeral_CodAtendimentoRN')
        ));

        //parei aqui

        if ($recemNato == 'true') {

            $recemNato = 'Sim';
        } else {

            $recemNato = 'Não';
        }

        $selectRecemNat->selectByVisibleText($recemNato);

        $selectAtend = new Select($this->driver->findElement(By::id('TabContainerControl1_TabGeral_NroCaraterAtendimento')));

        if ($caraterAtend == 'ELT') {

            $caraterAtend = 'Eletivo';
        } else {

            $caraterAtend = 'Urgência/Emergência';
        }
        $selectAtend->selectByVisibleText($caraterAtend);

        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_DesIndicacaoClinica'))
            ->sendKeys($carteirinha);
    }

    private function preencherSolicitante(
        $codSolic,
        $solicitante,
        $tipoConselho,
        $estado,
        $crm,
        $cbo
    ) {
        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_NroContratadoCPFCNPJPrestadorSolicitante'))
            ->sendKeys($codSolic);

        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_btnFindSolicitante'))
            ->click();

        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_NmeProfissionalSolicitante'))
            ->sendKeys($solicitante);

        $this->driver->findElement(By::id('TabContainerControl1_TabGeral_NroConselhoProfissionalSolicitante'))
            ->sendKeys($crm);

        $selectPacInt = new Select($this->driver->findElement(
            By::id('TabContainerControl1_TabGeral_CodConselhoProfissionalSolicitante')
        ));
        $selectPacInt->selectByVisibleText($tipoConselho);

        $selectPacInt = new Select($this->driver->findElement(
            By::id('TabContainerControl1_TabGeral_NroUFConselhoProfissionalSolicitante')
        ));
        $selectPacInt->selectByVisibleText($estado);

        $selectPacInt = new Select($this->driver->findElement(
            By::id('TabContainerControl1_TabGeral_NroCBOProfissionalSolicitante')
        ));
        $selectPacInt->selectByVisibleText($cbo);
    }

    public function preencherProcedimentos($procedures)
    {
        # TabContainerControl1_TabProcedimento_btnAbaProcedimento
        foreach ($procedures as $elemento) {

            $this->driver->findElement(
                By::id('TabContainerControl1_TabProcedimento_btnAbaProcedimento')
            )->click();

            $this->driver->findElement(By::id('TabContainerControl1_TabProcedimento_NroServicoGridRegulacao'))
                ->sendKeys($elemento['tuss']);

            $this->driver->findElement(
                By::id('TabContainerControl1_TabProcedimento_btnQuickServicoProc')
            )->click();

            $this->driver->findElement(
                By::id('TabContainerControl1_TabProcedimento_QtdSolicitadaGridRegulacao')
            )->sendKeys($elemento['amount']);

            $this->driver->findElement(
                By::id('TabContainerControl1_TabProcedimento_btn_gridProcedimentosSolicitacaoSADT')
            )->click();
        }

        dd("123");
    }


    private function enviarForm($indicacaoClinica)
    {

        $this->driver->findElement(
            By::id('solicitacao-sp-sadt.descricao-indicacao-clinica')
        )->sendKeys($indicacaoClinica);

        //clicar para enviar

    }

    private function retorno()
    {
    }

    private function kill()
    {
        $this->driver->quit();
    }
}
