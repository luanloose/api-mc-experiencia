<?php

namespace App\API;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Tests\DuskTestCase;

class Browser extends DuskTestCase
{
    /**
     * @method  browser -> Método para instanciar o chromeDriver
     * @see     Tests\DuskTestCase
     * @return  response -> driver com configurações passadas via parametro
     */
    public function browser()
    {
        if (config('app.env') === 'producao') {

            //silent mode
            $host = 'http://localhost:4444/wd/hub';

            $desiredCapabilities = DesiredCapabilities::chrome();
            $desiredCapabilities->setCapability(
                'goog:chromeOptions',
                ['args' => [
                    '--disable-gpu',
                    '--headless',
                    '--no-sandbox'
                ]]
            );

            return RemoteWebDriver::create($host, $desiredCapabilities);
        } else {

            //Mostra o browser na tela
            $host = 'http://localhost:4444/wd/hub';
            $desiredCapabilities = DesiredCapabilities::chrome();
            $desiredCapabilities->setCapability(
                'goog:chromeOptions',
                ['args' => ['no-first-run']]
            );

            return RemoteWebDriver::create($host, $desiredCapabilities);
        }
    }
}
