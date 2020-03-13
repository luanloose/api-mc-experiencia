<?php

//retorna json com dados e campos que convenio ultiliza

return [
    [
        "id" => "000001",
        "name" => "UnimedRio",
        "credentials_keys" => [
            "user",
            "password"
        ],
        "ans" => "",
        "cnpj" => "",
        "type" => "string",
        "organization" => "",
        "active" => true,
        "services" => [
            "eligible" => false,
            "authorize" => true,
            "status" => true,
            "cancel" => true
        ]
    ],
    [
        "id" => "000002",
        "name" => "Bradesco",
        "credentials_keys" => [
            "cpf",
            "cnpj",
            "password"
        ],
        "ans" => "",
        "cnpj" => "",
        "type" => "",
        "organization" => "",
        "active" => false,
        "services" => [
            "eligible" => false,
            "authorize" => false,
            "status" => true,
            "cancel" => true
        ]
    ],
    [
        "id" => "000003",
        "name" => "Sul America",
        "credentials_keys" => [
            "cnpj",
            "code",
            "user",
            "password"
        ],
        "ans" => "",
        "cnpj" => "",
        "type" => "",
        "organization" => "",
        "active" => false,
        "services" => [
            "eligible" => false,
            "authorize" => true,
            "status" => false,
            "cancel" => false
        ]
    ],
    [
        "id" => "00004",
        "name" => "Geap",
        "credentials_keys" => [
            "code",
            "password"
        ],
        "ans" => "",
        "cnpj" => "",
        "type" => "string",
        "organization" => "PRESTADORA DE SERVIÇOS LTDA",
        "active" => false,
        "services" => [
            "eligible" => false,
            "authorize" => false,
            "status" => false,
            "cancel" => false
        ]
        ],
        [
            "id" => "00000",
            "name" => "Nome do convênio",
            "credentials_keys" => [
                "user",
                "password"
            ],
            "ans" => "415949",
            "cnpj" => "99222333000529",
            "type" => "string",
            "organization" => "PRESTADORA DE SERVIÇOS LTDA",
            "active" => true,
            "services" => [
                "eligible" => true,
                "authorize" => false,
                "status" => true,
                "cancel" => true
            ]
            ]

];
