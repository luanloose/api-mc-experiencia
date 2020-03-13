<?php

namespace App\Http\Controllers\Api\geap;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use App\API\Retorno;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;


class Carencia extends Browser
{
    private $id, $status, $driver, $procedimentos, $locaisAtendimento;
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
    public function carencia(Request $request)
    {
        try {

            $this->driver->get(self::CONVENIO);

            $this->logar(
                $request->covenant['credentials']['code'],
                $request->covenant['credentials']['password']
            );

            //dd(implode( ", ", $request->procedures ));

            //902002364260050
            $this->consultarCarencia(
                $request->patient['card_number'],
                $request->procedures
            );

            $this->retorno();

            $this->kill();

            return response()->json(Retorno::retornoCarencia(
                $this->status,
                $this->id,
                $request->patient['name'],
                $this->procedimentos,
                $this->locaisAtendimento


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

    public function consultarCarencia($carteirinha, $procedimentos)
    {

        sleep(2);

        $this->driver->findElement(
            By::cssSelector('a[href*="/PRESTADOR/consultar-beneficiario.asp"]')
        )->click();

        $this->driver->findElement(By::id('NroCarteira'))
            ->sendKeys($carteirinha);

        $this->driver->findElement(By::id('btn-submit-carteira'))
            ->click();

        $procedimentos = implode(", ", $procedimentos);

        $this->driver->findElement(By::id('NroServicos'))
            ->sendKeys($procedimentos);

        $this->driver->findElement(By::id('btn-submit'))
            ->click();
    }

    private function retorno()
    {

        $procedimentos = $this->driver->findElements(
            By::cssSelector('#objTableCarenciaServico td')
        );

        $cont = count($procedimentos) / 8;

        $l = 0; //numero para encontrar os elementos dinamicamente
        $this->procedimentos = [];

        for ($i = 1; $i <= $cont; $i++) {

            array_push($this->procedimentos, [
                "tuss" => $procedimentos[0 + $l]->getText(),
                "desc" => $procedimentos[1 + $l]->getText(),
                "amb" => $procedimentos[2 + $l]->getText(),
                "hosp." => $procedimentos[3 + $l]->getText(),
                "parto" => $procedimentos[4 + $l]->getText(),
                "odonto" => $procedimentos[5 + $l]->getText(),
                "urgencia" => $procedimentos[6 + $l]->getText(),
                "auto" => $procedimentos[7 + $l]->getAttribute("ALT")
            ]);

            $l += 8;
        }

        $locaisAtendimento = $this->driver->findElements(
            By::cssSelector('.tabela.tabela-local td')
        );

        $cont = count($locaisAtendimento) / 2;

        $l = 0; //numero para encontrar os elementos dinamicamente
        $this->locaisAtendimento = [];

        array_push($this->locaisAtendimento, [
            'ambulatorio' => $locaisAtendimento[1]->getText(),
            'apartamento' => $locaisAtendimento[3]->getText(),
            'bercario' => $locaisAtendimento[5]->getText(),
            'consultorio' => $locaisAtendimento[7]->getText(),
            'day_clinic' => $locaisAtendimento[9]->getText(),
            'diagnose_terapia' => $locaisAtendimento[11]->getText(),
            'domicilio' => $locaisAtendimento[13]->getText(),
            'enfermaria' => $locaisAtendimento[15]->getText(),
            'estabel_comercial' => $locaisAtendimento[17]->getText(),
            'outros' => $locaisAtendimento[19]->getText(),
            'quarto_col' => $locaisAtendimento[21]->getText(),
            'quarto_indiv' => $locaisAtendimento[23]->getText(),
            'uni_semi_intensiva' => $locaisAtendimento[25]->getText(),
            'uti_un_trat' => $locaisAtendimento[27]->getText()
        ]);

        // for ($i = 1; $i <= $cont; $i++) {
        //     array_push($this->locaisAtendimento, [
        //         $locaisAtendimento[0 + $l]->getText() => $locaisAtendimento[1 + $l]->getText(),
        //     ]);
        //     $l += 2;
        // }
        //dd($this->locaisAtendimento);
    }

    private function kill()
    {
        $this->driver->quit();
    }
}
