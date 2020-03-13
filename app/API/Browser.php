<?php


namespace App\API;

use Facebook\WebDriver\Chrome\ChromeOptions;
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
        if (config('app.env') === 'producaoo') {
            
            //silent mode
            $options = (new ChromeOptions)->addArguments([
                '--disable-gpu',
                '--headless',
                '--no-sandbox'
            ]);

            $host = 'http://localhost:4444/wd/hub';
            return RemoteWebDriver::create($host, DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            ));
        } else {

            //Mostra o browser na tela
            $chromeOptions = new ChromeOptions();
            $chromeOptions->addArguments(['no-first-run']);
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

            return RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
        }
    }
}
