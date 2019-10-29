<?php

$dias_de_prazo_para_pagamento = $boleto["prazo"];
$taxa_boleto = $boleto["taxa"];
$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = $totalBoleto; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".", $valor_cobrado);
$valor_boleto = number_format($valor_cobrado + $taxa_boleto, 2, ',', '');


$dadosboleto["nosso_numero"] = str_pad($pedido["id"], 5, "0", STR_PAD_LEFT);
$dadosboleto["nosso_numero"] = "87654";

$dadosboleto["numero_documento"] = $pedido["id"]; // Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto;  // Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula


// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = "$cliente[nome] - CPF: $cliente[documento]";
$dadosboleto["endereco1"] = "$cliente[endereco], $cliente[numero] $cliente[complemento], $cliente[bairro]";
$dadosboleto["endereco2"] = "$cliente[cidade] - $cliente[estado] -  CEP: $cliente[cep]";

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja " . $empresa["titulo"];
$dadosboleto["demonstrativo2"] = "";
$dadosboleto["demonstrativo3"] = "";
$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% ap�s o vencimento";
$dadosboleto["instrucoes2"] = "- Receber at� 10 dias ap�s o vencimento";
$dadosboleto["instrucoes3"] = "- Em caso de d�vidas entre em contato conosco: " . $empresa["email"];
$dadosboleto["instrucoes4"] = "";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "10";
$dadosboleto["valor_unitario"] = "10";
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";

$dadosboleto["aceite"] = $boleto["aceite"];
$dadosboleto["especie"] = $boleto["especie"];
$dadosboleto["especie_doc"] = $boleto["especie_doc"];

// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
// DADOS DA SUA CONTA - BANCO DO BRASIL
$dadosboleto["agencia"] = $boleto["agencia"]; // Num da agencia, sem digito
$dadosboleto["conta"] = $boleto["conta"];  // Num da conta, sem digito

// DADOS PERSONALIZADOS - BANCO DO BRASIL
$dadosboleto["convenio"] = $boleto["convenio"];  // Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
$dadosboleto["contrato"] = $boleto["contrato"]; // Num do seu contrato
$dadosboleto["carteira"] = $boleto["carteira"];
$dadosboleto["variacao_carteira"] = $boleto["variacao_carteira"];  // Varia��o da Carteira, com tra�o (opcional)
//
// TIPO DO BOLETO
$dadosboleto["formatacao_convenio"] = $boleto["formatacao_convenio"]; // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
$dadosboleto["formatacao_nosso_numero"] = $boleto["formatacao_nosso_numero"]; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos
/*
  #################################################
  DESENVOLVIDO PARA CARTEIRA 18
  - Carteira 18 com Convenio de 8 digitos
  Nosso n�mero: pode ser at� 9 d�gitos
  - Carteira 18 com Convenio de 7 digitos
  Nosso n�mero: pode ser at� 10 d�gitos
  - Carteira 18 com Convenio de 6 digitos
  Nosso n�mero:
  de 1 a 99999 para op��o de at� 5 d�gitos
  de 1 a 99999999999999999 para op��o de at� 17 d�gitos
  #################################################
 */
// SEUS DADOS
$dadosboleto["identificacao"] = $empresa["titulo"];
$dadosboleto["cpf_cnpj"] = $empresa["cnpj"];
$dadosboleto["endereco"] = $empresa["endereco"];
$dadosboleto["cidade_uf"] = $empresa["cidade"] . " / " . $empresa["uf"];
$dadosboleto["cedente"] = $empresa["razao_social"];

// N�O ALTERAR!
include("include/funcoes_bb.php");
include("include/layout_bb.php");
