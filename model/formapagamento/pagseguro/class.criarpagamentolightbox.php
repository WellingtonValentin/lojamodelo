<?php
require_once ABSPATH . '/_utilitarios/PagSeguroLibrary/PagSeguroLibrary.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of criarPagamentoLightbox
 *
 * @author Desenvolvimento 6
 */
class criarPagamentoLightbox {

    /**
     * Token do pagamento configurado através do site do pagseguro
     *
     * @var string 
     */
    public $token = "";

    /**
     * E-mail cadastrado no pagseguro
     *
     * @var string 
     */
    public $email = "";

    /**
     * ID do pedido salvo
     *
     * @var int 
     */
    public $idPedido = "";

    /**
     * Produtos do pedido
     *
     * @var array 
     */
    public $produtos = array();

    /**
     * Dados do pedido
     *
     * @var array
     */
    public $dadosPedido = array();

    /**
     * Dados do pedido
     *
     * @var array
     */
    public $enderecoPedido = array();

    /**
     * Dados do pedido
     *
     * @var array
     */
    public $urlRedirecionamento = "";

    public function main() {

        $this->urlRedirecionamento = HOME_URL . "/pedido/compra-efetuada/" . cr($this->idPedido) . "/obrigado-pela-compra.html#conteudo";

        // Instanciando objeto do pagseguro
        $paymentRequest = new PagSeguroPaymentRequest();

        // Set the currency
        $paymentRequest->setCurrency("BRL");

        // Add an item for this payment request
        foreach ($this->produtos as $ind => $produto) {
            $paymentRequest->addItem($produto["id"], $produto["titulo"], $produto["quantidade"], number_format($produto["valor"] + $produto["valorEmbalagem"], 2, ".", ""));
        }

        // Set a reference code for this payment request, it is useful to identify this payment
        // in future notifications.
        $paymentRequest->setReference($this->idPedido);

        if (isset($this->dadosPedido["valorDesconto"])) {
            $desconto = "-" . number_format($this->dadosPedido["valorDesconto"], 2, ".", "");
            $paymentRequest->setExtraAmount($desconto);
        }

        // Set shipping information for this payment request
        if (
                $this->dadosPedido["tipoFrete"] == "PAC" ||
                $this->dadosPedido["tipoFrete"] == "SEDEX" ||
                $this->dadosPedido["tipoFrete"] == "ESEDEX" ||
                $this->dadosPedido["tipoFrete"] == "CARTA REGISTRADA" ||
                $this->dadosPedido["tipoFrete"] == "TRANSPORTADORA" ||
                $this->dadosPedido["tipoFrete"] == "MOTOBOY"
        ) {
            $paymentRequest->setShippingCost(number_format($this->dadosPedido["valorFrete"], 2, ".", ""));
        }

        $paymentRequest->setShippingAddress(
                $this->enderecoPedido[0]["cep"], $this->enderecoPedido[0]["endereco"], $this->enderecoPedido[0]["numero"], $this->enderecoPedido[0]["complemento"], $this->enderecoPedido[0]["bairro"], $this->enderecoPedido[0]["cidade"], $this->enderecoPedido[0]["estado"], "BRA"
        );
        $CODIGO_PAC = PagSeguroShippingType::getCodeByType('NOT_SPECIFIED');
        $paymentRequest->setShippingType($CODIGO_PAC);

        // Set your customer information.
        switch ($_SESSION["CLIENTE"]["tipo"]) {
            case "FISICA":
                $tipoDoc = "CPF";
                break;
            case "TESTE_FISICA":
                $tipoDoc = "CPF";
                break;
            default:
                $tipoDoc = "CNPJ";
                break;
        }
        $sender = new PagSeguroSender(); // objeto PagSeguroSender  
//        $chkNome = explode(" ", $_SESSION["CLIENTE"]["nome"]);
//        if (count($chkNome) > 1) {
//            $sender->setName($_SESSION["CLIENTE"]["nome"]);
//        } elseif($_SESSION["CLIENTE"]["sobrenome"]) {
//            $sender->setName($_SESSION["CLIENTE"]["nome"] . " " . $_SESSION["CLIENTE"]["sobrenome"]);
//        } 
        $sender->setEmail($_SESSION["CLIENTE"]["email"]);
        if (isset($_SESSION["CLIENTE"]["telefone1"])) {
            $telefone = str_replace(array("(", ")"), "", $_SESSION["CLIENTE"]["telefone1"]);
            $telefone = explode(" ", $telefone);
            $codigoArea = $telefone[0];
            if (isset($telefone[1])) {
                $numero = str_replace("-", "", $telefone[1]);
            } elseif (isset($telefone[0])) {
                $numero = str_replace("-", "", $telefone[0]);
            } else {
                $numero = str_replace("-", "", $telefone);
            }
            $sender->setPhone((int) $codigoArea, (int) $numero);
        }
        $paymentRequest->setSender($sender);

        // Set the url used by PagSeguro to redirect user after checkout process ends
        $paymentRequest->setRedirectUrl($this->urlRedirecionamento);

        try {

            /*
             * #### Credentials #####
             * Replace the parameters below with your credentials
             * You can also get your credentials from a config file. See an example:
             * $credentials = PagSeguroConfig::getAccountCredentials();
             */

            // seller authentication
            $credentials = new PagSeguroAccountCredentials($this->email, $this->token);

            // application authentication
            //$credentials = PagSeguroConfig::getApplicationCredentials();
            //$credentials->setAuthorizationCode("E231B2C9BCC8474DA2E260B6C8CF60D3");
            // Register this payment request in PagSeguro to obtain the checkout code
            $onlyCheckoutCode = true;
            $code = $paymentRequest->register($credentials, $onlyCheckoutCode);

            $code = self::printPaymentUrl($code);
            return $code;
        } catch (PagSeguroServiceException $e) {
            die($e->getMessage());
        }
    }

    public function printPaymentUrl($code) {
        if ($code) {
            $this->db = new DB();
            $this->db->tabela = "config_valores";
            $configValores = $this->db->consultaId(1);
            ?>
            <? if ($configValores["pagamentoSandbox"] == "N") { ?>
                <script src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js" type="text/javascript"></script>
            <? } else { ?>
                <script src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js" type="text/javascript"></script>
            <? } ?>
            <script>
                checkoutCode = '<?= $code ?>';
                isOpenLightbox = PagSeguroLightbox({
                    code: checkoutCode
                }, {
                    success: function (transactionCode) {
                        location.href = "<?= $this->urlRedirecionamento ?>";
                    },
                    abort: function () {
                        location.href = "<?= $this->urlRedirecionamento ?>";
                    }
                });
                if (!isOpenLightbox) {
            <? if ($configValores["pagamentoSandbox"] == "N") { ?>
                        location.href = "https://pagseguro.uol.com.br/v2/checkout/payment.html?code=" + code;
            <? } else { ?>
                        location.href = "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=" + code;
            <? } ?>
                }
            </script>
            <?
            return $code;
        }
    }

}
