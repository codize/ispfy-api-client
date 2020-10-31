<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);

//Consulta boleto
$boleto = $api->request("/tool/cobranca/imprimir/333126", 'GET');

//Mostra resultado
echo $api->getBodyContent();

