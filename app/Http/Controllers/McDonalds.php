<?php

namespace App\Http\Controllers;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;

class McDonalds extends Browser
{
    private $code;
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

            $this->driver->switchTo()->frame(0);

            $this->driver->findElement(By::cssSelector('label.custom-check.checkbox'))
            ->click();


            $this->driver->findElement(By::id('movenextbtn'))
            ->click();

            $this->fillNote(
                $request->cnpj,
                $request->date['day'],
                $request->date['month'],
                $request->date['year'],
                $request->time['hour'],
                $request->time['minute'],
                $request->nf
            );
            
            $this->answerQuestions($request->text);

            $this->getCode(
                $request->email,
                $request->name,
                $request->age
            );

            $this->retorno();

            $this->kill();

            return response()->json([
                "msg" => "Cupom enviado para o email"
            ], 200);

        } catch (\Exception $e) {
            $erro = new Erro;
            return $erro->getErroUnimedRio($this->driver, $e);
        }
    }


    private function fillNote($cnpj, $day, $mon, $year, $hour, $min, $nf)
    {
        $element = $this->driver->findElement(By::id('cnpj'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );
        $element->sendKeys($cnpj);
        
        sleep(2);

        $this->driver->findElement(By::id('movenextbtn'))
                ->click();
        
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
        
        $this->driver->findElement(By::cssSelector('#NTKT > .input-text'))
                ->sendKeys($nf);

        $this->driver->findElement(By::id('movenextbtn'))
                ->click();
    }

    public function answerQuestions($text)
    {

        $element = $this->driver->findElement(By::id('label-answer658597X8857X36524Y'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );
        $element->click();

        $this->driver->findElement(By::id('label-answer658597X8846X365201'))
                ->click();

        $this->driver->findElement(By::id('label-answer658597X8846X365192'))
                ->click();

        $this->driver->findElement(By::id('movenextbtn'))
                ->click();

        $element = $this->driver->findElement(By::cssSelector('#qb\.answer658597X8847X365211-4 span'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );
        $element->click();

        $this->driver->findElement(By::id('answer658597X8859X36540'))
                ->sendKeys($text);
        sleep(1);

        $this->driver->findElement(By::id('movenextbtn'))
            ->click();
        
        $element = $this->driver->findElement(By::cssSelector('#qb\.answer658597X8848X365224-4 > .answerOptionButton'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );
        $element->click();
        
        $element = $this->driver->findElement(By::cssSelector('#qb\.answer658597X8848X365222-1 > .answerOptionButton.answerOptionButton-border'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );
        $element->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8849X365423-4 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8879X365721-4 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8852X365252-4 > .answerOptionButton'))
            ->click();
        
        $element = $this->driver->findElement(By::cssSelector('#qb\.answer658597X8852X365255-4 > .answerOptionButton'));
        $this->driver->wait(10, 1000)->until(
            Condition::visibilityOf($element)
        );
        $element->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8861X365441-4 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8878X365711-4 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8853X36526SQ001-8 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::id('label-answer658597X8863X36545N'))
                ->click();

        $this->driver->findElement(By::id('answer658597X8854X36527'))
                ->click();

        $this->driver->findElement(By::id('answer658597X8854X36527'))
                ->click();
    }

    private function getCode($email, $name, $age)
    {
        $this->driver->findElement(By::id('answer658597X8854X36527'))
                ->sendKeys($email);

        $this->driver->findElement(By::id('answer658597X8854X36528'))
                ->sendKeys($name);

        $this->driver->findElement(By::id('label-answer658597X8854X36529M'))
                ->click();

        $this->driver->findElement(By::id('answer658597X8854X36530'))
                ->sendKeys($age);

        $this->driver->findElement(By::id('movenextbtn'))
                ->click();
    }

    private function retorno()
    {
        $this->driver->findElement(By::id('movenextbtn'))
                ->click();
        $this->driver->findElement(By::cssSelector('button.send'))
                ->click();
        $this->driver->switchTo()->alert()->accept();

        sleep(2);
    }

    private function kill()
    {
        $this->driver->quit();
    }
}