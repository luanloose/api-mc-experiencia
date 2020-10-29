<?php

namespace App\API;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

trait Browser
{
    /**
     * @method  browser -> Método para instanciar o chromeDriver
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function browser()
    {
        if (config('app.env') === 'production') {
            //silent mode
            $args = [
                '--disable-gpu',
                '--headless',
                '--no-sandbox',
                'window-size=1460,820'
            ];
        } else {
            //Mostra o browser na tela
            $args = [
                'no-first-run',
                'window-size=1460,820'
            ];
        }
        $host = 'http://localhost:4444/wd/hub';
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(
            'goog:chromeOptions',
            ['args' => $args]
        );
        $desiredCapabilities->setPlatform('windows');

        return RemoteWebDriver::create($host, $desiredCapabilities, 60000, 60000);
    }

    public function firefox()
    {
        if (config('app.env') === 'production') {
            //silent mode
            $args = [
                '-headless'
            ];
        } else {
            //Mostra o browser na tela
            $args = [];
        }

        $desiredCapabilities = DesiredCapabilities::firefox();

        $host = 'http://localhost:4444/wd/hub';

        // Disable accepting SSL certificates
        $desiredCapabilities->setCapability('acceptSslCerts', false);
        $desiredCapabilities->setCapability(
            'moz:firefoxOptions',
            ['args' => $args]
        );

        return RemoteWebDriver::create($host, $desiredCapabilities);
    }


    /**
     * @method  browserSession -> Método para recuperar o Driver
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function browserSession($session)
    {
        $host = 'http://localhost:4444/wd/hub';

        return RemoteWebDriver::createBySessionID($session, $host, 60000, 60000);
    }
}
