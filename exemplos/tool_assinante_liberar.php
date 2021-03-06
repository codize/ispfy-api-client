<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);

//Consulta cliente
$cliente = $api->get('/tool/assinante/info', ["doc" => "99.745.928/0001-62"]);

//Escolhe contrato
$idContrato = $cliente['contratos'][0]['id'];

//Libera cliente
$api->post('/tool/assinante/liberar', ["id_contrato" => $idContrato]);

