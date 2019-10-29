<?

/**
 * Modelo para gerenciar os pedido
 * 
 */
class ModelPedido extends MainModel {

    /**
     * $resultadoPorPagina
     * 
     * Receberá o número de resultados por página para configurar
     * a listagem e também para ser utilizada na paginação
     * 
     * @access public
     */
    public $resultadoPorPagina = 12;

    /**
     * $ordenacao
     * 
     * Receberá a ordenação dos resultados da consulta
     * 
     * @access public
     */
    public $ordenacao = "";

    /**
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    public function detalharPedido($idPedido, $idCliente) {

        $retornoPedido = array();

        $this->db->tabela = "pedido";
        $pedido = $this->db->consultaId($idPedido);

        $this->db->tabela = "cliente";
        $cliente = $this->db->consultaId($idCliente);

        $this->db->tabela = "pedido_endereco";
        $consulta = $this->db->consulta("WHERE pedidoFK = '$idPedido'", "ORDER BY id DESC");
        $enderecoPedido = $this->db->fetch($consulta);

        $this->db->tabela = "pedido_status";
        $consulta = $this->db->consulta("WHERE pedidoFK = '$idPedido'", "ORDER BY data DESC");
        $statusPedido = $this->db->fetch($consulta);

        $this->db->tabela = "pedido_produto";
        $consulta = $this->db->consulta("WHERE pedidoFK = '$idPedido'");
        $produtoPedido = $this->db->fetchAll($consulta);

        $this->db->tabela = "pedido_entrega";
        $consulta = $this->db->consulta("WHERE pedidoFK = '$idPedido'");
        $rastreioPedido = $this->db->fetchAll($consulta);

        if ($idCliente == $pedido["clienteFK"]) {

            $retornoPedido["id"] = $pedido["id"];
            $retornoPedido["data"] = dataHoraSite($pedido["data"]);
            $retornoPedido["valorTotal"] = "R$ " . number_format($pedido["valorTotal"], 2, ",", ".");
            if ($pedido["valorDesconto"]) {
                $retornoPedido["valorDesconto"] = "R$ " . number_format($pedido["valorDesconto"], 2, ",", ".");
            }
            if ($pedido["prazoEstimado"]) {
                $retornoPedido["prazoEstimado"] = $pedido["prazoEstimado"] . " Dias úteis.";
            } else {
                $retornoPedido["prazoEstimado"] = "A calcular.";
            }

            $retornoPedido["clienteEndereco"] = $cliente["endereco"];
            $retornoPedido["clienteNumero"] = $cliente["numero"];
            $retornoPedido["clienteComplemento"] = $cliente["complemento"];
            $retornoPedido["clienteBairro"] = $cliente["bairro"];
            $retornoPedido["clienteCEP"] = $cliente["cep"];
            $retornoPedido["clienteCidade"] = $cliente["cidade"];
            $retornoPedido["clienteEstado"] = $cliente["estado"];

            $retornoPedido["pedidoDestinatario"] = $enderecoPedido["0"]["destinatario"];
            $retornoPedido["pedidoEndereco"] = $enderecoPedido["0"]["endereco"];
            $retornoPedido["pedidoNumero"] = $enderecoPedido["0"]["numero"];
            $retornoPedido["pedidoComplemento"] = $enderecoPedido["0"]["complemento"];
            $retornoPedido["pedidoBairro"] = $enderecoPedido["0"]["bairro"];
            $retornoPedido["pedidoCEP"] = $enderecoPedido["0"]["cep"];
            $retornoPedido["pedidoCidade"] = $enderecoPedido["0"]["cidade"];
            $retornoPedido["pedidoEstado"] = $enderecoPedido["0"]["estado"];

            if ($pedido["freteGratis"] != "S" || $pedido["tipoFrete"] == "RETIRADA NA LOJA") {
                if ($pedido["valorFrete"]) {
                    $retornoPedido["valorFrete"] = "R$ " . number_format($pedido["valorFrete"], 2, ",", ".");
                } else {
                    if ($pedido["tipoFrete"] == "RETIRADA NA LOJA") {
                        $retornoPedido["valorFrete"] = "N/A";
                    } else {
                        $retornoPedido["valorFrete"] = "A calcular";
                    }
                }
                $retornoPedido["tipoFrete"] = $pedido["tipoFrete"];
                $retornoPedido["valorFinal"] = "R$ " . number_format($pedido["valorFrete"] + $pedido["valorTotal"] - $pedido["valorDesconto"], 2, ",", ".");
            } else {
                $retornoPedido["valorFrete"] = "GRÁTIS";
                $retornoPedido["tipoFrete"] = "PAC";
                $retornoPedido["valorFinal"] = "R$ " . number_format($pedido["valorTotal"] - $pedido["valorDesconto"], 2, ",", ".");
            }

            switch ($statusPedido["0"]["status"]) {
                case "AGUARDANDO":
                    $retornoPedido["valorStatus"] = 20;
                    break;
                case "CALCULANDO FRETE":
                    $retornoPedido["valorStatus"] = 20;
                    break;
                case "PAGO":
                    $retornoPedido["valorStatus"] = 40;
                    break;
                case "EMBALANDO":
                    $retornoPedido["valorStatus"] = 60;
                    break;
                case "DESPACHADO":
                    $retornoPedido["valorStatus"] = 80;
                    break;
                case "ENTREGUE":
                    $retornoPedido["valorStatus"] = 100;
                    break;
                case "DISPUTA":
                    $retornoPedido["valorStatus"] = 0;
                    break;
                case "CANCELADO":
                    $retornoPedido["valorStatus"] = 0;
                    break;
                case "CREDITADO":
                    $retornoPedido["valorStatus"] = 20;
                    break;
                case "DEVOLVIDO":
                    $retornoPedido["valorStatus"] = 0;
                    break;
                default:
                    $retornoPedido["valorStatus"] = 0;
                    break;
            }

            $retornoPedido["produtoPedido"] = $produtoPedido;
            $retornoPedido["rastreioPedido"] = $rastreioPedido;

            return $retornoPedido;
        } else {
            return FALSE;
        }
    }

    public function salvarPedido() {

        // SALVANDO PEDIDO //
        $this->db->tabela = "pedido";
        $parametros = array();
        $parametros["clienteFK"] = $_SESSION["CLIENTE"]["id"];
        $parametros["tipoFrete"] = $this->parametros["formaEntrega"];
        $parametros["data"] = date("d-m-Y H:i:s");
        $parametros["valorTotal"] = $this->parametros["valotTotalCarrinho"];
        $freteGratis = FALSE;
        if (isset($_SESSION["PEDIDO"]["FRETE"])) {
            if ($_SESSION["PEDIDO"]["FRETE"]["VALOR"] == "GRÁTIS") {
                $freteGratis = TRUE;
            } else {
                $parametros["valorFrete"] = $_SESSION["PEDIDO"]["FRETE"]["VALOR"];
            }
        }
        if (isset($_SESSION["PEDIDO"]["CUPOM"])) {
            $modeloCarrinho = $this->controller->loadModel("carrinho/model-carrinho");
            $parametros["valorDesconto"] = $modeloCarrinho->verificaCupomDesconto($this->parametros["valotTotalCarrinho"]);
            $parametros["cupomFK"] = $_SESSION["PEDIDO"]["CUPOM"]["ID"];
        }
//        $parametros["observacao"] = $this->parametros["observacao"];

        if ($this->parametros["formaEntrega"] == "RETIRADA NA LOJA" || $this->parametros["formaEntrega"] == "FRETE GRÁTIS - PAC" || $freteGratis) {
            $parametros["freteGratis"] = "S";
        } else {
            $parametros["freteGratis"] = "N";
        }
        $parametros["prazoEstimado"] = $_SESSION["PEDIDO"]["FRETE"]["PRAZO"];
        $this->db->importArray($parametros);
        $idPedido = $resultado = $this->db->persist();
        // FIM - SALVANDO PEDIDO //

        if (!is_array($resultado)) {

            // SALVANDO CAIXAS DO PEDIDO //
            if (isset($_SESSION["CAIXAS"])) {
                $posicaoCaixa = 0;
                foreach ($_SESSION["CAIXAS"] as $ind => $caixa) {
                    $posicaoCaixa++;
                    $parametros = array();
                    $parametros["pedidoFK"] = $idPedido;
                    $parametros["altura"] = $caixa["altura"];
                    $parametros["largura"] = $caixa["largura"];
                    $parametros["profundidade"] = $caixa["profundidade"];
                    $parametros["peso"] = $caixa["peso"];

                    $this->db->tabela = "pedido_caixa";
                    $this->db->importArray($parametros);
                    $idCaixa = $resultado = $this->db->persist();
                    if (!is_array($resultado)) {
                        if (isset($_SESSION["PRODUTOEMPACOTADO"]) && ($this->parametros["formaEntrega"] == "PAC" || $this->parametros["formaEntrega"] == "SEDEX" || $this->parametros["formaEntrega"] == "ESEDEX" || $this->parametros["formaEntrega"] == "CARTA REGISTRADA")) {
                            foreach ($_SESSION["PRODUTOEMPACOTADO"] as $ind => $produtoEmpacotado) {
                                if ($produtoEmpacotado["posicaoCaixa"] == $posicaoCaixa) {
                                    $parametros = array();
                                    $parametros["caixaFK"] = $idCaixa;
                                    $parametros["produtoCombinacaoFK"] = $produtoEmpacotado["produtoCombinacaoFK"];
                                    $parametros["posicaoZ"] = $produtoEmpacotado["posicaoZ"];
                                    $parametros["posicaoY"] = $produtoEmpacotado["posicaoY"];
                                    $parametros["posicaoX"] = $produtoEmpacotado["posicaoX"];

                                    $this->db->tabela = "pedido_caixa_produto";
                                    $this->db->importArray($parametros);
                                    $this->db->persist();
                                }
                            }
                        }
                    }
                }
            }
            // FIM - SALVANDO CAIXAS PEDIDO //
            // 
            // 
            // SALVANDO ENDEREÇO DO PEDIDO //
            $parametros = array();
            $parametros["pedidoFK"] = $idPedido;
            if (isset($_SESSION["PEDIDO"]["ENDERECO"]["ID"])) {
                $this->db->tabela = "cliente_endereco";
                $endereco = $this->db->consultaId($_SESSION["PEDIDO"]["ENDERECO"]["ID"]);

                $parametros["destinatario"] = ($endereco["destinatario"]) ? $endereco["destinatario"] : $_SESSION["CLIENTE"]["nome"];
                $parametros["endereco"] = $endereco["endereco"];
                $parametros["numero"] = $endereco["numero"];
                $parametros["complemento"] = $endereco["complemento"];
                $parametros["bairro"] = $endereco["bairro"];
                $parametros["cidade"] = $endereco["cidade"];
                $parametros["cep"] = $endereco["cep"];
                $parametros["estado"] = $endereco["estado"];
            } else {
                $parametros["destinatario"] = $_SESSION["CLIENTE"]["nome"];
                $parametros["endereco"] = $_SESSION["CLIENTE"]["endereco"];
                $parametros["numero"] = $_SESSION["CLIENTE"]["numero"];
                $parametros["complemento"] = $_SESSION["CLIENTE"]["complemento"];
                $parametros["bairro"] = $_SESSION["CLIENTE"]["bairro"];
                $parametros["cidade"] = $_SESSION["CLIENTE"]["cidade"];
                $parametros["cep"] = $_SESSION["CLIENTE"]["cep"];
                $parametros["estado"] = $_SESSION["CLIENTE"]["estado"];
            }

            $this->db->tabela = "pedido_endereco";
            $this->db->importArray($parametros);
            $this->db->persist();
            // FIM - SALVANDO ENDEREÇO DO PEDIDO //
            // 
            // 
            // SALVANDO PAGAMENTO DO PEDIDO //
            $parametros = array();
            $parametros["pedidoFK"] = $idPedido;
            $parametros["status"] = "AGUARDANDO";
            if ($this->parametros["formaPagamento"] != "SALVAR") {
                $parametros["formaPagamento"] = $this->parametros["formaPagamento"];
            }
            $parametros["data"] = date("d-m-Y H:i:s");

            $this->db->tabela = "pedido_pagamento";
            $this->db->importArray($parametros);
            $this->db->persist();
            // FIM - SALVANDO PAGAMENTO DO PEDIDO //
            // 
            // 
            // SALVANDO PRODUTOS DO PEDIDO //
            foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                $this->db->tabela = "produto_combinacao";
                $combinacao = $this->db->consultaId($produtoCarrinho["COMBINACAO"]);
                if (isset($_SESSION["CLIENTE"]) && isset($combinacao["valorAtacado"])) {
                    if (chkArray($_SESSION["CLIENTE"], "tipo") == "JURIDICA") {
                        $combinacao["valorPor"] = $combinacao["valorAtacado"];
                    }
                }

                $parametros = array();
                $parametros["pedidoFK"] = $idPedido;
                $parametros["combinacaoFK"] = $produtoCarrinho["COMBINACAO"];
                $parametros["quantidade"] = $produtoCarrinho["QTD"];
                $parametros["valor"] = $combinacao["valorPor"];
                if (chkArray($produtoCarrinho, 'VLR_PRESENTE')) {
                    $parametros["valorEmbalagem"] = $produtoCarrinho['VLR_PRESENTE'];
                    $parametros["descEmbalagem"] = $produtoCarrinho['MSG_PRESENTE'];
                }

                $this->db->tabela = "pedido_produto";
                $this->db->importArray($parametros);
                $this->db->persist();
            }
            // FIM - SALVANDO PRODUTOS DO PEDIDO //
            // 
            // 
            // SALVANDO STATUS DO PEDIDO //
            $parametros = array();
            $parametros["pedidoFK"] = $idPedido;
            if ($_SESSION["PEDIDO"]["FRETE"]["TIPO"] == "FRETE A CALCULAR") {
                $parametros["status"] = "CALCULANDO FRETE";
                $retorno["liberarPagamento"] = FALSE;
            } elseif ($this->parametros["formaPagamento"] == "BOLETO" || $this->parametros["formaPagamento"] == "TRANSFERENCIA") {
                $parametros["status"] = "AGUARDANDO";
                $retorno["liberarPagamento"] = FALSE;
            } else {
                $parametros["status"] = "AGUARDANDO";
                $retorno["liberarPagamento"] = TRUE;
            }
            $parametros["observacao"] = "Pedido feito pelo site";
            $parametros["data"] = date("d-m-Y H:i:s");

            $this->db->tabela = "pedido_status";
            $this->db->importArray($parametros);
            $this->db->persist();
            // FIM - SALVANDO STATUS DO PEDIDO //
            // 
            // 
            // MARCAR O CUPOM COMO RESGATADO CASO NECESSÁRIO //
            if (isset($_SESSION["PEDIDO"]["CUPOM"])) {
                $this->db->tabela = "cupom";
                $cupom = $this->db->consultaId($_SESSION["PEDIDO"]["CUPOM"]["ID"]);
                if ($cupom["descontoGeral"] == "N") {
                    $parametros["pedidoFK"] = $idPedido;
                    $parametros["status"] = "RESGATADO";
                    $this->db->importArray($parametros);
                    $this->db->persist($_SESSION["PEDIDO"]["CUPOM"]["ID"]);
                }
            }
            // FIM - MARCAR O CUPOM COMO RESGATADO CASO NECESSÁRIO //

            $retorno["idPedido"] = $idPedido;

//            require_once ABSPATH . '/_utilitarios/google-analytics/medir-compras.php';
            return $retorno;
        } else {
            return FALSE;
        }
    }

    public function baixaEstoque() {

        $this->db->tabela = "produto_combinacao";
        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
            if ($produtoCarrinho["COMBINACAO"]) {
                $consulta = $this->db->consulta("WHERE id = '" . $produtoCarrinho["COMBINACAO"] . "' AND estoque < '" . $produtoCarrinho["QTD"] . "'");
                if (mysql_num_rows($consulta)) {
                    return FALSE;
                }
            }
        }

        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
            if ($produtoCarrinho["COMBINACAO"]) {
                $combinacao = $this->db->consultaId($produtoCarrinho["COMBINACAO"]);
                $persist["estoque"] = $combinacao["estoque"] - $produtoCarrinho["QTD"];
                $this->db->importArray($persist);
                $this->db->persist($produtoCarrinho["COMBINACAO"]);
            }
        }

        return TRUE;
    }

    public function apagarSessoes() {

        $_SESSION["PEDIDO"] = array();
        unset($_SESSION["PEDIDO"]);

        if (isset($_SESSION["CAIXAS"])) {
            unset($_SESSION["CAIXAS"]);
        }
        if (isset($_SESSION["PRODUTOEMPACOTADO"])) {
            unset($_SESSION["PRODUTOEMPACOTADO"]);
        }
    }

}
