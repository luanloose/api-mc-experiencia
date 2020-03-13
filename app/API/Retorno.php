<?php


namespace App\API;

class Retorno
{

    public static function retorno(
        $senha = '----',
        $status,
        $situação,
        $numeroGuia,
        $dataValSenha = '----',
        $nomePaciente,
        $procedimentos = '----'
    ) {
        return [
            "password" => $senha,
            "status" => $status,
            "message" => $situação,
            "document_url" => "----",
            "provider_guide" => $numeroGuia,
            "operator_guide" => "----",
            "expiration_date" => $dataValSenha,
            "patient" => [
                "name" => $nomePaciente
            ],
            "procedures" => $procedimentos,
            "id" => ""
        ];

    }

    public static function retornoElegibilidade(
        $status,
        $id = '----',
        $nomePaciente
    ) {
        return [
            "status" => $status,
            "patient" => [
                "name" => $nomePaciente
            ],
            "id" => $id
        ];

    }

    public static function retornoCarencia(
        $status,
        $id = '----',
        $nomePaciente,
        $procedimentos,
        $locais
    ) {
        return [
            "status" => $status,
            "patient" => [
                "name" => $nomePaciente
            ],
            "procedures" => $procedimentos,
            "service_locations" => $locais,
            "id" => $id
        ];

    }

}
