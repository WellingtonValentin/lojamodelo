<?

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerCarrinho extends MainController {

    /**
     * Carrega a página "/view/home/index.php"
     */
    public function meusProdutos() {

// Titulo da página
        $this->title = "Meus Produtos";
        $modelo = $this->loadModel("carrinho/model-carrinho");
        $modeloProduto = $this->loadModel("produto/model-produto");

        $resultado = TRUE;
        if (is_numeric(chkArray($this->parametros, 0))) {
            $modelo->apagarProdutoCarrinho($this->parametros[0]);
        } else {
            if (chkArray($_SESSION, "CLIENTE") && isset($_SESSION['PEDIDO']['CARRINHO'])) {
                $modelo->verificarCarrinhoGuardado();
            }
            if (chkArray($_POST, "variacao") && chkArray($_POST, "quantidade") > 0) {
                $modelo->id_combinacao = $_POST["variacao"];
                $modelo->quantidade = $_POST["quantidade"];
                $resultado = $modelo->adicionarProdutoCarrinho();
            }
        }

        if (!$resultado) {
            echo "<script>alert('Quantidade indisponível em estoque!')</script>";
            echo "<script>javascript:history.back(-2)</script>";
        }

        if (isset($_SESSION["PEDIDO"]["FRETE"])) {
            unset($_SESSION["PEDIDO"]["FRETE"]);
        }
        if (isset($_SESSION["PEDIDO"]["CUPOM"])) {
            unset($_SESSION["PEDIDO"]["CUPOM"]);
        }

// Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . '/view/carrinho/view-carrinho.php';
        require ABSPATH . "/view/_include/footer.php";
    }

    public function mudarQuantidade() {

        $modelo = $this->loadModel("carrinho/model-carrinho");
        $modelo->parametros = $_POST;
        $retorno["resultado"] = $modelo->mudarQuantidade();
        echo json_encode($retorno);
    }

    public function selecionarFrete() {

        require_once ABSPATH . '/enum.php';
        $modelo = $this->loadModel("carrinho/model-carrinho");
        $modeloProduto = $this->loadModel("produto/model-produto");

        $_SESSION["PEDIDO"]["FRETE"]["TIPO"] = $_POST["frete"];
        if (!is_numeric($_POST["valor"])) {
            $_SESSION["PEDIDO"]["FRETE"]["VALOR"] = utf8_decode($_POST["valor"]);
        } else {
            $_SESSION["PEDIDO"]["FRETE"]["VALOR"] = $_POST["valor"];
        }
        if (isset($_POST["prazoDias"])) {
            $_SESSION["PEDIDO"]["FRETE"]["PRAZO"] = $_POST["prazoDias"];
        }

        $total = $modelo->valorTotalCarrinho(TRUE);

        $parcelamento = $modeloProduto->parcelaSemJuros();
        if (isset($parcelamento["0"]["parcelaSemJuros"])) {
            $retorno['numeroParcela'] = $parcelamento["0"]["parcelaSemJuros"];
            $retorno['valorParcela'] = number_format(($total + $_POST["valor"]) / $parcelamento["0"]["parcelaSemJuros"], 2, ',', '.');
        }
        if ($configValores['descontoBoleto']) {
            $retorno['descontoBoleto'] = $configValores['descontoBoleto'];
            $retorno['valorBoleto'] = number_format(($total + $_POST["valor"]) - ((($total + $_POST["valor"]) * $configValores['descontoBoleto']) / 100), 2, ",", ".");
        }

        $retorno["total"] = number_format($total + $_POST["valor"], 2, ",", ".");
        echo json_encode($retorno);
    }

    public function atribuirPresente() {

        $idCombinacao = $_SESSION["PEDIDO"]["CARRINHO"][$_POST['indice']]["COMBINACAO"];
        $this->db->tabela = "produto_combinacao";
        $combinacao = $this->db->consultaId($idCombinacao);

        $_SESSION["PEDIDO"]["CARRINHO"][$_POST['indice']]["VLR_PRESENTE"] = $combinacao['valorPresente'];
        $_SESSION["PEDIDO"]["CARRINHO"][$_POST['indice']]["MSG_PRESENTE"] = $_POST['mensagem'];
    }

    public function validarCupom() {

        $retorno = array();
        $modelo = $this->loadModel("carrinho/model-carrinho");

        if (chkArray($_SESSION, "CLIENTE")) {
            $modelo->parametros = $_POST;
            $resposta = $modelo->validarCupom();

            if ($resposta) {
                $total = $modelo->valorTotalCarrinho(TRUE, TRUE);

                $retorno["total"] = number_format($total, 2, ",", ".");
                switch ($_SESSION["PEDIDO"]["CUPOM"]["TIPO"]) {
                    case "VALOR":
                        $retorno["desconto"] = "<strong>Desconto: </strong>R$ " . number_format($_SESSION["PEDIDO"]["CUPOM"]["VALOR"], 2, ",", ".");
                        break;
                    default :
                        $retorno["desconto"] = "<strong>Desconto: </strong>" . number_format($_SESSION["PEDIDO"]["CUPOM"]["VALOR"], 0, ",", ".") . "%";
                        break;
                }

                $retorno["resposta"] = "VALIDO";
            } else {

                if (chkArray($_SESSION["PEDIDO"], "CUPOM")) {
                    unset($_SESSION["PEDIDO"]["CUPOM"]);
                }
                $retorno["resposta"] = "INVALIDO";
            }
        } else {
            $retorno["resposta"] = "DESLOGADO";
        }

        echo json_encode($retorno);
    }

    public function finalizar() {

        if (!chkArray($_SESSION, "PEDIDO")) {
            header("Location: " . HOME_URL . "/carrinho/meus-produtos/detalhes.html#conteudo");
        }
        if (!chkArray($_SESSION, "CLIENTE")) {
            $_SESSION["REDIRECT_URL"] = HOME_URL . "/carrinho/finalizar/finalizar-compra.html#conteudo";
            header("Location: " . HOME_URL . "/cliente/login/login.html#conteudo");
            exit();
        }

// Titulo da página
        $this->title = "Finalizar Compra";
        $modelo = $this->loadModel("carrinho/model-carrinho");
        $paginas[0]["link"] = "/carrinho/meus-produtos/detalhes.html#conteudo";
        $paginas[0]["titulo"] = "Meus Produtos";
        require ABSPATH . '/enum.php';
        $enderecoEntrega = array();

        $this->db->tabela = "cliente_endereco";
        $consulta = $this->db->consulta("WHERE clienteFK = '" . $_SESSION["CLIENTE"]["id"] . "'");
        if (mysql_num_rows($consulta)) {
            $enderecoEntrega = $this->db->fetchAll($consulta);
        }

        if (chkArray($_SESSION["PEDIDO"], "ENDERECO")) {
            $this->db->tabela = "cliente_endereco";
            $enderecoSelecionado = $this->db->consultaId($_SESSION["PEDIDO"]["ENDERECO"]["ID"]);
            $linkEndereco = HOME_URL . "/cliente/endereco-entrega/" . cr($enderecoSelecionado["id"]) . "/meus-enderecos.htm#conteudo";

            $cep = $enderecoSelecionado["cep"];
        } else {
            $cep = $_SESSION["CLIENTE"]["cep"];
            $enderecoSelecionado = $_SESSION["CLIENTE"];
            $linkEndereco = HOME_URL . "/cliente/dados/meus-dados.html#conteudo";
        }

        $freteTransportadora = FALSE;
        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
            $this->db->tabela = "produto";
            $produto = $this->db->consultaId($produtoCarrinho["ID"]);
            if ($produto["freteTransportadora"] == "S") {
                $freteTransportadora = TRUE;
            }
        }

        if ($freteTransportadora) {
            $modeloTransportadora = $this->loadModel("frete/model-transportadora");
            $modeloCarrinho = $this->loadModel("carrinho/model-carrinho");
            $modeloTransportadora->cep = $cep;
            $modeloTransportadora->valor = $modeloCarrinho->valorTotalCarrinho(TRUE);
            $modeloTransportadora->encontraPeso(array(), TRUE);
            $modeloTransportadora->montaCalculo();

            if (!$modeloTransportadora->erro) {
                $formasEnvio[0]["titulo"] = "Transportadora";
                $formasEnvio[0]["frete"] = "<strong>Frete:</strong> R$ " . number_format($modeloTransportadora->total, 2, ",", ".") . "<br/>";
                $formasEnvio[0]["prazo"] = "<strong>Prazo:</strong> " . $modeloTransportadora->prazo . " Dias úteis*.";
                $formasEnvio[0]["valor"] = $modeloTransportadora->total;
                $formasEnvio[0]["prazoDias"] = $modeloTransportadora->prazo;
                $formasEnvio[0]["tipo"] = "TRANSPORTADORA";
            } else {
                $formasEnvio[0]["titulo"] = "Aguardar Cálculo";
                $formasEnvio[0]["frete"] = "<strong>Frete:</strong> <span>A calcular.</span><br/>";
                $formasEnvio[0]["prazo"] = "<strong>Prazo:</strong> <span>A calcular.</span>";
                $formasEnvio[0]["valor"] = "Calcular";
                $formasEnvio[0]["tipo"] = "FRETE A CALCULAR";
            }
        } else {
            $modeloFrete = $this->loadModel("frete/model-frete");
            $this->db->tabela = "config";
            $modeloFrete->enderecoLoja = $this->db->consultaId(1);
            $modeloFrete->cep = $cep;
            $modeloFrete->telaFinalizacao = TRUE;
            $valorFrete = $modeloFrete->calcularFrete();
            if (is_array($valorFrete)) {
                if (
                        isset($valorFrete["erro"]) ||
                        (
                        (
                        chkArray(chkArray($_SESSION["PEDIDO"], 'FRETE'), 'TIPO') == "PAC" ||
                        chkArray(chkArray($_SESSION["PEDIDO"], 'FRETE'), 'TIPO') == "SEDEX" ||
                        chkArray(chkArray($_SESSION["PEDIDO"], 'FRETE'), 'TIPO') == "ESEDEX"
                        ) &&
                        chkArray(chkArray($_SESSION["PEDIDO"], 'FRETE'), 'VALOR') <= 0
                        )
                ) {
                    $formasEnvio[0]["titulo"] = "Aguardar Cálculo";
                    $formasEnvio[0]["frete"] = "<strong>Frete:</strong> <span>A calcular.</span><br/>";
                    $formasEnvio[0]["prazo"] = "<strong>Prazo:</strong> <span>A calcular.</span>";
                    $formasEnvio[0]["valor"] = "Calcular";
                    $formasEnvio[0]["tipo"] = "FRETE A CALCULAR";
                } else {
                    $indice = 0;
                    foreach ($valorFrete as $fretes => $valor) {
                        $formasEnvio[$indice]["titulo"] = $tipoFrete[$fretes];
                        if ($valor["valor"] != "GRÁTIS") {
                            $formasEnvio[$indice]["frete"] = "<strong>Frete:</strong> R$ " . number_format($valor["valor"], 2, ",", ".") . "<br/>";
                        } else {
                            $formasEnvio[$indice]["frete"] = "<strong>Frete:</strong> Grátis<br/>";
                        }
                        $formasEnvio[$indice]["prazo"] = "<strong>Prazo:</strong> " . $valor["prazo"] . " Dias úteis*.";
                        $formasEnvio[$indice]["prazoDias"] = $valor["prazo"];
                        $formasEnvio[$indice]["valor"] = $valor["valor"];
                        $formasEnvio[$indice]["tipo"] = $fretes;
                        $indice++;
                    }
                }
            }
        }
        $maiorIndice = 0;
        foreach ($formasEnvio as $ind => $valor) {
            if ($ind > $maiorIndice) {
                $maiorIndice = $ind;
            }
        }
        $indiceAtual = $maiorIndice + 1;
        $formasEnvio[$indiceAtual]["titulo"] = "Retirada na loja";
        $formasEnvio[$indiceAtual]["frete"] = "<strong>Frete:</strong> <span>N/A.</span><br/>";
        $formasEnvio[$indiceAtual]["valor"] = "N/A";
        $formasEnvio[$indiceAtual]["tipo"] = "RETIRADA NA LOJA";

        $this->db->tabela = "frete_regiao_entrega";
        $consultaRegiaoEntrega = $this->db->consulta("WHERE '$cep' BETWEEN cepMinimo AND cepMaximo");
        if (mysql_num_rows($consultaRegiaoEntrega)) {
            $regiaoEntrega = mysql_fetch_assoc($consultaRegiaoEntrega);
            $indiceAtual++;
            $formasEnvio[$indiceAtual]["titulo"] = "Entrega Própria";
            $formasEnvio[$indiceAtual]["frete"] = "<strong>Frete:</strong> R$ " . number_format($regiaoEntrega["valor"], 2, ",", ".") . "<br/>";
            $formasEnvio[$indiceAtual]["valor"] = $regiaoEntrega["valor"];
            $formasEnvio[$indiceAtual]["prazoDias"] = $regiaoEntrega['prazo'];
            $formasEnvio[$indiceAtual]["tipo"] = "MOTOBOY";
        } elseif ($enderecoSelecionado["cidade"] != $empresa["cidade"]) {
            if (chkArray($_SESSION["PEDIDO"], "FRETE")) {
                if ($_SESSION["PEDIDO"]["FRETE"]["TIPO"] == "MOTOBOY") {
                    unset($_SESSION["PEDIDO"]["FRETE"]);
                }
            }
        }



        if (chkArray($_SESSION["PEDIDO"], "FRETE")) {
            $modeloFormaPagamento = $this->loadModel("formapagamento/model-formapagamento");
            if ($_SESSION["PEDIDO"]["FRETE"]["TIPO"] != "FRETE A CALCULAR" && $modelo->valorTotalCarrinho(TRUE, TRUE) != 0) {
                $this->db->tabela = "forma_pagamento";
                $consulta = $this->db->consulta("WHERE status = 'A'");
                $cont = 0;
                while ($linha = mysql_fetch_array($consulta)) {
                    $modeloFormaPagamento->formaPagamento = $linha["classe"];
                    $formasPagamento[$cont]["CLASSE"] = $linha["classe"];
                    $formasPagamento[$cont]["TITULO"] = $linha["titulo"];
                    $formasPagamento[$cont]["semJuros"] = $modeloFormaPagamento->encontrarParcelaSemJuros($linha["classe"]);
                    $formasPagamento[$cont]["parcela"] = $modeloFormaPagamento->calcularJuros($modelo->valorTotalCarrinho(TRUE, TRUE));
                    $cont++;
                }
            } else {
                $formasPagamento[0]["CLASSE"] = "SALVAR";
                $formasPagamento[0]["TITULO"] = "Salvar o Pedido";
            }
        }

// Carrega os arquivos da view
        require ABSPATH . "/view/carrinho/view-topo-finalizar.php";
        require ABSPATH . '/view/carrinho/view-finalizar.php';
        require ABSPATH . "/view/carrinho/view-rodape-finalizar.php";
    }

    public function mudarEndereco() {

        if (isset($_SESSION["PEDIDO"]["FRETE"])) {
            unset($_SESSION["PEDIDO"]["FRETE"]);
        }

        if (chkArray($_POST, "valor")) {
            $_SESSION["PEDIDO"]["ENDERECO"]["ID"] = $_POST["valor"];

            $this->db->tabela = "cliente_endereco";
            $clienteEndereco = $this->db->consultaId($_POST["valor"]);
        } else {
            if (isset($_SESSION["PEDIDO"]["ENDERECO"])) {
                unset($_SESSION["PEDIDO"]["ENDERECO"]["ID"]);
            }
        }

        echo json_encode($retorno);
    }

}
