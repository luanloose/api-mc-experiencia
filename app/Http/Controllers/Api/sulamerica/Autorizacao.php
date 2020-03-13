<?php

namespace App\Http\Controllers\Api\sulamerica;

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

    const CONVENIO = 'https://saude.sulamericaseguros.com.br/prestador/',
        AUTORIZACAO = self::CONVENIO . '/Autorizacao/EnvioPreAutorizacao/0',
        SOLICITAR = self::CONVENIO . 'segurado/validacao-de-procedimentos-tiss-3/validacao-de-procedimentos/solicitacao/';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\sulamerica\\');
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
                $request->covenant['credentials']['user'],
                $request->covenant['credentials']['password']
            );

            //por enquanto tem apenas pre autorização (solicitação)
            $this->driver->get(self::SOLICITAR);

            $this->preencherSolicitante(
                $request->patient['card_number'],
                $request->covenant['credentials']['cnpj'],
                $request->medic_requester['name'],
                $request->medic_requester['type_council'],
                $request->medic_requester['region'],
                $request->medic_requester['number_council'],
                $request->medic_requester['cbo']
            );

            $this->preencherAtendimento(
                $request->date,
                $request->patient['newborn'],
                $request->service_character,
                $request->used_technique,
                $request->procedures
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

    private function logar($code, $user, $senha)
    {
        //preenche o campo email e realiza login
        $this->driver->findElement(By::id('code'))
            ->sendKeys($code);

        $this->driver->findElement(By::id('user'))
            ->sendKeys($user);

        $this->driver->findElement(By::id('senha'))
            ->sendKeys($senha);

        $this->driver->findElement(By::id('entrarLogin'))
            ->click();
    }

    private function preencherSolicitante(
        $carteirinha,
        $cnpj,
        $solicitante,
        $tipoConselho,
        $estado,
        $crm,
        $cbo
    ) {
        //colocado 3 zeros confirme vídeo, dps verificar
        $this->driver->findElement(By::id('codigo-beneficiario-1'))
            ->sendKeys('000' . $carteirinha);

        $this->driver->findElement(
            By::cssSelector('a.sasBtn.sasbt1.sasbtsmall.sas-form-submit')
        )->click();

        $this->driver->findElement(By::id('btn-eletivo'))
            ->click();

        $element = $this->driver->findElement(By::id('btn-sp-sadt'));

        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );

        $element->click();

        $this->driver->findElement(By::id('solicitacao-sp-sadt.numero-guia-prestador'))
            ->sendKeys($carteirinha);

        $select = new Select($this->driver->findElement(
            By::cssSelector(
                'select[name*="solicitacao-sp-sadt.executante-solicitante.tipo-documento.codigo"]'
            )
        ));
        $select->selectByVisibleText('CNPJ');

        $this->driver->findElement(By::id('numero-documento'))
            ->sendKeys($cnpj);

        $this->driver->findElement(
            By::cssSelector(
                'input[name*="solicitacao-sp-sadt.executante-solicitante.nome-profissional-solicitante"]'
            )
        )->sendKeys($solicitante);

        $selectConselho = new Select($this->driver->findElement(By::id('conselho-profissional')));
        $selectConselho->selectByVisibleText($tipoConselho);

        $selectEstado = new Select($this->driver->findElement(By::id('uf-conselho-profissional')));
        $selectEstado->selectByVisibleText($estado);

        $this->driver->findElement(By::id('numero-solicitante-no-conselho-profissional '))
            ->sendKeys($crm);

        $this->driver->findElement(By::id('busca-codigo-cbo'))
            ->sendKeys($cbo);

        $element = $this->driver->findElement(
            By::id(
                'ui-id-1'
            )
        );

        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );

        $this->driver->findElement(
            By::cssSelector(
                'a.ui-corner-all'
            )
        )->click();
    }

    private function preencherAtendimento(
        $dataAtendimento,
        $recemNato,
        $caraterAtend,
        $tecnica,
        $procedures
    ) {

        $data = $this->driver->findElement(By::id('data-atendimento'))
            ->click();

        $data->sendKeys($dataAtendimento);

        $select = new Select($this->driver->findElement(By::id('recem-nato')));

        if ($recemNato == 'true') {

            $recemNato = 'Sim';
        } else {

            $recemNato = 'Não';
        }

        $select->selectByVisibleText($recemNato);

        $select = new Select($this->driver->findElement(By::id('carater-atendimento')));

        if ($caraterAtend == 'ELT') {

            $caraterAtend = 'Eletivo';
        } else {

            $caraterAtend = 'Urgência/Emergência';
        }

        $select->selectByVisibleText($caraterAtend);

        $select = new Select($this->driver->findElement(
            By::cssSelector(
                'select[name*="solicitacao-sp-sadt.atendimento.tecnica-utilizada.codigo"]'
            )
        ));

        $select->selectByVisibleText($tecnica);

        foreach ($procedures as $elemento) {
            $this->driver->findElement(
                By::cssSelector(
                    'input[name*="codigo-procedimento"]'
                )
            )->sendKeys($elemento['tuss']);

            $addExame = $this->driver->findElement(
                By::id('btn-incluir-procedimento')
            );
            $addExame->click();

            $element = $this->driver->findElement(By::id('tabelaSolicitaProcedimento'));
            $this->driver->wait(10, 1000)->until(
                Condition::visibilityOf($element)
            );

            $linha = $this->driver->findElement(
                By::cssSelector(
                    'tr[data-codigo*="' . $elemento['tuss'] . '"]'
                )
            );

            $linha->findElement(
                By::cssSelector(
                    'input[name*="quantidade-solicitada"]'
                )
            )->sendKeys($elemento['amount']);
        }
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
        $this->situação = $this->driver->findElement(
            By::cssSelector('label:nth-child(2)')
        )->getText();

        if ($this->situação == "Autorizado") {

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
