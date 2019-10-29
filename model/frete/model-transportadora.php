<?php

class ModelTransportadora extends MainModel {

    /**
     * Valores vindos do controler e representam totais calculados
     */
    public $valor = "";
    public $peso = "";
    public $cep = "";

    /**
     * Variaveis que serão utilizadas para encontrar taxas e valores
     */
    public $cidade = "";
    public $estado = "";
    public $taxas = array();
    public $tabela = array();
    public $fluvial = FALSE;
    public $erro = FALSE;

    /**
     * Taxas aplicáveis ao cálculo
     */
    public $frete_peso = "";
    public $tx_fv = "";
    public $tx_despacho = "";
    public $tx_tas = "";
    public $tx_pedagio = "";
    public $tx_suframa = "";
    public $tx_portuaria = "";
    public $tx_gris = "";
    public $tx_fluvial = "";
    public $tx_seguro_aquaviario = "";
    public $tx_trt1 = "";
    public $tx_trt2 = "";
    public $tx_icms = "";

    /**
     * Valores relevantes calculados pela função
     */
    public $prazo = "";
    public $total = "";

    /**
     * Construtor da classe
     * 
     * Configura o banco, controlador, os parâmetros e o dados do usuário
     * 
     * @access public
     * @param Objeto $db Objeto da nossa conexão
     * @param Objeto $controller Objeto do controlador
     */
    function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
        require_once ABSPATH . '/enum.php';
    }

    public function montaCalculo() {
        $endereco = file_get_contents('http://byteabyte.com.br/webservice/cep/?pass=123321456&cep=' . $this->cep);
        $endereco = (array) json_decode($endereco);
        $this->cidade = utf8_decode($endereco['cidade']);
        $this->estado = $endereco['uf'];
        
        if ($endereco['status'] == "erro") {
            $this->erro = TRUE;
            return ;
        }
        

        $this->checaFluvial();
        $this->db->tabela = "fdx_regiao_metropolitana";
        $consulta = $this->db->consulta("WHERE municipio LIKE '%$this->cidade%' AND uf = '$this->estado'");
        if (mysql_num_rows($consulta)) {
            $this->db->tabela = "estado";
            $consulta = $this->db->consulta("WHERE uf = '$this->estado'");
            $linha = mysql_fetch_assoc($consulta);
            $this->cidade = $linha['capital'];
        }

        $this->db->tabela = "fdx_tabela_frete";
        $consulta = $this->db->consulta("WHERE uf = '$this->estado' AND cidade = '$this->cidade'");
        if (mysql_num_rows($consulta)) {
            $this->tabela = mysql_fetch_assoc($consulta);
        } else {
            if ($this->fluvial) {
                $consulta = $this->db->consulta("WHERE uf = '$this->estado' AND cidade = 'FLUVIAL'");
                $this->tabela = mysql_fetch_assoc($consulta);
            } else {
                $consulta = $this->db->consulta("WHERE uf = '$this->estado' AND cidade = 'DEMAIS CIDADES'");
                $this->tabela = mysql_fetch_assoc($consulta);
            }
        }

        $this->aplicaTaxaFixa();
        $this->aplicaSuframa();
        $this->aplicaTarifaPortuaria();
        $this->aplicaFluvial();
        $this->aplicaSeguroFluvial();
        $this->aplicaTRT1e2();

        $this->db->tabela = "fdx_tabela_frete_faixa_peso";
        $consultaFaixa = $this->db->consulta("WHERE tabela_frete_fk = '" . $this->tabela['id'] . "' and " . $this->peso . " BETWEEN peso_minimo and peso_maximo");
        if (mysql_num_rows($consultaFaixa)) {
            $faixa = mysql_fetch_assoc($consultaFaixa);
            $this->frete_peso = $faixa['valor'];
        } else {
            $this->frete_peso = $this->tabela['excedente_por_kilo'] * $this->peso;
        }
        $this->tx_fv = ($this->valor * $this->tabela['tx_fv']) / 100;

        $this->total = $this->frete_peso + $this->tx_despacho + $this->tx_fluvial + $this->tx_seguro_aquaviario + $this->tx_fv + $this->tx_gris + $this->tx_pedagio + $this->tx_portuaria + $this->tx_suframa + $this->tx_tas + $this->tx_trt1 + $this->tx_trt2;
        $this->aplicaICMS();
        $this->total += $this->tx_icms;

        $this->db->tabela = "fdx_prazo";
        $consulta = $this->db->consulta("WHERE cidade LIKE '%$this->cidade%' AND uf = '$this->estado'");
        $linha = mysql_fetch_assoc($consulta);

        $this->db->tabela = "fdx_taxa";
        $this->taxas = $this->db->consultaId($this->tabela['taxa_fk']);
        $this->prazo = $linha['prazo'] + $this->taxas['prazoExtra'];
    }

    /**
     * Checando se o destinatário é de uma cidade de acesso apenas fluvial
     */
    public function checaFluvial() {

        $this->db->tabela = "fdx_cidade_fluvial";
        $consulta = $this->db->consulta("WHERE cidade LIKE '%$this->cidade%' AND uf = '$this->estado'");
        if (mysql_num_rows($consulta)) {
            $this->fluvial = TRUE;
        }
    }

    public function aplicaTaxaFixa() {
        // Trazendo tabela de taxas
        $this->db->tabela = "fdx_taxa";
        $this->taxas = $this->db->consultaId($this->tabela['taxa_fk']);

        // Aplicando taxas obrigatórias
        $this->tx_despacho = $this->taxas['tx_despacho'];
        $this->tx_gris = ($this->valor * $this->taxas['tx_gris']) / 100;
        if ($this->tx_gris < $this->taxas['tx_gris_minimo']) {
            $this->tx_gris = $this->taxas['tx_gris_minimo'];
        }
        $this->tx_tas = $this->taxas['tx_tas'];
        $this->tx_pedagio = (ceil($this->peso / 100)) * $this->taxas['tx_pedagio'];
    }

    public function aplicaSuframa() {
        $estadosSuframa = array('AC', 'AM', 'AP', 'RR', 'RO');
        if (in_array($this->estado, $estadosSuframa)) {
            $this->tx_suframa = $this->taxas['tx_suframa'];
        }
    }

    public function aplicaTarifaPortuaria() {
        $estadoPortuaria = array('AM', 'RR');
        if ($this->fluvial && in_array($this->estado, $estadoPortuaria)) {
            $this->tx_portuaria = (ceil($this->peso / 1000)) * $this->taxas['tx_portuaria'];
            if ($this->tx_portuaria < $this->taxas['tx_portuaria_minimo']) {
                $this->tx_portuaria = $this->taxas['tx_portuaria_minimo'];
            }
        }
    }

    public function aplicaFluvial() {
        if ($this->fluvial) {
            $this->tx_fluvial = ($this->valor * $this->taxas['tx_fluvial']) / 100;
        }
    }

    public function aplicaSeguroFluvial() {
        $estadoPortuaria = array('AM', 'RR');
        if ($this->fluvial && in_array($this->estado, $estadoPortuaria)) {
            $this->tx_seguro_aquaviario = ($this->valor * $this->taxas['tx_seguro_aquaviario']) / 100;
        }
    }

    public function aplicaTRT1e2() {
        $this->db->tabela = "fdx_cidade_trt";
        $consulta = $this->db->consulta("WHERE cidade LIKE '%$this->cidade%' AND uf = '$this->estado'");
        if (mysql_num_rows($consulta)) {

            $estadoTRT2 = array('RJ', 'SP');
            if (in_array($this->estado, $estadoTRT2)) {
                $this->tx_trt2 = ($this->valor * $this->taxas['tx_trt2']) / 100;
                if ($this->tx_trt2 < $this->taxas['tx_trt2_minimo']) {
                    $this->tx_trt2 = $this->taxas['tx_trt2_minimo'];
                }
            } else {
                $this->tx_trt1 = ($this->valor * $this->taxas['tx_trt1']) / 100;
                if ($this->tx_trt1 < $this->taxas['tx_trt1_minimo']) {
                    $this->tx_trt1 = $this->taxas['tx_trt1_minimo'];
                }
            }
        }
    }

    public function aplicaICMS() {
        $this->db->tabela = "fdx_icms";
        $consulta = $this->db->consulta("WHERE uf = '$this->estado'");
        if (mysql_num_rows($consulta) AND $this->estado != "PR") {
            $campo = mysql_fetch_assoc($consulta);
            $this->taxas['tx_icms'] = $campo['valor'];
            $this->tx_icms = ($this->total * $campo['valor']) / 100;
        }
    }

    public function encontraPeso($dimencoes = array(), $carrinho = FALSE) {
        $endereco = file_get_contents('http://byteabyte.com.br/webservice/cep/?pass=123321456&cep=' . $this->cep);
        $endereco = (array) json_decode($endereco);

        if (!$carrinho) {
            $cubagem = ($dimencoes['altura'] / 100) * ($dimencoes['largura'] / 100) * ($dimencoes['profundidade'] / 100) * 300;
            if ($cubagem > $this->peso && $endereco['uf'] != "pr") {
                $this->peso = $cubagem;
            }
        } else {
            foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                $this->db->tabela = "produto_combinacao";
                $comb = $this->db->consultaId($produtoCarrinho['COMBINACAO']);
                $cubagem += ($comb['altura'] / 100) * ($comb['largura'] / 100) * ($comb['profundidade'] / 100) * $produtoCarrinho['QTD'] * 300;
                $peso += $comb['peso'] * $produtoCarrinho['QTD'];
            }
            if ($cubagem > $peso && $endereco['uf'] != "PR") {
                $this->peso = $cubagem;
            } else {
                $this->peso = $peso;
            }
        }
    }

}
