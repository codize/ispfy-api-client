<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);

//Consulta boletos
$api->request('/tool/assinante/boleto', 'GET', [
    "doc" => "99.745.928/0001-62",
    "tipo" => "carne",
    "status" => "todos"
]);

//Imprime resposta
print_r($api->getBodyContent());
