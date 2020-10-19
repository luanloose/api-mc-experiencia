<?php

namespace App\Http\Controllers;

use App\API\Browser;
use App\API\Errors;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use Facebook\WebDriver\WebDriverExpectedCondition as Condition;

class McDonalds extends Browser
{
    private $driver;
    const MC = 'https://mcexperiencia.com.br/';

    /**
     * @method  getCupom -> Método para preencher campos e capturar cupom
     * @param   Request $request -> recebe json via post
     * @see     App\API\Browser
     * @return  response -> Json com dados referente ao cupom
     */
    public function getCupom(Request $request)
    {
        try {

            $this->driver = $this->browser();

            $this->driver->get(self::MC);

            $this->driver->switchTo()->frame(0);

            $this->driver->findElement(By::cssSelector('label.custom-check.checkbox'))
                ->click();

            $this->driver->findElement(By::id('movenextbtn'))
                ->click();

            $this->fillNote(
                $request->cnpj,
                $request->date,
                $request->time,
                $request->nf
            );

            $this->answerQuestions($request->text);

            $this->getCode(
                $request->email,
                $request->name,
                $request->age
            );

            $src = $this->retorno();

            $this->kill();

            return view('cupom', compact(['src']));
        } catch (\Exception $e) {
            $error = new Errors($this->driver, $e, $request->all());
            return $error->getError();
        }
    }


    private function fillNote($cnpj, $date, $time)
    {

        $this->driver->wait(3, 500)->until(
            Condition::visibilityOfElementLocated(
                By::id('cnpj')
            )
        );
        $this->driver->findElement(By::id('cnpj'))->sendKeys($cnpj);

        sleep(2);

        $this->driver->findElement(By::id('movenextbtn'))
            ->click();

        $date = explode("-", $date);

        $select = new Select($this->driver->findElement(By::cssSelector('select.input-day')));
        $select->selectByVisibleText($date[2]);

        $select = new Select($this->driver->findElement(By::cssSelector('select.input-month')));
        $select->selectByVisibleText($date[1]);

        $select = new Select($this->driver->findElement(By::cssSelector('select.input-year')));
        $select->selectByVisibleText($date[0]);

        $time = explode(":", $time);

        $select = new Select($this->driver->findElement(By::cssSelector('select.input-hours')));
        $select->selectByVisibleText($time[0]);

        $select = new Select($this->driver->findElement(By::cssSelector('select.input-minutes')));
        $select->selectByVisibleText($time[1]);

        $this->driver->findElement(By::id('movenextbtn'))
            ->click();
    }

    public function answerQuestions($text)
    {

        $this->waitAndClickId([
            'label-answer592111X11590X45955Y',
            'label-answer592111X11579X459511',
            'label-answer592111X11579X459501',
            'label-answer592111X11579X459781'
        ]);

        $this->driver->findElement(By::id('movenextbtn'))->click();

        $this->waitAndClickSelector([
            '#question45952 div div ul li div:nth-child(4)'
        ]);

        $this->driver->findElement(By::id('answer592111X11592X45971'))->sendKeys($text);
        sleep(1);

        $this->driver->findElement(By::id('movenextbtn'))->click();


        $this->waitAndClickSelector([
            '#question45953 div div ul li div:nth-child(4)',
            'div div ul li#qw-1 div:nth-child(2) div:nth-child(1)',
            '#question45973 div div ul li div:nth-child(4)',
            '#question45992 div div ul li div:nth-child(4)',
            '#question45956 div div ul li div:nth-child(4)',
            'div div ul li#qw-1 div:nth-child(2) div:nth-child(4)',
            '#question45974 div div ul li div:nth-child(4)',
            '#question45993 div div ul li div:nth-child(4)',
        ]);

        $this->driver->findElement(By::id('answer592111X11605X45995'))->sendKeys($text);
        $this->driver->findElement(By::id('movenextbtn'))->click();

        $this->waitAndClickSelector([
            '#question45975 div div ul li div:nth-child(4)',
            '#question45991 div div ul li div:nth-child(4)',
            '#question45957 div div ul li div:nth-child(10)',
            '#question45976 div div:nth-child(3) label:nth-child(2)'
        ]);
    }

    private function getCode($email, $name, $age)
    {
        $this->driver->findElement(By::id('answer592111X11587X45958'))->sendKeys($email);

        $this->driver->findElement(By::id('answer592111X11587X45959'))->sendKeys($name);

        $this->driver->findElement(By::id('label-answer592111X11587X45960M'))->click();

        $this->driver->findElement(By::id('answer592111X11587X45961'))->sendKeys($age);

        $this->driver->findElement(By::id('movenextbtn'))->click();
    }

    private function retorno()
    {
        $this->driver->findElement(By::id('movenextbtn'))->click();

        $this->driver->wait(3, 500)->until(
            Condition::visibilityOfElementLocated(
                By::cssSelector('#question45954 div div div div p img')
            )
        );
        $file = $this->driver->findElement(By::cssSelector('#question45954 div div div div p img'));

        $src = $file->getAttribute("src");

        return $src;
    }


    public function waitAndClickId($ids)
    {
        foreach ($ids as $id) {
            $this->driver->wait(3, 500)->until(
                Condition::visibilityOfElementLocated(
                    By::id($id)
                )
            );
            $this->driver->findElement(By::id($id))->click();
        }
    }

    public function waitAndClickSelector($selectors)
    {
        foreach ($selectors as $selector) {
            $this->driver->wait(3, 500)->until(
                Condition::visibilityOfElementLocated(
                    By::cssSelector($selector)
                )
            );
            $this->driver->findElement(By::cssSelector($selector))->click();
        }
    }

    private function kill()
    {
        $this->driver->quit();
    }
}
