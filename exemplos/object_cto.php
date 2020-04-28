<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);

//Consulta CTOs dentro de uma área
$lonStart = -53.19;
$lonEnd = -53;
$latStart = -26;
$latEnd = -25;

$ctos = $api->get('/object/geofiber/cto',
    ["limit" => -1, "filter" => "latitude:BTW:{$latStart},{$latEnd}[AND]longitude:BTW:{$lonStart},{$lonEnd}"]
);

//Cria uma lista com os IDs da CTOs
$idsCtos = implode(",", array_map(function ($row) {
    return $row['id'];
}, $ctos));

//Cria uma lista com os IDs dos Spliters
$spliters = $api->get('/object/geofiber/spliter', ["limit" => -1, "filter" => "id_cto:IN:{$idsCtos}"]);

//Cria uma lista com os IDs da CTOs
$idsSpliters = implode(",", array_map(function ($row) {
    return $row['id'];
}, $spliters));

//Cria uma lista com os IDs dos Spliters
$vias = $api->get('/object/geofiber/spliter/via',["limit" => -1, "filter" => "id_spliter:IN:{$idsSpliters}"]);

//Mostra resultado
print_r($vias);
