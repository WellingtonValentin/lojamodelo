<?php

class ModelFrete extends MainModel {

    public $cep = "",
            $idCombinacao = "",
            $telaFinalizacao = FALSE,
            $idPedido = "",
            $tipoEnvio = "",
            $caixaPadrao = array(),
            $freteGratis = TRUE;

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

        $this->db->tabela = "frete_tabela";

        /// DEFINE A CAIXA PARA ENTREGA QUE IRA DEFINIR OS LIMITES PARA ABRIR UMA NOVA CAIXA DE ENVIO ///
        if (isset($configFrete[0]["alturaLimite"]) AND isset($configFrete[0]["larguraLimite"]) AND isset($configFrete[0]["profundidadeLimite"])) {
            $this->caixaPadrao = array(
                "altura" => $configFrete[0]["alturaLimite"],
                "largura" => $configFrete[0]["larguraLimite"],
                "profundidade" => $configFrete[0]["profundidadeLimite"]
            );
        } else {
            $this->caixaPadrao = array(
                "altura" => 60,
                "largura" => 60,
                "profundidade" => 60
            );
        }
    }

    /**
     * Função principal de cálculo de frete
     * 
     * @return array Retorna valores do frete 
     */
    public function calcularFrete() {

        $this->db->tabela = "config_frete";
        $configFrete = $this->db->consultaId(1);

        $objFreteTabela = $this->controller->loadModel("frete/model-freteTabela");
        $objEmpacota = $this->controller->loadModel("frete/model-empacotamento");
        $objCarrinho = $this->controller->loadModel("carrinho/model-carrinho");

        $objFreteTabela->cep = $this->cep;

        /// VERIFICA SE O CÁLCULO ESTA SENDO EFETUADO A PARTIR DO CARRINHO OU DA TELA DE PRODUTO ///
        if (isset($this->idCombinacao) == $this->idCombinacao > 0) {

            // FRETE GRÁTIS DO PRODUTO É CHECADO NO CONTROLER PORTANTO O PADRÃO DE FRETE GRÁTIS NESTE MODELO É FALSE
            $this->freteGratis = FALSE;

            // BUSCA PRODUTO QUE ESTA SENDO PESQUISADO O FRETE
            $this->db->tabela = "produto_combinacao";
            $produto = $this->db->consultaId($this->idCombinacao);

            // CHAMA A FUNÇÃO PARA FAZER O CÁLCULO DO FRETE
            $valorFrete = $this->calculoTelaProduto($produto);

            // VARIÁVEIS QUE SERÃO UTILIZADAS NO TESTE DE FRETE GRÁTIS
            $valorTotal = $produto["valorPor"];
        } else {

            // CHAMA A FUNÇÃO PARA FAZER O CÁLCULO DO FRETE
            $valorFrete = $this->calculoTelaCarrinho();

            // VARIÁVEIS QUE SERÃO UTILIZADAS NO TESTE DE FRETE GRÁTIS
            $valorTotal = $objCarrinho->valorTotalCarrinho(TRUE);
        }

        // VERIFICA SE TEM PROMOÇÃO DE FRETE GRÁTIS ATIVA E ATRIBUI O DESCONTO AO VALOR DO FRETE
        $valorFreteGratis = $this->freteGratis($valorTotal);
        if ($valorFreteGratis > 0 && isset($valorFrete["PAC"]["valor"])) {
            if ($valorFreteGratis >= 100) {
                $valorFrete["PAC"]["valor"] = "GRÁTIS";
                $this->freteGratis = TRUE;
            } else {
                $valorFrete["PAC"]["valor"] -= ($valorFrete["PAC"]["valor"] * $valorFreteGratis) / 100;
            }
        }

        /// APLICA VALOR ADICIONAL NOS VALORES DO FRETE CASO CONFIGURADO EM BANCO ///
        if (!isset($valorFrete["erro"])) {
            foreach ($valorFrete as $ind => $frete) {
                if (isset($frete["valor"]) && ((!$this->freteGratis && $ind == "PAC") || $ind != "PAC")) {
                    $valorFrete[$ind]["valor"] = $valorFrete[$ind]["valor"] + (($valorFrete[$ind]["valor"] * $configFrete["valorAdicional" . $ind]) / 100);
                }
            }
            $erro = "";
        } else {
            $erro = $this->freteErro($valorFrete["erro"]);
        }


        return $valorFrete;
    }

    /**
     * Efetua o calculo do frete na tela do produto
     * 
     * @param type $produto O produto
     * @return type 
     */
    public function calculoTelaProduto($produto) {

        $objFreteTabela = $this->controller->loadModel("frete/model-freteTabela");
        $objFreteTabela->cep = $this->cep;

        /// DEFINE VARIÁVEIS PARA SEREM ENVIADAS PARA A FUNÇÃO DE CÁLCULO DE FRETE ///
        $pesoConsiderado = $this->encontraPeso($produto);

        /// CALCULA O FRETE ///
        $tipoFrete = $this->verificaTipoDeFrete($this->tipoEnvio, $this->cep, $pesoConsiderado);
        $erro = $this->buscaErros($pesoConsiderado, $produto["altura"], $produto["largura"], $produto["profundidade"], $this->tipoEnvio);

        if (!$erro) {
            $valorFrete = $objFreteTabela->calculaFrete($this->cep, $pesoConsiderado, $tipoFrete);
        } else {
            $valorFrete["erro"] = $erro;
        }

        /// BUSCA ERROS NO CALCULO DO FRETE ///
        if (!$erro && !chkArray($valorFrete, 'erro')) {
            /// CALCULA O PRAZO DE ENTREGA DOS PRODUTOS ///
            $prazoAdicional = $produto["prazoExtra"] + isset($configFrete["prazoAdicional"]) ? $configFrete["prazoAdicional"] : 0;
            $prazoCorreios = $this->prazoFrete();
            $valorFrete["PAC"]["prazo"] = $prazoCorreios["PAC"]["prazo"] + $prazoAdicional;
            $valorFrete["SEDEX"]["prazo"] = $prazoCorreios["SEDEX"]["prazo"] + $prazoAdicional;
            if (isset($prazoCorreios["ESEDEX"]["prazo"])) {
                $valorFrete["ESEDEX"]["prazo"] += $prazoCorreios["ESEDEX"]["prazo"] + $prazoAdicional;
            }
        }

        /// SALVA O LOG DE PESQUISA ///
        $this->salvarLog("TELA DE PRODUTO", $produto, $valorFrete, $erro);
        return $valorFrete;
    }

    /**
     * Efetua o calculo de frete na tela de produtos
     * 
     * @return boolean
     */
    public function calculoTelaCarrinho() {
        require ABSPATH . '/enum.php';

        $objFreteTabela = $this->controller->loadModel("frete/model-freteTabela");
        $objFreteTabela->cep = $this->cep;
        $objEmpacota = $this->controller->loadModel("frete/model-empacotamento");

        // DEFINE SE A ENTREGA IRÁ UTILIZAR O CORREIOS, CARTA REGISTRADA OU TRANSPORTADORA
        $this->db->tabela = "produto";
        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
            $produto = $this->db->consultaId($produtoCarrinho["ID"]);

            if ($produto["freteTransportadora"] == "S") {
                $entregaTransportadora = TRUE;
                $entregaCarta = FALSE;
                $entregaCorreios = FALSE;
                break;
            } elseif ($produto["freteCartaRegistrada"] == "N") {
                $entregaCorreios = TRUE;
                $entregaTransportadora = FALSE;
                $entregaCarta = FALSE;
                break;
            } else {
                $entregaCarta = TRUE;
                $entregaTransportadora = FALSE;
                $entregaCorreios = FALSE;
            }
        }

        if ($entregaTransportadora) {
            return FALSE;
        } elseif ($entregaCorreios) {

            $acheiGratis = FALSE;

            /// ARRUMA O ARRAY DE PRODUTOS PARA PODER SER UTILIZADO NO SISTEMA DE EMPACOTAMENTO ///
            foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {

                $this->db->tabela = "produto";
                $produto = $this->db->consultaId($produtoCarrinho["ID"]);
                $this->db->tabela = "produto_combinacao";
                $combinacao = $this->db->consultaId($produtoCarrinho["COMBINACAO"]);

                if ($produto["freteGratis"] == "N") {
                    $this->freteGratis = FALSE;

                    $produtosPAC["titulo"][] = $produto["titulo"];
                    $produtosPAC["codigoProduto"][] = $produtoCarrinho["COMBINACAO"];
                    $produtosPAC["altura"][] = $combinacao["altura"];
                    $produtosPAC["largura"][] = $combinacao["largura"];
                    $produtosPAC["profundidade"][] = $combinacao["profundidade"];
                    $produtosPAC["quantidade"][] = $produtoCarrinho["QTD"];
                    $produtosPAC["peso"][] = $combinacao["peso"];
                } else {
                    $acheiGratis = TRUE;
                }

                $produtos["titulo"][] = $produto["titulo"];
                $produtos["codigoProduto"][] = $produtoCarrinho["COMBINACAO"];
                $produtos["altura"][] = $combinacao["altura"];
                $produtos["largura"][] = $combinacao["largura"];
                $produtos["profundidade"][] = $combinacao["profundidade"];
                $produtos["quantidade"][] = $produtoCarrinho["QTD"];
                $produtos["peso"][] = $combinacao["peso"];
            }

            /// EMPACOTA O PRODUTO EM CAIXAS DE ENVIO ///
            $envio = $objEmpacota->empacotar($produtos, $this->caixaPadrao);
            if ($acheiGratis) {
                if (isset($produtosPAC)) {
                    $envioPAC = $objEmpacota->empacotar($produtosPAC, $this->caixaPadrao);
                }
            }

            /// CALCULA OS VALORES DE ENTREGA DAS CAIXAS DE ENVIO ///
            $totalPAC = 0;
            $totalSEDEX = 0;
            $totalESEDEX = 0;
            $erroFrete = FALSE;

            foreach ($envio["pacotes"] as $ind => $pacote) {
                $peso = $this->encontraPeso($pacote);

                $tipoFrete = $this->verificaTipoDeFrete($this->tipoEnvio, $this->cep, $peso);
                $erro = $this->buscaErros($peso, $pacote["altura"], $pacote["largura"], $pacote["profundidade"], $this->tipoEnvio);


                if (!$erro) {

                    $valorFrete = $objFreteTabela->calculaFrete($this->cep, $peso, $tipoFrete);
                    if (!isset($valorFrete["erro"])) {
                        if (!isset($envioPAC)) {
                            $envio["pacotes"][$ind]["valorPAC"] = "R$ " . number_format($valorFrete["PAC"]["valor"], 2, ",", ".");
                            $totalPAC += $valorFrete["PAC"]["valor"];
                        }
                        $envio["pacotes"][$ind]["valorSEDEX"] = "R$ " . number_format($valorFrete["SEDEX"]["valor"], 2, ",", ".");
                        $totalSEDEX += $valorFrete["SEDEX"]["valor"];
                        if (isset($valorFrete["ESEDEX"]["valor"])) {
                            $envio["pacotes"][$ind]["valorESEDEX"] = "R$ " . number_format($valorFrete["ESEDEX"]["valor"], 2, ",", ".");
                            $totalESEDEX += $valorFrete["ESEDEX"]["valor"];
                        }
                    } else {
                        $erroFrete = TRUE;
                        break;
                    }
                } else {
                    $valorFrete["erro"] = $erro;
                    $erroFrete = TRUE;
                    break;
                }
            }

            // FAZ A PESQUISA DE VALORES APENAS COM OS PRODUTOS QUE NÃO SÃO FRETE GRÁTIS
            if (!$erroFrete && isset($envioPAC)) {
                foreach ($envioPAC["pacotes"] as $ind => $pacote) {
                    $peso = $this->encontraPeso($pacote);
                    $tipoFrete = $this->verificaTipoDeFrete($this->tipoEnvio, $this->cep, $peso);
                    $valorFrete = $objFreteTabela->calculaFrete($this->cep, $peso, $tipoFrete);
                    $envio["pacotes"][$ind]["valorPAC"] = "R$ " . number_format($valorFrete["PAC"]["valor"], 2, ",", ".");
                    $totalPAC += $valorFrete["PAC"]["valor"];
                }
            }

            // VERIFICA SE EXISTE ERRO NO FRETE CASO TENHA ERRO RETORNA O ERRO E SALVA NO LOG
            // CASO NÃO TENHA ERRO RETORNA O TOTAL DO FRETE EM TODOS OS MEIO DE ENTREG
            if ($erroFrete) {
                if ($this->telaFinalizacao) {
                    $pagina = "FINALIZAÇÃO DE PEDIDO";
                } else {
                    $pagina = "CARRINHO DE COMPRAS";
                }

                $erro = $this->freteErro($valorFrete["erro"]);
                $this->salvarLog($pagina, $_SESSION["PEDIDO"]["CARRINHO"], $valorFrete, $erro);

                return $valorFrete;
            } else {

                /// CALCULA O PRAZO DE ENTREGA DOS PRODUTOS ///
                $prazoCorreios = $this->prazoFrete();
                $objCarrinho = $this->controller->loadModel("carrinho/model-carrinho");
                $prazoAdicional = $objCarrinho->verificaMaiorPrazo() + $configFrete[0]["prazoAdicional"];

                // COMEÇA A PREPARAR O ARRAY DE RETORNO
                $valorFrete = array();

                if ($this->freteGratis) {
                    $valorFrete["PAC"]["valor"] = "GRÁTIS";
                } else {
                    $valorFrete["PAC"]["valor"] = $totalPAC;
                }
                $valorFrete["PAC"]["prazo"] = $prazoCorreios["PAC"]["prazo"] + $prazoAdicional;

                $valorFrete["SEDEX"]["valor"] = $totalSEDEX;
                $valorFrete["SEDEX"]["prazo"] = $prazoCorreios["SEDEX"]["prazo"] + $prazoAdicional;

                if ($totalESEDEX > 0) {
                    $valorFrete["ESEDEX"]["valor"] = $totalESEDEX;
                    $valorFrete["ESEDEX"]["prazo"] = $prazoCorreios["SEDEX"]["prazo"] + $prazoAdicional;
                }

                /// SALVA AS CAIXAS DE ENTREGA EM UMA SESSÃO PARA DEPOIS SER SALVAS JUNTO COM O PEDIDO ///
                if ($this->telaFinalizacao) {
                    $conta = 0;
                    foreach ($envio["pacotes"] as $ind => $caixa) {
                        $_SESSION["CAIXAS"][$conta]["altura"] = $caixa["altura"];
                        $_SESSION["CAIXAS"][$conta]["largura"] = $caixa["largura"];
                        $_SESSION["CAIXAS"][$conta]["profundidade"] = $caixa["profundidade"];
                        $_SESSION["CAIXAS"][$conta]["peso"] = $caixa["peso"];
                        $conta++;
                    }
                    $conta = 0;
                    foreach ($envio["produtos"] as $ind => $produtoEmpacotado) {
                        $_SESSION["PRODUTOEMPACOTADO"][$conta]["produtoCombinacaoFK"] = $produtoEmpacotado["codigoProduto"];
                        $_SESSION["PRODUTOEMPACOTADO"][$conta]["posicaoZ"] = $produtoEmpacotado["posicaoZ"];
                        $_SESSION["PRODUTOEMPACOTADO"][$conta]["posicaoY"] = $produtoEmpacotado["posicaoY"];
                        $_SESSION["PRODUTOEMPACOTADO"][$conta]["posicaoX"] = $produtoEmpacotado["posicaoX"];
                        $_SESSION["PRODUTOEMPACOTADO"][$conta]["posicaoCaixa"] = $produtoEmpacotado["posicaoCaixa"];
                    }
                    $pagina = "FINALIZAÇÃO DE PEDIDO";
                } else {
                    unset($_SESSION["CAIXAS"]);
                    unset($_SESSION["PRODUTOEMPACOTADO"]);
                    $pagina = "CARRINHO DE COMPRAS";
                }

                /// PREPARA VARIÁVEIS PARA SEREM SALVAS NO LOG ///
                $produtos = $_SESSION["PEDIDO"]["CARRINHO"];

                return $valorFrete;
            }
        } else {

            $envelopes = $objEmpacota->empacotaEnvelope();

            $totalCARTA = 0;
            foreach ($envelopes["ENVELOPE"] as $ind => $carta) {
                $valorFrete = $objFreteTabela->calculaFrete($this->cep, $carta["PESO"], "CARTA REGISTRADA");

                if (!$valorFrete["erro"]) {
                    $envelopes["ENVELOPE"][$ind]["valorCARTA"] = "R$ " . number_format($valorFrete["CARTA"]["valor"], 2, ",", ".");
                    $totalCARTA += $valorFrete["CARTA"]["valor"];
                } else {
                    $erroFrete = TRUE;
                    break;
                }
            }
            $valorFrete = array();
            $valorFrete["CARTA"]["valor"] = $totalCARTA;

            $prazoCorreios = 5;
            $objCarrinho = $this->controller->loadModel("carrinho/model-carrinho");
            $prazoAdicional = $objCarrinho->verificaMaiorPrazo() + $configFrete["prazoAdicional"];
            $valorFrete["CARTA"]["prazo"] = $prazoCorreios + $prazoAdicional;

            if ($erroFrete) {
                if ($this->telaFinalizacao) {
                    $pagina = "FINALIZAÇÃO DE PEDIDO";
                } else {
                    $pagina = "CARRINHO DE COMPRAS";
                }
                $erro = $this->freteErro($valorFrete["erro"]);
                $this->salvarLog($pagina, $envelopes, $valorFrete, $erro);
            }
            return $valorFrete;
        }
    }

    /**
     * Verifica o tipo de frete que será utilizado na 
     * entrega e se será possível utilizar este modo de entrega
     * 
     * @param string $tipoFrete Tipo da entrega selecionado
     * @param string $cep CEP de destino
     * @param array $valorFrete Array com o resultado do cálculo de frete
     * @param int $peso Peso total dos produtos
     * @return string Tipo do frete
     */
    function verificaTipoDeFrete($tipoFrete = "", $cep = "", $peso = "", $valorFrete = "") {
        /*
         *  1 - FRETE PAC
         *  2 - FRETE SEDEX
         *  3 - FRETE ESEDEX
         *  4 - FRETE CARTA REGISTRADA
         *  5 - FRETE TRANSPORTADORA
         *  6 - RETIRADA NA LOJA
         *  7 - ENTREGA POR MOTOBOY
         *  8 - FRETE A CALCULAR
         *  9 - FRETE GRÁTIS
         */


        if ($tipoFrete == 5) {
            return "TRANSPORTADORA";
        } elseif ($tipoFrete == 6) {
            return "RETIRADA NA LOJA";
        } elseif ($tipoFrete == 8) {
            return "MOTOBOY";
        } elseif ((isset($valorFrete["PAC"]["erro"]) && $tipoFrete == 1) || (isset($valorFrete["SEDEX"]["erro"]) && $tipoFrete == 2) || (isset($valorFrete["ESEDEX"]["erro"]) && $tipoFrete == 3)) {
            return "FRETE A CALCULAR";
        } elseif ($tipoFrete == 4 AND $peso <= 0.5) {
            return "CARTA REGISTRADA";
        } else {
            switch ($tipoFrete) {
                case 1:
                    $this->freteGratis = FALSE;
                    $fretePago = FALSE;
                    if (isset($this->idCombinacao)) {
                        $this->db->tabela = "produto_combinacao";
                        $produtoCombinacao = $this->db->consultaId($this->idCombinacao);

                        $this->db->tabela = "produto";
                        $produto = $this->db->consultaId($produtoCombinacao["produtoFK"]);

                        if ($produto["freteGratis"] == "N") {
                            $fretePago = TRUE;
                        } else {
                            $this->freteGratis = TRUE;
                        }
                    } else {
                        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                            if (chkArray($produtoCarrinho, "FRETE_GRATIS")) {
                                $fretePago = TRUE;
                            } else {
                                $this->freteGratis = TRUE;
                            }
                        }
                    }
                    if ($fretePago && $this->freteGratis) {
                        return "FRETE A CALCULAR";
                    } elseif ($fretePago) {
                        if (!isset($this->idCombinacao)) {
                            $objCarrinho = $this->controller->loadModel("carrinho/model-carrinho");
                            $valorCarrinho = $objCarrinho->valorTotalCarrinho();
                            $valorFrete = $this->freteGratis($valorCarrinho);
                            if ($valorFrete <= 0) {
                                return "FRETE GRÁTIS - PAC";
                            } else {
                                return "PAC";
                            }
                        } else {
                            return "PAC";
                        }
                    } elseif ($this->freteGratis) {
                        return "FRETE GRÁTIS - PAC";
                    }
                    break;
                case 2:
                    return "SEDEX";
                    break;
                case 3:
                    if (!$valorFrete["ESEDEX"]["erro"]) {
                        return "ESEDEX";
                    } else {
                        return "ESEDEX NEGADO";
                    }
                    break;
            }
        }
    }

    /**
     * Retorna o valor do desconto do frete caso tenha campanha de frete grátis ativa
     * 
     * @param float $valorCarrinho Valor total dos produts no carrinho
     * @param string $cep CEP de destino
     * @return int Valor do desconto
     */
    function freteGratis($valorCarrinho) {
        $data = date("Y-m-d");

        $this->db->tabela = "campanha_frete_gratis";
        $consulta = $this->db->consulta("WHERE ('" . $data . "' BETWEEN cfg.dataInicio AND cfg.dataFim) AND ('" . $valorCarrinho . "' >= cfg.valorMinimo OR cfg.valorMinimo IS NULL)", "ORDER BY cfg.valorDesconto DESC", "", "JOIN campanha_frete_gratis_regiao cfgr ON ('" . $this->cep . "' BETWEEN cfgr.faixaCepMinima AND cfgr.faixaCepMaxima) AND (cfgr.campanhaFK = cfg.id)", "", "cfg.valorDesconto AS desconto", "cfg", TRUE);

        if (mysql_num_rows($consulta)) {
            $valor = mysql_fetch_assoc($consulta);
            return $valor["desconto"];
        } else {
            return 0;
        }
    }

    /**
     * Retorna o peso que será utilizado no cálculo de frete
     * 
     * @param array $produto Dados do produto
     * @return float Peso considerado
     */
    function encontraPeso($produto) {

        $fatorCubagem = 6000;
        $peso = round($produto["peso"]);
        $altura = $produto["altura"];
        $largura = $produto["largura"];
        $profundidade = $produto["profundidade"];

        $cubagem = ($altura * $largura * $profundidade) / $fatorCubagem;
        $cubagem = round($cubagem);

        if ($cubagem < 10 OR $cubagem < $peso) {
            return $peso;
        } else {
            return $cubagem;
        }
        return $peso;
    }

    /**
     * Salvar log do cálculo de frete
     * 
     * @param int $pedidoFK ID do pedido
     * @param string $cep CEP de destino de entrega
     * @param string $pagina Página em que foi feito o cálculo do frete
     * @param array $produtos Produtos que foram feitos cálculo do frete
     * @param array $retorno Retorno do cálculo do frete
     * @param string $erro Mensagem de erro
     */
    function salvarLog($pagina = "", $produtos = "", $retorno = "", $erro = "") {

        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'IE';
        } elseif (preg_match('|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Opera';
        } elseif (preg_match('|Firefox/([0-9\.]+)|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Firefox';
        } elseif (preg_match('|Chrome/([0-9\.]+)|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Chrome';
        } elseif (preg_match('|Safari/([0-9\.]+)|', $useragent, $matched)) {
            $browser_version = $matched[1];
            $browser = 'Safari';
        } else {
            // browser not recognized!
            $browser_version = 0;
            $browser = 'other';
        }

        $ip = $_SERVER["REMOTE_ADDR"];
        if (isset($_SESSION['CLIENTE'])) {
            $idCliente = $_SESSION['CLIENTE']['id'];
            $filtro = "WHERE ip = '$ip' OR clienteFK = '$idCliente'";
        } else {
            $filtro = "WHERE ip = '$ip'";
        }
        $this->db->tabela = "relatorio_visita";
        $consultaVisita = $this->db->consulta($filtro, "ORDER BY data DESC");
        if (mysql_num_rows($consultaVisita)) {
            $visita = mysql_fetch_assoc($consultaVisita);
            $idVisita = $visita['id'];
        } else {
            $idVisita = "NULL";
        }

        $navegador = $browser . "v" . $browser_version;

        if (isset($_SESSION["CLIENTE"])) {
            $parametros["clienteFK"] = $_SESSION["CLIENTE"]["id"];
        }
        $parametros["pedidoFK"] = $this->idPedido;
        $parametros["visita_fk"] = $idVisita;
        $parametros["ip"] = $ip;
        $parametros["cep"] = $this->cep;
        $parametros["pagina"] = $pagina;
        $parametros["navegador"] = $navegador;
        $parametros["erro"] = $erro;
        $parametros["data"] = date("d-m-Y H:i:s");
//        $parametros["sessao"] = serialize($_SESSION);
        $parametros["retorno"] = serialize($retorno);
        $parametros["produtos"] = serialize($produtos);
//        $parametros["server"] = serialize($_SERVER);

        $this->db->tabela = "relatorio_frete";
        $this->db->importArray($parametros);
        $this->db->persist();
    }

    /**
     * Interpreta o código de erro e apresenta a mensagem de retorno
     * 
     * @param string $codigoErro Código do erro
     * @return string Mensagem de retorno
     */
    function freteErro($codigoErro) {
        switch ($codigoErro) {
            case 1:
                return "Endere&ccedil;o de entrega n&atilde;o encontrado";
                break;
            case 2:
                return "Peso total do pedido superior ao limite dos correios";
                break;
            case 3:
                return "Soma da altura, largura e profundidade do pedido superior ao limite dos correios";
                break;
            case 4:
                return "Altura superior ao limite dos correios";
                break;
            case 5:
                return "Largura superior ao limite dos correios";
                break;
            case 6:
                return "Profundidade superior ao limite dos correios";
                break;
            case 7:
                return "Servi&ccedil;o de endere&ccedil;amento temporariamente indispon&iacute;vel, tente novamente mais tarde";
                break;
            case 8:
                return "Servi&ccedil;o indispon&iacute;vel para a sua localidade";
                break;
            case 9:
?>
                <?

                return "Valor de frete n&atilde;o encontrado em nossa base de dados de frete, entre em contato para efetuar a cota&ccedil;&atilde;o";
                break;
        }
    }

    /**
     * Busca de erros nos correios
     * 
     * @param float $peso Peso total do carrinho
     * @param int $altura Altura da encomenda total em centimetros
     * @param int $largura Largura da encomenda total em centimetros
     * @param int $profundidade Profundidade da encomenda total em centimetros
     * @param int $tipoEnvio Tipo de envio selecionado
     * @return int Código do erro
     */
    function buscaErros($peso = "", $altura = "", $largura = "", $profundidade = "", $tipoEnvio = "") {

        $objFreteTabela = $this->controller->loadModel("frete/model-freteTabela");

        if ($peso > 30) {
            return 2;
        }
        if ($altura > 105) {
            return 4;
        }
        if ($largura > 105) {
            return 5;
        }
        if ($profundidade > 105) {
            return 6;
        }
        if ($altura + $largura + $profundidade > 200) {
            return 3;
        }
        if (!$objFreteTabela->checaEsedex() AND $tipoEnvio == 3) {
            return 9;
        }
        return FALSE;
    }

    /**
     * Busca do endereço do cliente
     * 
     * @param string $cep CEP de destino infomado
     * @return array Dados do endereço
     */
    function buscarEndereco() {
        $endereco = file_get_contents('http://byteabyte.com.br/webservice/cep/?pass=123321456&cep=' . $this->cep);
        $endereco = (array) json_decode($endereco);
        $endereco["cidade"] = utf8_encode($endereco["cidade"]);
        $endereco["logradouro"] = utf8_encode($endereco["logradouro"]);
        $endereco["bairro"] = utf8_encode($endereco["bairro"]);
        return $endereco;
    }

    /**
     * Busca o prazo de entrega nos correios
     * 
     * @param string $cep_origem CEP da loja
     * @param string $cep_destino CEP do destinatário
     * @param string $codigoEmpresa Código da empresa utilizado para envio via E-Sedex
     * @param string $senhaEmpresa Senha da empresa utilizado para envio via E-Sedex
     * @return type
     */
    function prazoFrete() {

        $this->db->tabela = "config";
        $empresa = $this->db->consultaId(1);

        $this->db->tabela = "config_frete";
        $configFrete = $this->db->consultaId(1);

        $fretes = array("PAC" => "41106", "SEDEX" => "40010", "ESEDEX" => "81019");
        foreach ($fretes as $ind => $valor) {
            $correios = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx"
                    . "?nCdEmpresa=" . $configFrete["codigoESEDEX"] . ""
                    . "&sDsSenha=" . $configFrete["senhaESEDEX"] . ""
                    . "&sCepOrigem=" . $empresa["cep"] . ""
                    . "&sCepDestino=" . $this->cep . ""
                    . "&nVlPeso=2"
                    . "&nCdFormato=1"
                    . "&nVlComprimento=20"
                    . "&nVlAltura=20"
                    . "&nVlLargura=20"
                    . "&sCdMaoPropria=n"
                    . "&nVlValorDeclarado=0"
                    . "&sCdAvisoRecebimento=n"
                    . "&nCdServico=" . $valor . ""
                    . "&nVlDiametro=0"
                    . "&StrRetorno=xml";
            $xml = @simplexml_load_file($correios);
//            if ($xml->cServico->Erro == '0' && $xml->cServico->PrazoEntrega > 0) {
            if ($xml->cServico->PrazoEntrega > 0) {
                $retorno[$ind]["prazo"] = (string) $xml->cServico->PrazoEntrega;
            } else {
                $retorno[$ind]["erro"] = utf8_encode($xml->cServico->Erro);
            }
        }
        return $retorno;
    }

    /**
     * Acompanhamento do rastreamento do produto
     * 
     * @param string $codigoRastreio Código de rastreio
     * @return Define variáveis que poderão ser utilizadas exibir status da entrega
     */
    function rastreamento($codigoRastreio) {
        $html = utf8_encode(file_get_contents('http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' . $codigoRastreio));

        if (strstr($html, '<table') === false) {
            $this->erro = true;
            $this->erro_msg = 'Objeto ainda não foi adicionado no sistema';
            return;
        }

        $this->hash = md5($html);
        $html = preg_replace("@\r|\t|\n| +@", ' ', $html);
        $html = str_replace('</tr>', "</tr>\n", $html);

        if (preg_match_all('@<tr>(.*)</tr>@', $html, $mat, PREG_SET_ORDER)) {
            $track = array();
            $mat = array_reverse($mat);
            $temp = null;
            foreach ($mat as $item) {
                if (preg_match("@<td rowspan=[12]>(.*)</td><td>(.*)</td><td><FONT COLOR=\"[0-9A-F]{6}\">(.*)</font></td>@", $item[0], $d)) {
                    $tmp = array(
                        'data' => $d[1],
                        'data_sql' => preg_replace('@([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2})@', '$3-$2-$1 $4:$5:00', $d[1]),
                        'local' => $d[2],
                        'acao' => strtolower($d[3]),
                        'detalhes' => ''
                    );

                    if ($temp) {
                        $tmp['detalhes'] = $temp;
                        $temp = null;
                    }

                    $track[] = (object) $tmp;
                } else if (preg_match("@<td colspan=2>(.*)</td>@", $item[0], $d)) {
                    $temp = $d[1];
                }
                $this->status = $tmp['acao'];
            }
            $this->track = $track;
            return;
        }

        $this->erro = true;
        $this->erro_msg = 'Falha de Comunicação com os correios';

        /// ------------------- PARA UTILIZAR OS RETORNOS UTILIZE ESSAS VARIÁVEIS -------------------   ///
        /// $c->status;     <--- STATUS ATUAL DA ENCOMENTA                                              ///
        /// $c->track;      <--- STATUS QUE A ENCOMENTA PASSOU UTILIZAR (FOREACH ($c->track as $l))     ///
        /// $l->data;       <--- DATA DO STATUS (DENTRO DO FOREACH)                                     ///
        /// $l->local;      <--- LOCAL DO STATUS (DENTRO DO FOREACH)                                    ///
        /// $l->acao;       <--- STATUS DO STATUS (DENTRO DO FOREACH)                                   ///
        /// $l->detalhes;   <--- DETALHES DO STATUS (DENTRO DO FOREACH)                                 ///
        /// $c->erro;       <--- BOLEANA SE HOUVE ERRO                                                  ///
        /// $c->erro_msg;   <--- MENSAGEM DO ERRO                                                       ///
        ///                                                                                             ///
        /// EXEMPLO: $c = new Correios(); $c->rastreamento("RJ279590559CN"); echo $c->status;           ///
    }

}
