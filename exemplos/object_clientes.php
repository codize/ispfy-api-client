<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);

//Consulta cliente pelo cnpj
$cliente = $api->get('/object/cliente', [
    "filter" => "nome_razao:CONTAINS:paulo",
    "pagination" => true
]);

print_r($cliente);

