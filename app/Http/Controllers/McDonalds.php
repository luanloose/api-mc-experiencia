<?php

namespace App\Http\Controllers;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use App\API\Retorno;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;

class McDonalds extends Browser
{
    const MC = 'https://mcexperiencia.com.br/';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\mcdonalds\\');
        $this->driver = $this->browser();
    }

    /**
     * @method  Autorizacao -> Método para preencher campos e verificar guia
     * @param   Request $request -> recebe json via post
     * @see     App\API\Browser
     * @return  response -> Json com dados referente a guia ou erro
     */
    public function cupom(Request $request)
    {
        try {
            $this->driver->get(self::MC);

            $this->driver->switchTo()->frame("iframelanding");
            //for="aceptotyc"
            //movenextbtn

            // $this->driver->findElement(
            //     By::cssSelector('label.he_leido')
            // )->click();

            $element = $this->driver->findElement(By::cssSelector('label.custom-check.checkbox'));
            $this->driver->wait(10, 1000)->until(
                Condition::visibilityOf($element)
            );
            $element->click();

            $this->driver->findElement(By::id('movenextbtn'))
            ->click();

            sleep(4);

            $this->preencher(
                $request->cnpj,
                $request->date['day'],
                $request->date['month'],
                $request->date['year'],
                $request->time['hour'],
                $request->time['minute'],
                $request->nf
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
            return $erro->getErroUnimedRio($this->driver, $e);
        }
    }


    private function preencher($cnpj, $day, $mon, $year, $hour, $min, $nf)
    {

        $element = $this->driver->findElement(By::id('cnpj'));
       
        $element->isDisplayed()->sendKeys($cnpj);

        $this->driver->findElement(By::id('movenextbtn'))
                ->click();
        // input-day
        $select = new Select($this->driver->findElement(By::cssSelector('select.input-day')));
        $select->selectByVisibleText($day);

        
        $select = new Select($this->driver->findElement(By::cssSelector('select.input-month')));
        $select->selectByVisibleText($mon);
        
        $select = new Select($this->driver->findElement(By::cssSelector('select.input-year')));
        $select->selectByVisibleText($year);

        
        $select = new Select($this->driver->findElement(By::cssSelector('select.input-hours')));
        $select->selectByVisibleText($hour);
        
        $select = new Select($this->driver->findElement(By::cssSelector('select.input-minutes')));
        $select->selectByVisibleText($min);
        
        $this->driver->findElement(By::cssSelector('input[pattern="[0-9]*"'))
                ->sendKeys($nf);

        $this->driver->findElement(By::id('movenextbtn'))
                ->click();
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
