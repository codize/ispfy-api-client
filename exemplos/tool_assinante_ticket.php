<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);

//Consulta cliente
$cliente = $api->get('/tool/assinante/info', ["doc" => "99.745.928/0001-62"]);

//Consulta topicos
$topicos = $api->get('/object/suporte/topico');

//Abre ticket
$ticket = $api->post('/tool/assinante/ticket', [
  "setor" => "TECNICO",
  "id_cliente" => $cliente['id'],
  "id_topico" => $topicos[0]['id'],
  "requisicao" => "Internet lenta",
]);

//Mostra protocolo
print_r($ticket);
