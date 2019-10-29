<?

/**
 * Modelo para gerenciar o carrinho de produtos
 */
class ModelCarrinho extends MainModel {

    public $id_combinacao = "",
            $quantidade = "";

    /**
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    /**
     * Retorna o ultimo index do carrinho
     * 
     * @return int Ultimo indice do array
     */
    public function ultimoIndexCarrinho() {

        $ultimo = 1;
        if (isset($_SESSION['PEDIDO']['CARRINHO'])) {
            foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $valor) {
                if ($ind >= $ultimo) {
                    $ultimo = $ind + 1;
                }
            }
        }

        return $ultimo;
    }

    /**
     * Procura item no carrinho e retorna o indice atual
     * 
     * @param int $idCombinacao ID da combinação a ser procurada
     * @return type FALSE se não encontrar / Indice atual se encontrar
     */
    public function procurarItemCarrinho($idCombinacao) {

        if (isset($_SESSION['PEDIDO']['CARRINHO'])) {
            foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $valor) {
                if ($valor["COMBINACAO"] == $idCombinacao) {
                    return $ind;
                }
            }
        }
        return FALSE;
    }

    /**
     * Adiciona produto no carrinho, sendo esse carrinho uma
     * sessão que é criada no site
     * 
     * @param type $idCombinacao
     * @param type $quantidade
     * @return boolean Retorna se conseguiu ou não inserir o produto ao carrinho
     */
    public function adicionarProdutoCarrinho() {

        // Busca qual combinação está sendo inserido
        $this->db->tabela = "produto_combinacao";
        $combinacao = $this->db->consultaId($this->id_combinacao);

        // Busca a qual produto pertence a combinação
        $this->db->tabela = "produto";
        $produto = $this->db->consultaId($combinacao['produtoFK']);

        // Traz o ultimo indice do carrinho para que o produto possa ser inserido após ele
        $indice = $this->ultimoIndexCarrinho();

        // Verifica se o produto possui estoque disponivel e se esse estoque corresponde a quantidade selecionada pelo cliente
        if ($combinacao["estoque"] > 0 && $combinacao["estoque"] >= $this->quantidade) {

            // Verifica se o produto já se encontra cadastrado no carrinho, caso sim apenas atualiza a quantidade
            $indiceProduto = $this->procurarItemCarrinho($this->id_combinacao);
            if (!$indiceProduto) {
                $_SESSION["PEDIDO"]["CARRINHO"][$indice]["ID"] = $combinacao["produtoFK"]; // ID do Produto
                $_SESSION["PEDIDO"]["CARRINHO"][$indice]["COMBINACAO"] = $this->id_combinacao; // ID da Variação escolhida (mesmo se o produto for Unico)
                $_SESSION["PEDIDO"]["CARRINHO"][$indice]["QTD"] = $this->quantidade; // Quantidade selecionada
                // Adiciona produto ao relatorio de carrinhos abandonados
                $this->adicionaProdutoRelatorio();
            } else {
                $_SESSION["PEDIDO"]["CARRINHO"][$indiceProduto]["QTD"] += $this->quantidade; // Quantidade selecionada
                // Atualiza a quantidade do produto no relatorio de carrinhos abandonados
                $this->adicionaProdutoRelatorio(TRUE);
            }

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Adicionar o produto ao relatório de carrinhos do cliente
     * podendo então ser utilizado para acompanhar os carrinhos abandonados
     * 
     * @param type $idCombinacao
     */
    public function adicionaProdutoRelatorio($atualizacaoProduto = FALSE) {

        // Pega o ip do cliente e esvazia a variavel que guardará o id do cliente
        $ip = $_SERVER["REMOTE_ADDR"];

        // Testa se o cliente esta logado
        if (isset($_SESSION['CLIENTE'])) {

            $idCliente = $_SESSION['CLIENTE']['id'];
            $filtro = "WHERE ip = '$ip' OR cliente_fk = '" . $_SESSION['CLIENTE']['id'] . "'";
        } else {

            $idCliente = "";
            $filtro = "WHERE ip = '$ip'";
        }

        // Consulta se existe relatorio de visita ativo para o cliente e atribui o id da visita a variavel
        $this->db->tabela = "relatorio_visita";
        $consultaVisita = $this->db->consulta("WHERE ip = '$ip' OR clienteFK = '$idCliente'", "ORDER BY data DESC");
        if (mysql_num_rows($consultaVisita)) {
            $visita = mysql_fetch_assoc($consultaVisita);
            $idVisita = $visita['id'];
        } else {
            $idVisita = "NULL";
        }

        // Busca entre os carrinhos guardados se já existe algum carrinho aberto com o id ou ip do cliente
        $this->db->tabela = "relatorio_carrinho_abandonado";
        $consultaExitencia = $this->db->consulta($filtro, "ORDER BY data_cadastro DESC");


        // Atribui os parametros para serem salvos no relatorio
        $parametros = array();
        $parametros['cliente_fk'] = $idCliente;
        $parametros['visita_fk'] = $idVisita;
        $parametros['ip'] = $ip;
        if (mysql_num_rows($consultaExitencia)) {
            $carrinhoAbandonado = mysql_fetch_assoc($consultaExitencia);
            $idCarrinhoAbandonado = $carrinhoAbandonado['id'];
        } else {
            $idCarrinhoAbandonado = "";
            $parametros['data_cadastro'] = date("d-m-Y H:i:s");
        }

        $this->db->tabela = "relatorio_carrinho_abandonado";
        $this->db->importArray($parametros);
        $idRelatorio = $this->db->persist($idCarrinhoAbandonado);

        if ($this->db->status == "OK") {

            $this->db->tabela = "relatorio_carrinho_abandonado_produto";

            // Testa se é uma atualização de quantidade ou se está sendo adicionado o produto
            if ($atualizacaoProduto) {
                $consultaRelatorio = $this->db->consulta("WHERE combinacao_fk = '" . $this->id_combinacao . "' AND carrinho_fk = '$idRelatorio'");
                $relatorio = mysql_fetch_assoc($consultaRelatorio);
                $quantidade = $relatorio['quantidade'] + $this->quantidade;
                $idCarrinhoAbandonadoProduto = $relatorio['id'];
            } else {
                $quantidade = $this->quantidade;
                $idCarrinhoAbandonadoProduto = "";
            }

            // Passa os parametros para serem salvos em banco
            $parametros = array();
            $parametros['combinacao_fk'] = $this->id_combinacao;
            $parametros['carrinho_fk'] = $idRelatorio;
            $parametros['quantidade'] = $quantidade;
            $parametros['data_cadastro'] = date("d-m-Y H:i:s");

            $this->db->importArray($parametros);
            $resposta = $this->db->persist($idCarrinhoAbandonadoProduto);
        }
    }

    /**
     * Função que verifica se existe um carrinho guardado no banco
     * relacionado ao cliente, caso exista começa a adicionar os 
     * produtos novamente a sessão tomando cuidado para não cadastrar
     * novamente um produto já cadastrado.
     * 
     * @param int $idCliente ID do cliente logado
     */
    public function verificarCarrinhoGuardado() {

        // Pega o ip do cliente e esvazia a variavel que guardará o id do cliente
        $ip = $_SERVER["REMOTE_ADDR"];

        // Consulta se existe um carrinho ativo para o cliente
        $this->db->tabela = "relatorio_carrinho_abandonado";
        $consulta = $this->db->consulta("WHERE cliente_fk = '" . $_SESSION['CLIENTE']['id'] . "' OR ip = '$ip'");

        // Testa se existe carrinho guardado
        if (mysql_num_rows($consulta)) {
            return;
        }

        // Verifica se tem produtos no carrinho que foi guardado
        $carrinhoProduto = mysql_fetch_assoc($consulta);
        $this->db->tabela = "relatorio_carrinho_abandonado_produto";
        $consulta = $this->db->consulta("WHERE carrinho_fk = '" . $carrinhoProduto["id"] . "'");

        // Percorre os produtos no carrinho guardado e vai adicionando ao carrinho atual
        $this->db->tabela = "produto_combinacao";
        while ($campo = mysql_fetch_assoc($consulta)) {
            $combinacao = $this->db->consultaId($campo["combinacaoFK"]);

            if ($combinacao["estoque"] > 0) {
                $indice = $this->ultimoIndexCarrinho();
                if (!chkArray($_SESSION, "PEDIDO")) {
                    $_SESSION["PEDIDO"]["CARRINHO"][$indice]["ID"] = $combinacao["produtoFK"]; // ID do Produto
                    $_SESSION["PEDIDO"]["CARRINHO"][$indice]["COMBINACAO"] = $campo["combinacaoFK"]; // ID da Variação escolhida (mesmo se o produto for Unico)
                    $_SESSION["PEDIDO"]["CARRINHO"][$indice]["QTD"] = $campo["quantidade"]; // Quantidade selecionada
                } else {
                    if ($this->procurarItemCarrinho($campo["combinacaoFK"]) === FALSE) {
                        $_SESSION["PEDIDO"]["CARRINHO"][$indice]["ID"] = $combinacao["produtoFK"]; // ID do Produto
                        $_SESSION["PEDIDO"]["CARRINHO"][$indice]["COMBINACAO"] = $campo["combinacaoFK"]; // ID da Variação escolhida (mesmo se o produto for Unico)
                        $_SESSION["PEDIDO"]["CARRINHO"][$indice]["QTD"] = $campo["quantidade"]; // Quantidade selecionada
                    }
                }
            }
        }
    }

    /**
     * Apaga produto do carrinho com base no indice fornecido
     * 
     * @param type $indice Indice do produto o carrinho
     */
    public function apagarProdutoCarrinho($indice) {

        // Verifica se existe carrinho e se existe o indice do carrinho
        if (isset($_SESSION["PEDIDO"]["CARRINHO"])) {
            if (chkArray($_SESSION["PEDIDO"]["CARRINHO"], $indice)) {
                $idCombinacao = $_SESSION["PEDIDO"]["CARRINHO"][$indice]["COMBINACAO"];
                unset($_SESSION["PEDIDO"]["CARRINHO"][$indice]);

                if (isset($_SERVER['CLIENTE'])) {

                    $idCliente = $_SERVER['CLIENTE']['id'];
                    // Esta função apaga o produto do reltorio de carrinhos
                    $this->db->tabela = "relatorio_carrinho_abandonado";
                    $consulta = $this->db->consulta("WHERE cliente_fk = '$idCliente'");
                    if (mysql_num_rows($consulta)) {
                        $carrinhoCliente = mysql_fetch_assoc($consulta);

                        // Percorre os produtos do carrinho e vai apagando
                        $this->db->tabela = "relatorio_carrinho_abandonado_produto";
                        $consulta = $this->db->consulta("WHERE carrinho_fk = '" . $carrinhoCliente["id"] . "' AND combinacao_fk = '$idCombinacao'");
                        if (mysql_num_rows($consulta)) {
                            $carrinhoProduto = mysql_fetch_assoc($consulta);
                            $this->db->apagaId($carrinhoProduto["id"]);

                            // Apaga o carrinho guardado
                            $consulta = $this->db->consulta("WHERE carrinho_fk = '" . $carrinhoCliente["id"] . "'");
                            if (!mysql_num_rows($consulta)) {
                                $this->db->tabela = "relatorio_carrinho_abandonado";
                                $this->db->apagaId($carrinhoCliente["id"]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Retorna o valor total dos produtos no carrinho
     * 
     * @access public
     * @param boolean $desconto Verifica se o desconto é utilizado na tela
     * @return float Retorna o valor total do carrinho
     */
    public function valorTotalCarrinho($desconto = FALSE, $frete = FALSE) {

        // Zera o valor total do carrinho para caso ele esteja vazio
        $total = 0;

        if (isset($_SESSION["PEDIDO"]["CARRINHO"]) && !empty($_SESSION["PEDIDO"]["CARRINHO"]) && is_array($_SESSION["PEDIDO"]["CARRINHO"])) {
            // Percorre o carrinho somando o valor do produto 
            // multiplicado pela quantidade com o total do carrinho
            $this->db->tabela = "produto_combinacao";
            foreach ((array) $_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produto) {
                if ($produto["COMBINACAO"] != "") {
                    $variacao = $this->db->consultaId($produto["COMBINACAO"]);
                    if (isset($_SESSION["CLIENTE"]) && isset($variacao["valorAtacado"])) {
                        if (chkArray($_SESSION["CLIENTE"], "tipo") == "JURIDICA") {
                            $variacao["valorPor"] = $variacao["valorAtacado"];
                        }
                    }
                    if (chkArray($produto, 'VLR_PRESENTE')) {
                        $total += ($variacao["valorPor"] + $produto['VLR_PRESENTE']) * $produto["QTD"];
                    } else {
                        $total += $variacao["valorPor"] * $produto["QTD"];
                    }
                }
            }

            // Verifica se existe cupom de desconto ativo e se é necessário utilizalo
            // caso verdadeiro aplica o desconto do cupom ao total
            if ($desconto && $this->verificaCupomDesconto($total)) {
                $valorCupom = $this->verificaCupomDesconto($total);
                $total -= $valorCupom;
            }
        }
        if (isset($_SESSION["PEDIDO"]["FRETE"]) && $frete) {
            if (chkArray($_SESSION["PEDIDO"]["FRETE"], "VALOR")) {
                $total += $_SESSION["PEDIDO"]["FRETE"]["VALOR"];
            }
        }

        return $total;
    }

    /**
     * Valida codigo do cupom e retorna FALSE se inválido ou
     * não pertencente ao cliente, ou o id do cupom
     * 
     */
    public function validarCupom() {

        $codigo = $this->parametros["codigo"];

        $this->db->tabela = "cupom";
        $consulta = $this->db->consulta("WHERE codigo = '$codigo'");
        if (mysql_num_rows($consulta)) {
            $cupom = mysql_fetch_assoc($consulta);
            if ($cupom["status"] == "AGUARDANDO") {
                $dataAtual = date("Y-m-d");
                $dataMinimaCupom = $cupom["dataInicio"];
                $dataMaximaCupom = $cupom["dataFim"];
                if (
                        strtotime($dataAtual) >= strtotime($dataMinimaCupom) &&
                        strtotime($dataAtual) <= strtotime($dataMaximaCupom)
                ) {
                    $idCliente = $_SESSION["CLIENTE"]["id"];
                    $documento = $_SESSION["CLIENTE"]["documento"];

                    if ($cupom["descontoGeral"] === "S" || $cupom["clienteFK"] === $idCliente || $cupom["documento"] === $documento) {

                        $this->db->tabela = "cupom_categoria";
                        $consultaCupomCat = $this->db->consulta("WHERE cupomFK = '" . $cupom["id"] . "'");
                        if (mysql_num_rows($consultaCupomCat)) {
                            $catLiberada = FALSE;
                            $this->db->tabela = "produto_categoria";
                            while ($cupomCat = mysql_fetch_assoc($consultaCupomCat)) {
                                foreach ((array) $_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produto) {
                                    if ($produto["ID"] != "") {
                                        $consultaProdCat = $this->db->consulta("WHERE produtoFK = '" . $produto["ID"] . "' AND categoriaFK = '" . $cupomCat["categoriaFK"] . "'");
                                        if (mysql_num_rows($consultaProdCat)) {
                                            $catLiberada = TRUE;
                                        }
                                    }
                                }
                            }
                        } else {
                            $catLiberada = TRUE;
                        }

                        if ($catLiberada) {
                            $_SESSION["PEDIDO"]["CUPOM"]["ID"] = $cupom["id"];
                            $_SESSION["PEDIDO"]["CUPOM"]["TIPO"] = $cupom["tipoDesconto"];
                            $_SESSION["PEDIDO"]["CUPOM"]["VALOR"] = $cupom["valor"];

                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Verifica se tem algum cupom de desconto ativo e retorna o valor do desconto
     * 
     * @param float $totalCarrinho  Valor total do carrinho
     */
    public function verificaCupomDesconto($totalCarrinho) {

        // Verifica se tem algum cupom de desconto ativo
        if (isset($_SESSION["PEDIDO"]["CUPOM"]) && !empty($_SESSION["PEDIDO"]["CUPOM"]) && is_array($_SESSION["PEDIDO"]["CUPOM"]) && $totalCarrinho) {

            // Verifica o tipo do cupom e retorna o valor
            if ($_SESSION["PEDIDO"]["CUPOM"]["TIPO"] == "VALOR") {
                return $_SESSION["PEDIDO"]["CUPOM"]["VALOR"];
            } else {
                return ($totalCarrinho * $_SESSION["PEDIDO"]["CUPOM"]["VALOR"]) / 100;
            }
        }

        return FALSE;
    }

    /**
     * Conta o total de produtos no carrinho incluindo as quantidades
     * 
     * @return int $total Total de produtos no carrinho
     */
    public function totalProdutoCarrinho() {

        $total = 0;
        // Verifica se existem produtos no carrinho, caso sim conta todos os produtos
        if (isset($_SESSION["PEDIDO"]["CARRINHO"]) && !empty($_SESSION["PEDIDO"]["CARRINHO"]) && is_array($_SESSION["PEDIDO"]["CARRINHO"])) {
            foreach ((array) $_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produto) {
                if ($produto["ID"] != "") {
                    $total += $produto["QTD"];
                }
            }
            return $total;
        }

        return 0;
    }

    /// RETORNA O MAIOR PRAZO DE ENTREGA ENTRE OS PRODUTOS NO CARRINHO ///
    function verificaMaiorPrazo() {
        $maior = 0;
        $this->db->tabela = "produto_combinacao";
        if (is_array($_SESSION["PEDIDO"]["CARRINHO"])) {
            foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                if ($produtoCarrinho["COMBINACAO"] != "") {
                    $variacao = $this->db->consultaId($produtoCarrinho["COMBINACAO"]);
                    if ($variacao["prazoExtra"] > $maior) {
                        $maior = $variacao["prazoExtra"];
                    }
                }
            }
            return $maior;
        } else {
            return $maior;
        }
    }

    function mudarQuantidade() {

        $variacaoFK = $this->parametros["variacaoFK"];
        $quantidade = $this->parametros["quantidade"];
        $indice = $this->parametros["indice"];

        $this->db->tabela = "produto_combinacao";
        $combinacao = $this->db->consultaId($variacaoFK);
        if ($combinacao["estoque"] <= 0 || $quantidade <= 0) {
            $this->apagarProdutoCarrinho($indice);
            return true;
        } elseif ($combinacao["estoque"] < $quantidade) {
            return false;
        } else {
            $_SESSION["PEDIDO"]["CARRINHO"][$indice]["QTD"] = $quantidade;
            return true;
        }
    }

}
