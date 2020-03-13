<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\API\ApiError as Erro;
use App\Http\Controllers\Api\unimedrio\ConsultarAutorizacao as UnimedRioConsulta;
use App\Http\Controllers\Api\unimedrio\CancelarGuia as UnimedRioCancelar;
use App\Http\Controllers\Api\unimedrio\Autorizacao as UnimedRioAutorizacao;
use App\Http\Controllers\Api\bradesco\Autorizacao as BradescoAutorizacao;
use App\Http\Controllers\Api\sulamerica\Autorizacao as SulAmericaAutorizacao;
use App\Http\Controllers\Api\geap\Autorizacao as GeapAutorizacao;
use App\Http\Controllers\Api\geap\Elegibilidade as GeapElegibilidade;
use App\Http\Controllers\Api\geap\Carencia as GeapCarencia;
use App\Http\Controllers\Api\calculo\Calculo as Calcular;



class RedirecionarConvenio extends Controller
{
    /**
     * @method  redirecionar -> Método para enviar requisição para o convenio
     * @param   Request $request -> recebe json via post com o id que sera redirecionado
     * @return  response -> Json com dados referente a funçao passada na rota
     */
    public function redirecionar(Request $request)
    {
        $id = $request->covenant['id'];
        switch ($id) {
            case "000001":
                return $this->unimedRio($request);

            case "000002":
                return $this->bradesco($request);

            case "000003":
                return $this->sulAmerica($request);

            case "000004":
                return $this->geap($request);
                
            default:
                return response()->json(Erro::errorMessage('Id do convenio não encontrada', 100), 404);
        }
    }

    public function unimedRio($request)
    {
        switch (Route::currentRouteName()) {

            case "padrao.cancelar":
                $cancelar = new UnimedRioCancelar;
                return $cancelar->cancelarGuia($request);

            case "padrao.autorizar":
                $autorizar = new UnimedRioAutorizacao;
                return $autorizar->autorizar($request);

            case "padrao.consultar":
                $consultar = new UnimedRioConsulta;
                return $consultar->consultar($request);
        }
    }

    public function bradesco($request)
    {
        switch (Route::currentRouteName()) {

            case "padrao.autorizar":
                $autorizar = new BradescoAutorizacao;
                return $autorizar->autorizar($request);
        }
    }

    public function sulAmerica($request)
    {
        switch (Route::currentRouteName()) {

            case "padrao.autorizar":
                $autorizar = new SulAmericaAutorizacao;
                return $autorizar->autorizar($request);
        }
    }

    public function geap($request)
    {
        switch (Route::currentRouteName()) {

            case "padrao.autorizar":
                $autorizar = new GeapAutorizacao;
                return $autorizar->autorizar($request);

            case "padrao.elegibilidade":
                $eleger = new GeapElegibilidade;
                return $eleger->consultar($request);

            case "padrao.carencia":
                $carencia = new GeapCarencia;
                return $carencia->carencia($request);

            case "padrao.calculo":
                $calculo = new Calcular;
                return $calculo->calcular($request);
        }
    }

    //classe padrao para reaproveitar
    public function convenio($request)
    {
        switch (Route::currentRouteName()) {

                // case "cancelar":
                //     $cancelar = new ;
                //     return $cancelar->cancelarGuia($request);

                // case "autorizar":
                //     $autorizar = new ;
                //     return $autorizar->autorizar($request);

                // case "consultar":
                //     $consultar = new ;
                //     return $consultar->consultar($request);

                // case "padrao.elegibilidade":
                //     $autorizar = new ;
                //     return $autorizar->autorizar($request);
        }
    }
}
