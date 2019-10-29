<?

/**
 * Modelo para gerenciar as formas de pagamento
 * 
 */
class ModelFormaPagamento extends MainModel {

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
     *  Coeficiente de parcelamento
     * 
     * @var array 
     */
    public $coefParcelamento = array();

    /**
     *  Forma de pagamento
     * 
     * @var array 
     */
    public $formaPagamento = "";

    /**
     * ID do pedido salvo
     *
     * @var int 
     */
    public $idPedido = "";

    /**
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    public function definirCoeficiente() {
        switch ($this->formaPagamento) {
            case "PAGSEGURO":
                $this->coefParcelamento = array(
                    2 => '0.52255',
                    3 => '0.35347',
                    4 => '0.26898',
                    5 => '0.21830',
                    6 => '0.18453',
                    7 => '0.16044',
                    8 => '0.14240',
                    9 => '0.12838',
                    10 => '0.11717',
                    11 => '0.10802',
                    12 => '0.10040'
                );
                break;
            case "BCASH":
                $this->coefParcelamento = array(
                    2 => '1.0400',
                    3 => '1.0608',
                    4 => '1.0818',
                    5 => '1.1032',
                    6 => '1.1250',
                    7 => '1.1475',
                    8 => '1.1704',
                    9 => '1.1940',
                    10 => '1.2175',
                    11 => '1.2419',
                    12 => '1.2665'
                );
                break;
            case "MERCADO_PAGO":
                $this->coefParcelamento = array(
                    2 => '1.301',
                    3 => '1.401',
                    4 => '1.501',
                    5 => '1.607',
                    6 => '1.708',
                    9 => '1.1021',
                    10 => '1.1123',
                    12 => '1.134'
                );
                break;
            case "CIELO":
                break;
        }
    }

    public function encontrarParcelaSemJuros() {

        $formaPagamento = $this->formaPagamento;
        $this->db->tabela = "forma_pagamento";
        $consulta = $this->db->consulta("WHERE classe = '$formaPagamento'");
        $retorno = $this->db->fetch($consulta);
        if (isset($retorno["0"]["parcelaSemJuros"])) {
            return $retorno["0"]["parcelaSemJuros"];
        } else {
            return 1;
        }
    }

    public function calcularJuros($valor) {

        $formaPagamento = $this->formaPagamento;
        $retorno = array();
        $this->definirCoeficiente($formaPagamento);
        $parcelaSemJuros = $this->encontrarParcelaSemJuros($formaPagamento);

        foreach ($this->coefParcelamento as $parcela => $coef) {
            if ($formaPagamento == "PAGSEGURO") {
                if ($parcela <= $parcelaSemJuros) {
                    $valorParcelado = $valor / $parcela;
                } else {
                    $valorParcelado = $valor * $coef;
                }
                if ($valorParcelado > 5) {
                    $retorno[$parcela] = $valorParcelado;
                }
            } else {
                if ($parcela <= $parcelaSemJuros) {
                    $valorParcelado = $valor / $parcela;
                } else {
                    $valorParcelado = ($valor * $coef) / $parcela;
                }
                if ($valorParcelado > 5) {
                    $retorno[$parcela] = $valorParcelado;
                }
            }
        }

        return $retorno;
    }

    public function selecionarPagamento() {

        $this->db->tabela = "pedido";
        $pedido = $this->db->consultaId($this->idPedido);

        $formaPagamento = $this->formaPagamento;
        switch ($formaPagamento) {
            case "PAGSEGURO":
                $this->db->tabela = "forma_pagamento";
                $consulta = $this->db->consulta("WHERE classe = 'PAGSEGURO'");
                $pagamento = $this->db->fetch($consulta);

                require_once ABSPATH . '/model/formapagamento/pagseguro/class.criarpagamentolightbox.php';
                $objeto = new criarPagamentoLightbox();
                $objeto->email = $pagamento[0]["email"];
                $objeto->token = $pagamento[0]["token"];
                $objeto->idPedido = $this->idPedido;


                $this->db->tabela = "pedido_produto";
                $consulta = $this->db->consulta("WHERE pedidoFK = '" . $this->idPedido . "'");
                $conta = 0;
                while ($produtoPedido = mysql_fetch_assoc($consulta)) {
                    $this->db->tabela = "produto_combinacao";
                    $combinacao = $this->db->consultaId($produtoPedido["combinacaoFK"]);

                    $this->db->tabela = "produto";
                    $produto = $this->db->consultaId($combinacao["produtoFK"]);

                    $arrayProdutos[$conta]["id"] = $produto["id"];
                    $arrayProdutos[$conta]["titulo"] = $produto["titulo"];
                    $arrayProdutos[$conta]["quantidade"] = $produtoPedido["quantidade"];
                    $arrayProdutos[$conta]["valor"] = $produtoPedido["valor"];
                    $arrayProdutos[$conta]["valorEmbalagem"] = $produtoPedido["valorEmbalagem"];
                    $conta++;
                }
                $objeto->produtos = $arrayProdutos;
                $objeto->dadosPedido = $pedido;

                $this->db->tabela = "pedido_endereco";
                $consulta = $this->db->consulta("WHERE pedidoFK = '" . $this->idPedido . "'");
                $objeto->enderecoPedido = $this->db->fetch($consulta);

                $codigo = $objeto->main();

                $this->db->tabela = "config_valores";
                $configValores = $this->db->consultaId(1);
                if ($configValores["pagamentoSandbox"] == "N") {
                    return "https://pagseguro.uol.com.br/v2/checkout/payment.html?code=" . $codigo;
                } else {
                    return "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=" . $codigo;
                }
                break;
            case "BCASH":
                break;
            case "MERCADO_PAGO":
                break;
            case "CIELO":
                break;
            case "BOLETO":
                break;
            case "TRANSFERENCIA":
                break;
        }
    }

    public function trazerInfoBoleto() {

        $this->db->tabela = "config_boleto";
        $consulta = $this->db->consulta();
        if (mysql_num_rows($consulta)) {
            $boleto = mysql_fetch_assoc($consulta);
            return $boleto;
        } else {
            return FALSE;
        }
    }

    public function bannerPagamentoPrincipal() {

        $this->db->tabela = "forma_pagamento";
        $consulta = $this->db->consulta("", "ORDER BY ordem ASC");
        if (mysql_num_rows($consulta)) {
            $pagamento = mysql_fetch_assoc($consulta);
            switch ($pagamento["classe"]) {
                case "PAGSEGURO":
                    ob_start();
                    ?>
                    <a href="https://pagseguro.uol.com.br" target="_blank">
                        <img src="<?= IMG_URL ?>/parcelamento-pagseguro.png"/>
                    </a>
                    <?
                    $banner = ob_get_clean();
                    return $banner;
                    break;
                case "BCASH":
                    ob_start();
                    ?>
                    <object type="application/x-shockwave-flash" data="https://a248.e.akamai.net/f/248/96284/168h/www.bcash.com.br/webroot/banners/site/meios/meios_468x60.swf" width="468" height="60">
                        <param name="movie" value="https://a248.e.akamai.net/f/248/96284/168h/www.bcash.com.br/webroot/banners/site/meios/meios_468x60.swf" />
                        <param name="wmode" value="transparent" />
                    </object>
                    <?
                    $banner = ob_get_clean();
                    return $banner;
                    break;
                case "MERCADO_PAGO":
                    break;
                case "CIELO":
                    break;
                case "BOLETO":
                    break;
                case "TRANSFERENCIA":
                    break;
                default:
                    return "<img src='" . HOME_URL . "/view/_image/formaPagamento.png'/>";
                    break;
            }
        } else {
            return "<img src='" . HOME_URL . "/view/_image/formaPagamento.png'/>";
        }
    }

}
