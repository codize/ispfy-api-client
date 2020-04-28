<?php

include __DIR__ . '/../vendor/autoload.php';

//Inicia API
$api = new Ispfy\Api("https://demo.ispfy.com.br:8043", "51bb35d7d5408292f63c6f620a844d43");
$api->setVerifySSL(false);


//Consulta cliente pelo cnpj
$cpfCnpj = preg_replace("/[^0-9]/", "", "99.745.928/0001-62");
$cliente = $api->get('/object/cliente', [
    "filter" => "cpf_cnpj:EQ:{$cpfCnpj}",
]);

//Consulta contratos pelo id doc cliente
$idCliente = $cliente[0]['id'];
$contratos = $api->get('/object/cliente/contrato', [
    "filter" => "id_cliente:EQ:{$idCliente} [AND] contrato_ativo:EQ:s",
]);

//Consulta titulos abertos
$idsContratos = implode(",", array_map(function ($row) {
    return $row['id'];
}, $contratos)) ?: [0];

$hoje = date("Y-m-d");

$titulos = $api->get('/object/cliente/contrato/cobranca', [
    "filter" => "id_contrato:IN:{$idsContratos} [AND] status:EQ:aberto [AND] data_vencimento:LT:{$hoje}",
]);

//Imprime resultado
print_r($titulos);
