<?php

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerPedido extends MainController {

    /**
     * Carrega a página "/view/home/index.php"
     */
    public function salvarPedido() {

        /**
         * Caso o cliente esteja acessando esta url sem um carrinho ele redireciona para a home
         * isso pode ocorrer quando o cliente der F5 nesta tela ou retornar a ela quando estiver chegado na proxima tela.
         */
        if (!isset($_SESSION["PEDIDO"]["CARRINHO"])) {
            ?>
            <script type="text/javascript">
                alert("Atenção você não possui produtos no carrinho, adicione algum produto para finalizar sua compra!");
                window.open('<?= HOME_URL ?>', '_top');
            </script>
            <?php
            exit();
        }

        $this->title = "Enviando Pedido";
        $modelo = $this->loadModel("pedido/model-pedido");
        $modeloCarrinho = $this->loadModel("carrinho/model-carrinho");
        $modeloFormaPagamento = $this->loadModel("formaPagamento/model-formaPagamento");
        $valorAquisicao = $modeloCarrinho->valorTotalCarrinho(TRUE, TRUE);

        $modelo->parametros = $_POST;
        $modelo->parametros["valotTotalCarrinho"] = $modeloCarrinho->valorTotalCarrinho();

        $respostaBaixaEstoque = $modelo->baixaEstoque();

        if ($respostaBaixaEstoque) {
            $retorno = $modelo->salvarPedido();
            if (is_array($retorno)) {

                foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                    $modeloCarrinho->apagarProdutoCarrinho($_SESSION["CLIENTE"]["id"], $produtoCarrinho["COMBINACAO"]);
                }
                $modelo->apagarSessoes();

                require_once ABSPATH . '/controller/controller-email.php';
                $controladoEmail = new ControllerEmail();
                $controladoEmail->parametros[0] = "pedido";
                $controladoEmail->parametros[1] = $retorno["idPedido"];
                $controladoEmail->index();

                if (!$retorno["liberarPagamento"] || $_POST["formaPagamento"] == "SALVAR") {
                    ?>
                    <script type="text/javascript">
                        window.open('<?= HOME_URL . "/pedido/compra-efetuada/" . cr($retorno["idPedido"]) . "/obrigado-pela-compra.html#conteudo" ?>', '_top');
                    </script>
                    <?php
//                    header("Location: " . HOME_URL . "/pedido/compra-efetuada/" . cr($retorno["idPedido"]) . "/obrigado-pela-compra.html#conteudo");
                } else {
                    $modeloFormaPagamento->idPedido = $retorno["idPedido"];
                    $modeloFormaPagamento->formaPagamento = $_POST["formaPagamento"];
                    $linkRedirecionamento = $modeloFormaPagamento->selecionarPagamento();
                }
            } elseif (!$retorno) {
                header("Location: " . HOME_URL . "/carrinho/meus-produtos/detalhes.html#conteudo");
            }
        } else {
            ?>
            <script type="text/javascript">
                alert("Atenção algum de seus produtos se encontra sem estoque no momento, volte ao carrinho e revise sua compra!");
                window.open('<?= HOME_URL . "/carrinho/meus-produtos/detalhes.html#conteudo" ?>', '_top');
            </script>
            <?php
        }

        $paginas[0]["link"] = "/carrinho/meus-produtos/detalhes.html#conteudo";
        $paginas[0]["titulo"] = "Meus Produtos";
        $paginas[1]["link"] = "/carrinho/finalizar/finalizar-compra.html";
        $paginas[1]["titulo"] = "Finalizar Compra";

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . '/view/pedido/view-redirecionando.php';
        require ABSPATH . "/view/_include/footer.php";
    }

    public function enviarPagamento() {

        $this->title = "Enviando Pedido";
        $modeloFormaPagamento = $this->loadModel("formaPagamento/model-formaPagamento");
        $modeloFormaPagamento->idPedido = dr($this->parametros[0]);
        $modeloFormaPagamento->formaPagamento = $_POST["formaPagamento"];
        $linkRedirecionamento = $modeloFormaPagamento->selecionarPagamento();


        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . '/view/pedido/view-redirecionando.php';
        require ABSPATH . "/view/_include/footer.php";
    }

    public function compraEfetuada() {

        $this->title = "Agradecemos pela sua compra!";
        $modelo = $this->loadModel("pedido/model-pedido");
        $modeloProduto = $this->loadModel("produto/model-produto");

        $idPedido = dr($this->parametros[0]);
        $numeroPedido = str_pad($idPedido, 10, "0", STR_PAD_LEFT);
        $pedido = $modelo->detalharPedido($idPedido, $_SESSION["CLIENTE"]["id"]);

        $this->db->tabela = "pedido_pagamento";
        $consulta = $this->db->consulta("WHERE pedidoFK = '$idPedido'", "ORDER BY data DESC");
        if (mysql_num_rows($consulta)) {
            $pedidoPagamento = mysql_fetch_assoc($consulta);
        }


        $paginas[0]["link"] = "/carrinho/meus-produtos/detalhes.html#conteudo";
        $paginas[0]["titulo"] = "Meus Produtos";
        $paginas[1]["link"] = "/carrinho/finalizar/finalizar-compra.html";
        $paginas[1]["titulo"] = "Finalizar Compra";


        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . '/view/pedido/view-pedidoRealizado.php';
        require ABSPATH . "/view/_include/footer.php";
    }

    public function gerarBoleto() {

        $this->title = "Gerar Boleto";
        $modelo = $this->loadModel("formaPagamento/model-formaPagamento");
        $boleto = $modelo->trazerInfoBoleto();

        $this->db->tabela = "pedido";
        $pedido = $this->db->consultaId(dr($this->parametros[0]));

        $validaCliente = FALSE;
        if (chkArray($_SESSION, "CLIENTE")) {
            if ($_SESSION["CLIENTE"]["id"] == $pedido["clienteFK"]) {
                $validaCliente = TRUE;
            }
        }

        if ($validaCliente) {
            $this->db->tabela = "cliente";
            $cliente = $this->db->consultaId($pedido["clienteFK"]);

            $this->db->tabela = "boleto";
            $consultaBoletoGravado = $this->db->consulta("", "ORDER BY id DESC");
            if (mysql_num_rows($consultaBoletoGravado)) {
                $boletoGravado = mysql_fetch_assoc($consultaBoletoGravado);
                $numSequencial = $boletoGravado["id"] + 1;
            } else {
                $numSequencial = 1;
            }

            $totalBoleto = ($pedido["valorTotal"] + $pedido["valorFrete"]) - $pedido["valorDesconto"];

            // Carrega os arquivos da view
            require ABSPATH . "/enum.php";
            require ABSPATH . "/_utilitarios/boletos/boleto_" . $boleto["classe"] . ".php";

            $this->db->tabela = "boleto";
            $consulta = $this->db->consulta("WHERE nosso_numero = '" . $dadosboleto["nosso_numero"] . "'");
            if (!mysql_num_rows($consulta)) {
                $parametros["pedidoFK"] = $pedido["id"];
                $parametros["clienteFK"] = $cliente["id"];
                $parametros["status"] = "AGUARDANDO";
                $parametros["nosso_numero"] = $dadosboleto["nosso_numero"];
                $parametros["valor"] = $dadosboleto["valor_boleto"];
                $parametros["vencimento"] = str_replace("/", "-", $dadosboleto["data_vencimento"]);
                $parametros["data"] = date("d-m-Y");
                $this->db->importArray($parametros);
                $this->db->persist();
            }
        } else {
            
        }
    }

}
