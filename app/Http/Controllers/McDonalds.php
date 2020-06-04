<?php

namespace App\Http\Controllers;

use App\API\Browser;
use App\API\ApiError as Erro;
use App\API\Validar;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Facebook\WebDriver\WebDriverDimension as Size;
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
        $this->driver->manage()->window()->setSize(new Size(1460, 820));
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
            $rules = [
                'covenant.credentials.user' => 'required',
                'covenant.credentials.password' => 'required',
                'patient.card_number' => 'required',
                'patient.newborn' => 'required',
                'medic_requester.name' => 'required',
                'medic_requester.type_council' => 'required',
                'medic_requester.number_council' => 'required',
                'medic_requester.region' => 'required',
                'medic_requester.cbo' => 'required',
                'procedures' => 'required'
            ];

            $errors = Validar::testar($request, $rules, $this->driver);

            if ($errors) {
                return response()->json(Erro::errorMessage($errors, 400), 400);
            }

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
            return $erro->getError($this->driver, $e);
        }
    }


    private function fillNote($cnpj, $day, $mon, $year, $hour, $min, $nf)
    {

        $this->driver->wait(3, 1000)->until(
            Condition::visibilityOfElementLocated(
                By::id('cnpj')
            )
        );
        $this->driver->findElement(By::id('cnpj'))->sendKeys($cnpj);

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

        $this->driver->wait(3, 1000)->until(
            Condition::visibilityOfElementLocated(
                By::id('label-answer658597X8857X36524Y')
            )
        );
        $this->driver->findElement(By::id('label-answer658597X8857X36524Y'))->click();


        $this->driver->findElement(By::id('label-answer658597X8846X365201'))
            ->click();

        $this->driver->findElement(By::id('label-answer658597X8846X365192'))
            ->click();

        $this->driver->findElement(By::id('movenextbtn'))
            ->click();

        $this->driver->wait(3, 1000)->until(
            Condition::visibilityOfElementLocated(
                By::cssSelector('#qb\.answer658597X8847X365211-4 span')
            )
        );
        $this->driver->findElement(
            By::cssSelector('#qb\.answer658597X8847X365211-4 span')
        )->click();

        $this->driver->findElement(By::id('answer658597X8859X36540'))
            ->sendKeys($text);
        sleep(1);

        $this->driver->findElement(By::id('movenextbtn'))
            ->click();

        $this->driver->wait(3, 1000)->until(
            Condition::visibilityOfElementLocated(
                By::cssSelector('#qb\.answer658597X8848X365224-4 > .answerOptionButton')
            )
        );
        $this->driver->findElement(
            By::cssSelector('#qb\.answer658597X8848X365224-4 > .answerOptionButton')
        )->click();

        $this->driver->wait(3, 1000)->until(
            Condition::visibilityOfElementLocated(
                By::cssSelector('#qb\.answer658597X8848X365222-1 > .answerOptionButton.answerOptionButton-border')
            )
        );
        $this->driver->findElement(
            By::cssSelector('#qb\.answer658597X8848X365222-1 > .answerOptionButton.answerOptionButton-border')
        )->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8849X365423-4 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8879X365721-4 > .answerOptionButton'))
            ->click();

        $this->driver->findElement(By::cssSelector('#qb\.answer658597X8852X365252-4 > .answerOptionButton'))
            ->click();

        $this->driver->wait(3, 1000)->until(
            Condition::visibilityOfElementLocated(
                By::cssSelector('#qb\.answer658597X8852X365255-4 > .answerOptionButton')
            )
        );
        $this->driver->findElement(
            By::cssSelector('#qb\.answer658597X8852X365255-4 > .answerOptionButton')
        )->click();

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
