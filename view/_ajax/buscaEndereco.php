<?php

require_once '../../class/class-MainModel.php';
require_once '../../class/class-DB.php';
require_once '../../model/frete/model-frete.php';
$cep = $_POST[cep];
$frete = new ModelFrete();
$frete->cep = $cep;
$endereco = $frete->buscarEndereco();
$endereco["cidade"] = utf8_decode($endereco["cidade"]);
$endereco["logradouro"] = utf8_decode($endereco["logradouro"]);
$endereco["bairro"] = utf8_decode($endereco["bairro"]);
echo json_encode($endereco);
