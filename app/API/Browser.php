<?php

namespace App\API;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Browser
{
    /**
     * @method  browser -> Método para instanciar o chromeDriver
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
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
                    '--no-sandbox',
                    'window-size=1024,768'
                ]]
            );

            return RemoteWebDriver::create($host, $desiredCapabilities);
        } else {

            //Mostra o browser na tela
            $host = 'http://localhost:4444/wd/hub';
            $desiredCapabilities = DesiredCapabilities::chrome();
            $desiredCapabilities->setCapability(
                'goog:chromeOptions',
                ['args' => [
                    'no-first-run',
                    'window-size=1024,768'
                ]]
            );

            return RemoteWebDriver::create($host, $desiredCapabilities);
        }
    }
}
