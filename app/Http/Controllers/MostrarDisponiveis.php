<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

class MostrarDisponiveis extends Controller
{   
    /**
     * @method  unimedRio -> Método para mostrar funcoes do convenio
     * @return  response -> Json com dados referente as funcoes disponiveis
     */
    public function unimedRio()
    {
        $funcoes = ['autorizar','cancelar','consultar'];

        return ['funcoes' => $funcoes];
    }

    public function padrao()
    {   
        $rotas = [];

         foreach (Route::getRoutes() as $route) {
            if (strpos($route->getName(), "padrao") !== false) {
                array_push($rotas, $route->uri);
            }
        }
        
        return ['rotas' => $rotas];

    }

    //classe padrao para reaproveitar
    public function convenio()
    {   
        $funcoes = [];
        
        return ['funcoes' => $funcoes];
    }


    public function convenios($id = null)
    {   
        if($id){
            $convenios = collect(config('convenios'))->where('id',$id);
        }else{
            $convenios = collect(config('convenios'));
        }
        
        return response()->json($convenios,200);
    }



}
