<?php

namespace App\Http\Controllers\Api\bradesco;

use App\API\Browser;
use App\API\ApiError as Erro;
use Facebook\WebDriver\WebDriverBy as By;
use Facebook\WebDriver\WebDriverSelect as Select;
use Illuminate\Http\Request;
use App\API\Retorno;


class Autorizacao extends Browser
{
    private $numeroGuia, $situação, $status, $driver, $senha, $dataValSenha, $procedimentos, $nomePaciente;
    protected $path;

    const CONVENIO = 'https://wwws.bradescosaude.com.br/PCBS-GerenciadorPortal/td/loginReferenciado.do',
        AUTORIZACAO = self::CONVENIO . '/Autorizacao/EnvioPreAutorizacao/0',
        SOLICITAR = self::CONVENIO . '/Autorizacao/EnvioSadt/0';

    public function __construct()
    {
        $this->path = base_path('storage\app\screenshots\\bradesco\\');
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
                $request->covenant['credentials']['cpf'],
                $request->covenant['credentials']['cnpj'],
                $request->covenant['credentials']['password']
            );
            
            //parei na tela de login pq estava indisponivel

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
            return $erro->getErroBradesco($this->driver, $e);
        }
    }

    private function logar($cpf, $cnpj, $senha)
    {
        //preenche o campo email e realiza login
        $this->driver->findElement(By::id('cpfRefPJ'))
            ->sendKeys($cpf);

        $this->driver->findElement(By::id('cnpjRef'))
            ->sendKeys($cnpj);

        $this->driver->findElement(By::id('senhaRef'))
            ->sendKeys($senha);

        $this->driver->findElement(By::id('btLoginReferenciado'))
            ->click();
    }

   

    private function enviarForm()
    {

    }

    private function retorno()
    {
        
    }

    private function kill()
    {
        $this->driver->quit();
    }
}
