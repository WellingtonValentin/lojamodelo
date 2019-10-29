<?

/**
 * Modelo para gerenciar os produtos
 * 
 */
class ModelProduto extends MainModel {

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
        
        // Configura o banco e seleciona a tabela
        $this->db->tabela = "produto";

        // Verifica se tem sessão de limite de itens por página
        if (chkArray($_SESSION, "FILTRAR")) {
            if (chkArray($_SESSION["FILTRAR"], "LIMITE")) {
                $this->resultadoPorPagina = $_SESSION["FILTRAR"]["LIMITE"];
            }
        }

        // Verifica se tem sessão de ordem dos produtos da home
        if (chkArray($_SESSION, "FILTRAR")) {
            if (chkArray($_SESSION["FILTRAR"], "ORDEM")) {
                switch ($_SESSION["FILTRAR"]["ORDEM"]) {
                    case "novos":
                        $this->ordenacao = "ORDER BY id DESC";
                        break;
                    case "maisVendidos":
                        $this->ordenacao = "ORDER BY ("
                                . "SELECT SUM(pp.quantidade) FROM pedido_produto pp WHERE pp.combinacaoFK IN ("
                                . "SELECT id FROM produto_combinacao pc WHERE pc.produtoFK = produto.id)"
                                . ") DESC";
                        break;
                    case "menorPreco":
                        $this->ordenacao = "ORDER BY ("
                                . "SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE pc.produtoFK = produto.id AND pc.unico = 'U'"
                                . ") ASC";
                        break;
                    case "maiorPreco":
                        $this->ordenacao = "ORDER BY ("
                                . "SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE pc.produtoFK = produto.id AND pc.unico = 'U'"
                                . ") DESC";
                        break;
                    case "AZ":
                        $this->ordenacao = "ORDER BY titulo ASC";
                        break;
                    case "ZA":
                        $this->ordenacao = "ORDER BY titulo DESC";
                        break;
                }
            }
        }
    }

    /**
     * Adiciona visita ao produto
     * 
     * @param type $idProduto
     */
    public function adicionarVisita($idProduto) {

        $ip = $_SERVER["REMOTE_ADDR"];
        if (!isset($_SESSION["VISITA"])) {
            $_SESSION["VISITA"] = array();
        }

        $this->db->tabela = "produto_visita";
        $consulta = $this->db->consulta("WHERE ip = '$ip' AND produtoFK = '$idProduto' AND DATEDIFF(now(), data) < 3");
        if (!mysql_num_rows($consulta)) {
            if (!isset($_SESSION["VISITA"][$idProduto])) {
                $_SESSION["VISITA"][$idProduto] = array();
                $_SESSION["VISITA"][$idProduto]["QTD"] = 1;
            } else {
                $_SESSION["VISITA"][$idProduto]["QTD"] = $_SESSION["VISITA"][$idProduto]["QTD"] + 1;
            }

            $parametro["produtoFK"] = $idProduto;
            $parametro["ip"] = $ip;
            $parametro["data"] = date("d-m-Y H:i:s");
            $this->db->importArray($parametro);
            $this->db->persist();
        }
    }

    /**
     * Lista os produtos
     * 
     * @access public
     * @return array Resultado da query
     */
    public function listarProdutos($filtro = "") {
        $this->db->tabela = "produto";

        // Configura as variações
        $id = $where = $order = $limit = null;

        if (isset($filtro)) {

            // Configura o where da consulta
            $where = $filtro;
        }

        // Verifica se tem sessão de fitro de pronta entrega dos produtos da home
        if (chkArray($_SESSION, "FILTRAR")) {
            if (chkArray($_SESSION["FILTRAR"], "PRONTAENTREGA")) {
                switch ($_SESSION["FILTRAR"]["PRONTAENTREGA"]) {
                    case "s":
                        $where .= " AND (SELECT SUM(pc.estoque) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) > 0";
                        break;
                    case "n":
                        $where .= " AND (SELECT SUM(pc.estoque) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) = 0";
                        break;
                }
            }
        }

        // Verifica se tem sessão de fitro de faixa de valor dos produtos da home
        if (chkArray($_SESSION, "FILTRAR")) {
            if (chkArray($_SESSION["FILTRAR"], "FAIXA")) {
                switch ($_SESSION["FILTRAR"]["FAIXA"]) {
                    case "19":
                        $where .= " AND (SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) <= 19";
                        break;
                    case "39":
                        $where .= " AND (SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) BETWEEN 19 AND 39";
                        break;
                    case "59":
                        $where .= " AND (SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) BETWEEN 39 AND 59";
                        break;
                    case "79":
                        $where .= " AND (SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) BETWEEN 59 AND 79";
                        break;
                    case "99":
                        $where .= " AND (SELECT SUM(pc.valorPor) FROM produto_combinacao pc WHERE produto.id = pc.produtoFK) >= 79";
                        break;
                }
            }
        }
        
        $where .= " AND status = 'A'";

        // Configura a página a ser exibida
        $pagina = !empty($this->parametros[0]) ? $this->parametros[0] : 1;

        // Como a páginação se inicia do 0 diminui uma página da variável
        $pagina--;

        // Configura o número de resultados por página
        $resultadoPorPagina = $this->resultadoPorPagina;

        // O offset dos resultados da consulta
        $offset = $pagina * $resultadoPorPagina;

        // Configura o limite da consulta
        $limit = "LIMIT $offset, $resultadoPorPagina";

        // Verifica se foi informado uma ordem, caso contrario ordena por id descrecente
        if ($this->ordenacao == "") {
            $order = "ORDER BY id ASC";
        } else {
            $order = $this->ordenacao;
        }

        // Faz a consulta
        $query = $this->db->consulta($where, $order, $limit);
        $retornoArray = $this->db->fetchAll($query);

        // Inicia a variável que sera retornada para a view
        $retorno = array();

        // Percorre o array do resultado trazendo apenas os campos relevantes para a listagem e já configurados
        foreach ($retornoArray as $ind => $prod) {
            $retorno["$ind"] = array();

            // Define o ID, Título e Imagem do produto
            $retorno["$ind"]["id"] = $prod["id"];
            $retorno["$ind"]["titulo"] = $prod["titulo"];
            $retorno["$ind"]["imagem"] = $this->imagemPrincipal($prod["id"]);
            $retorno["$ind"]["prontaEntrega"] = $prod["prontaEntrega"];
            ;

            // Inicializa as variáveis de valores e parcelamentos
            $textoDE = "";
            $textoPOR = "";
            $textoParcelamento = "";
            $valores = $this->valoresProduto($prod["id"]);

            // Verifica se o produto possui variações
            if (isset($valores["0"]["maxValor"])) {
                if ($valores["0"]["minDe"] != $valores["0"]["maxDe"]) {
                    $retorno[$ind]["textoDE"] = "De R$ " . dinheiro($valores["0"]["minDe"]) . " - R$ " . dinheiro($valores["0"]["maxDe"]);
                } elseif (isset($valores["0"]["minDe"]) || isset($valores["0"]["maxDe"])) {
                    if ($valores["0"]["maxDe"] > 0) {
                        $retorno[$ind]["textoDE"] = "De R$ " . dinheiro($valores["0"]["maxDe"]);
                    }
                }

                if ($valores["0"]["minValor"] != $valores["0"]["maxValor"]) {
                    $retorno[$ind]["textoPOR"] = "R$ " . dinheiro($valores["0"]["minValor"]) . " - R$ " . dinheiro($valores["0"]["maxValor"]);
                } elseif (isset($valores["0"]["minValor"]) || isset($valores["0"]["maxValor"])) {
                    $retorno[$ind]["textoPOR"] = "R$ " . dinheiro($valores["0"]["maxValor"]);
                }
            } else {
                if (isset($valores["0"]["valorDe"])) {
                    if ($valores["0"]["valorDe"] > 0) {
                        $retorno[$ind]["textoDE"] = "De R$ " . dinheiro($valores["0"]["valorDe"]);
                    }
                }
                if (isset($valores["0"]["valorPor"])) {
                    $retorno[$ind]["textoPOR"] = "R$ " . dinheiro($valores["0"]["valorPor"]);
                }

                $parcelamento = $this->parcelaSemJuros();
                if (is_array($parcelamento) && isset($valores["0"]["valorPor"]) && isset($parcelamento["0"]["parcelaSemJuros"])) {
                    if ($parcelamento["0"]["parcelaSemJuros"] > 0) {
                        $valorParcelado = $valores["0"]["valorPor"] / $parcelamento["0"]["parcelaSemJuros"];
                        if ($valorParcelado >= 5) {
                            $retorno[$ind]["textoParcelamento"] = $parcelamento["0"]["parcelaSemJuros"] . "x de R$ " . dinheiro($valorParcelado) . " sem juros";
                        }
                    }
                }
            }

            // Avaliação média do produto
            $retorno[$ind]["avaliacao"] = $this->avaliacaoProduto($prod["id"]);


            // Estoque minimo e maximo
            $retorno[$ind]['estoque'] = $this->menorMaiorEstoque($prod["id"]);
        }

        // Cria paginação
        $paginacao = $this->paginacao($where);
        $retorno["paginacao"] = $paginacao;

        // Retorna o array com os dados dos produtos
        return $retorno;
    }

    /**
     * Retorno o maior e o menor estoque do produto
     * 
     * @produtoFK itn $produtoFK
     */
    public function menorMaiorEstoque($produtoFK) {

        $filtro = "WHERE produtoFK = '$produtoFK'";
        $campo = "MIN(estoque) as minEstoque, MAX(estoque) as maxEstoque";

        // Executa a busca
        $this->db->tabela = "produto_combinacao";
        $consulta = $this->db->consulta($filtro, "", "", "", "", $campo);
        $retorno = $this->db->fetch($consulta);
        $retorno["menor"] = $retorno[0]['minEstoque'];
        $retorno["maior"] = $retorno[0]['maxEstoque'];
        return $retorno;
    }

    /**
     * Paginação
     * 
     * @access public
     */
    public function paginacao($filtro = "") {

        $this->db->tabela = "produto";
        $atual = "";

        // Verifica se o primeiro parâmetro da url não é um numérico
        // caso seja não é necessária paginação
//        if (is_numeric(chkArray($this->parametros, 0))) {
//            return;
//        }
//        
        // Obtém o total de resultados da base de dados
        $query = $this->db->consulta($filtro, "", "", "", "", "COUNT(*) as total");
        $total = $this->db->fetch($query);
        $total = $total["0"]["total"];

        // Resultados por página
        $resultadoPorPagina = $this->resultadoPorPagina;

        // Obtém a última página possível
        $ultima = ceil($total / $resultadoPorPagina);

        // Configura a primeira página
        $primeira = 1;

        // Offisets utilizados como limites para exibir os números das
        // páginas deixando a página atual no centro
        $offset1 = 3;
        $offset2 = 7;

        // Página atual
        $atual = ($this->parametros[0]) ? $this->parametros[0] : 1;

        $retorno = array();

        $retorno["atual"] = $atual;
        $retorno["ultima"] = $ultima;
        $retorno["total"] = $total;
        $retorno["offset1"] = $offset1;
        $retorno["offset2"] = $offset2;

        return $retorno;
    }

    /**
     * Função de busca de produtos que também salva a busca no relatório
     * 
     * @return array Resultado da busca
     */
    public function buscaProduto($abrangente = FALSE) {

        // Arrai de termos para remover da busca
        $removeBusca = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "x", "y", "w", "z", "a", "e", "i", "o", "u", "ao", "para", "por", "da", "de", "di", "do", "du", " ", "  ", "   ", "    ", "ate", "em", "com");

        // Inicia a variável de filtragem
        $filtro = array();
        $idRelatorio = "";
        $where = "";


        // Transforma o POST em uma array de parametros
        if (chkArray($_GET, "busca")) {
            $_GET["busca"] = str_replace("'", "", $_GET["busca"]);
            $_GET["busca"] = str_replace("\"", "", $_GET["busca"]);
            $termo = arrumaString($_GET["busca"]);
            $parametros = explode(" ", $_GET["busca"]);
        } elseif (chkArray($this->parametros, 1)) {
            $termo = arrumaString($this->parametros[1]);
            $parametros = explode("-", $termo);
        }

        // Verifica se esta definido os parametros para a busca
        if (chkArray($parametros, 0)) {

            // Salva o termo pesquisado no banco para ser utilizado depois no relatorio
            $ip = $_SERVER["REMOTE_ADDR"];
            $where = "WHERE ip = '$ip' AND busca = '$termo' AND CAST(data AS date) != '" . date("Y-m-d") . "'";

            // Consulta se já existe esse termo buscado por esse cliente hoje
            $this->db->tabela = "relatorio_busca";
            $query = $this->db->consulta($where);

            // Caso ainda não tenha sido pesquisado hoje este termo será apagado no relatório
            if (!mysql_num_rows($query)) {

                $campo["ip"] = $ip;
                $campo["busca"] = $termo;
                $campo["data"] = date("d-m-Y H:i:s");

                $this->db->importArray($campo);
                $idRelatorio = $this->db->persist();
            }

            // Percorre os parametros para montar a busca
            foreach ($parametros as $parametro) {
                if (!in_array($parametro, $removeBusca)) {
                    $filtro[] = "(titulo LIKE '%$parametro%' OR texto LIKE '%$parametro%' OR keyword LIKE '%$parametro%')";

                    // Salva os termos pesquisados na tabela do relatório
                    if ($idRelatorio) {
                        $campo["buscaFK"] = $idRelatorio;
                        $campo["palavra"] = $parametro;

                        $this->db->tabela = "relatorio_busca_palavra";
                        $this->db->importArray($campo);
                        $this->db->persist();
                    }
                }
            }

            // Junta os termos para a busca
            if (chkArray($filtro, 0)) {
                $filtro = implode(($abrangente) ? " OR " : " AND ", $filtro);
                $where = "WHERE " . $filtro . " AND status = 'A'";
            } else {
                $where = "WHERE status = 'A'";
            }
        }

        // Chama função de listagem
        $retorno = $this->listarProdutos($where);
        $retorno["termo"] = $termo;

        return $retorno;
    }

    /**
     * Consula o menor e o maior valor de variação dos produtos
     * 
     * @param int $produtoFK ID do produto
     * @return array Retorna toda a linha da pesquisa com valor minimo e máximo das variações
     */
    function valoresProduto($produtoFK) {

        // Define os campos e o filtro para a busca
        $filtro = "WHERE produtoFK = '$produtoFK' AND unico = 'V'";
        $campo = "MIN(valorPor) as minValor, MIN(valorDe) as minDe, MAX(valorPor) as maxValor, MAX(valorDe) as maxDe, MAX(valorAtacado) as maxAtacado";

        // Executa a busca
        $this->db->tabela = "produto_combinacao";
        $consulta = $this->db->consulta($filtro, "", "", "", "", $campo);
        $retorno = $this->db->fetch($consulta);

        if (!isset($retorno["0"]["maxValor"])) {

            // Caso não exista variação retorna o valor do produto
            $filtro = "WHERE produtoFK = '$produtoFK' AND unico = 'U'";
            $consulta = $this->db->consulta($filtro);
            $retorno = $this->db->fetch($consulta);
            if (isset($_SESSION["CLIENTE"]) && isset($retorno["0"]["valorAtacado"])) {
                if (chkArray($_SESSION["CLIENTE"], "tipo") == "JURIDICA") {
                    $retorno["0"]["valorPor"] = $retorno["0"]["valorAtacado"];
                }
            }
        } elseif (isset($_SESSION["CLIENTE"]) && isset($retorno["0"]["maxAtacado"])) {
            if (chkArray($_SESSION["CLIENTE"], "tipo") == "JURIDICA") {
                $retorno["0"]["maxDe"] = $retorno["0"]["maxAtacado"];
            }
        }

        return $retorno;
    }

    /**
     * Função para buscar a maior parcela sem juros
     * 
     * @return array Forma de Pagamento com a maior parcela sem juros
     */
    function parcelaSemJuros() {
        $this->db->tabela = "forma_pagamento";
        $consulta = $this->db->consulta("WHERE parcelaSemJuros > 1", "ORDER BY parcelaSemJuros DESC");
        $retorno = $this->db->fetch($consulta);
        return $retorno;
    }

    /**
     * Verifica a média das avaliações do produto
     * 
     * @param int $produtoFK ID do produto
     * @return float Valor da avaliação
     */
    function avaliacaoProduto($produtoFK) {
        $this->db->tabela = "pesquisa_satisfacao_produto";
        $consulta = $this->db->consulta("WHERE produtoFK = '$produtoFK'", "", "", "", "", "AVG(avaliacao) AS avaliacao");
        $retorno = $this->db->fetch($consulta);
        if ($retorno["0"]["avaliacao"] > 0) {
            $retorno = number_format($retorno["0"]["avaliacao"], 2, ".", "");
            $retorno = floor($retorno);
        } else {
            $retorno = FALSE;
        }
        return $retorno;
    }

    /**
     * Retorna a imagem principal do produto ou então uma das imagens ou ainda uma imagem padrão caso sem foto
     * 
     * @param int $produtoFK ID do produto
     * @return string Nome da foto
     */
    function imagemPrincipal($produtoFK) {
        $this->db->tabela = "produto_foto";
        $campo = "pf.nomeFoto";
        $where = "WHERE pf.nomeFoto IS NOT NULL";
        $join = "JOIN produto_foto_combinacao pfc ON pfc.produtoFotoFK = pf.id "
                . "JOIN produto_combinacao pc ON pc.id = pfc.produtoCombinacaoFK AND pc.produtoFK = '$produtoFK'";
        $group = "GROUP BY pf.nomeFoto";
        $order = "ORDER BY pf.principal ASC";


        $consulta = $this->db->consulta($where, $order, "", $join, $group, $campo, "pf", TRUE, TRUE);
        $retorno = $this->db->fetch($consulta);
        if (isset($retorno["0"]["nomeFoto"])) {
            $retorno = HOME_URL . "/view/_upload/produto/" . $produtoFK . "/" . $retorno["0"]["nomeFoto"];
        } else {
            $retorno = HOME_URL . "/view/_image/padrao/semFoto.jpg";
        }
        return $retorno;
    }

    public function detalharProduto($idProduto) {

        $retornoProduto = array();
        $this->db->tabela = "produto";
        $produto = $this->db->consultaId($idProduto);

        $retornoProduto["produto"] = $produto;


        $this->db->tabela = "pesquisa_satisfacao_produto";
        $consultaMediaAvaliacao = $this->db->consulta("WHERE produtoFK = '$idProduto' AND status = 'LIBERADO'");
        $somaAvaliacao = 0;
        $contaAvaliacao = 0;
        while ($mediaAvaliacao = mysql_fetch_assoc($consultaMediaAvaliacao)) {
            $somaAvaliacao += $mediaAvaliacao["avaliacao"];
            $contaAvaliacao++;
        }

        $retornoProduto["avaliacao"]["media"] = $somaAvaliacao;
        $retornoProduto["avaliacao"]["total"] = $contaAvaliacao;


        $this->db->tabela = "produto_foto";
        $consulta = $this->db->consulta("", "ORDER BY pf.principal, pf.ordem", "", "JOIN produto_combinacao pc ON pc.produtoFK = '" . $idProduto . "' JOIN produto_foto_combinacao pfc ON pfc.produtoFotoFK = pf.id AND pfc.produtoCombinacaoFK = pc.id", "GROUP BY pf.nomeFoto", "pf.id, pf.nomeFoto as foto, pf.principal, pf.ordem", "pf", TRUE);

        $retornoProduto["fotos"] = $this->db->fetchAll($consulta);


        $this->db->tabela = "produto_video";
        $consulta = $this->db->consulta("WHERE produtoFK = '$idProduto'");

        $retornoProduto["videos"] = $this->db->fetchAll($consulta);


        $this->db->tabela = "variacao";
        $consulta = $this->db->consulta("", "ORDER BY v.titulo", "", "JOIN variacao_valor vv ON vv.variacaoFK = v.id JOIN produto_combinacao_valor pcv ON pcv.variacaoFK = vv.id JOIN produto_combinacao pc ON pc.produtoFK = '$idProduto' AND pcv.combinacaoFK = pc.id AND pc.unico = 'V'", "GROUP BY v.id", "v.id, v.subtitulo, v.titulo", "v", TRUE);
        $conta = 0;
        while ($variacao = mysql_fetch_assoc($consulta)) {
            $conta2 = 0;
            $retornoProduto["variacao"][$conta]["id"] = $variacao["id"];
            $retornoProduto["variacao"][$conta]["titulo"] = $variacao["titulo"];

            $this->db->tabela = "variacao_valor";
            $consulta2 = $this->db->consulta("", "ORDER BY vv.titulo", "", "JOIN produto_combinacao_valor pcv ON pcv.variacaoFK = vv.id JOIN produto_combinacao pc ON pc.produtoFK = '$idProduto' AND pcv.combinacaoFK = pc.id AND pc.unico = 'V' JOIN variacao v ON v.id = vv.variacaoFK AND v.id = '" . $variacao["id"] . "'", "GROUP BY vv.titulo", "vv.*", "vv", TRUE);

            while ($variacaoValor = mysql_fetch_assoc($consulta2)) {
                $retornoProduto["variacao"][$conta]["valor"][$conta2]["id"] = $variacaoValor["id"];
                $retornoProduto["variacao"][$conta]["valor"][$conta2]["titulo"] = $variacaoValor["titulo"];
                if ($variacaoValor["nomeFoto"]) {
                    $retornoProduto["variacao"][$conta]["valor"][$conta2]["icone"] = "<img src=\"" . UPLOAD_URL . "/variacao/" . $variacaoValor["nomeFoto"] . "\"  height=\"32\"/>";
                } elseif (substr($variacaoValor["icone"], 0, 1) == "#") {
                    $retornoProduto["variacao"][$conta]["valor"][$conta2]["icone"] = $variacaoValor["icone"];
                } else {
                    $retornoProduto["variacao"][$conta]["valor"][$conta2]["icone"] = "<span>" . $variacaoValor["icone"] . "</span>";
                }
                $conta2++;
            }
            $conta++;
        }


        $valores = $this->valoresProduto($idProduto);
        if (isset($valores["0"]["maxDe"])) {
            if (isset($valores["0"]["minDe"]) && ($valores["0"]["minDe"] != $valores["0"]["maxDe"])) {
                $retornoProduto["produto"]["valorDe"] = "R$ " . number_format($valores["0"]["minDe"], 2, ",", ".");
                $retornoProduto["produto"]["valorDe"] .= " - R$ " . number_format($valores["0"]["maxDe"], 2, ",", ".");
            } else {
                if ($valores["0"]["maxDe"] > 0) {
                    $retornoProduto["produto"]["valorDe"] = "R$ " . number_format($valores["0"]["maxDe"], 2, ",", ".");
                }
            }
        } elseif ($valores["0"]["valorDe"]) {
            if ($valores["0"]["valorDe"] > 0) {
                $retornoProduto["produto"]["valorDe"] = "R$ " . number_format($valores["0"]["valorDe"], 2, ",", ".");
            }
        }


        if (isset($valores["0"]["maxValor"])) {
            if (isset($valores["0"]["minValor"]) && ($valores["0"]["minValor"] != $valores["0"]["maxValor"])) {
                $retornoProduto["produto"]["valorPor"] = "R$ " . number_format($valores["0"]["minValor"], 2, ",", ".");
                $retornoProduto["produto"]["valorPor"] .= " - R$ " . number_format($valores["0"]["maxValor"], 2, ",", ".");
            } else {
                $retornoProduto["produto"]["valorPor"] = "R$ " . number_format($valores["0"]["maxValor"], 2, ",", ".");
            }
        } else {
            $retornoProduto["produto"]["valorPor"] = "R$ " . number_format($valores["0"]["valorPor"], 2, ",", ".");
            $retornoProduto["produto"]["variacaoUnica"] = $retornoProduto["produto"]["parmiteParcelamento"] = TRUE;
            $retornoProduto["produto"]["valorReal"] = $valores["0"]["valorPor"];

            $this->db->tabela = "produto_combinacao";
            $consulta = $this->db->consulta("WHERE produtoFK = '$idProduto' AND unico = 'U'");
            if (mysql_num_rows($consulta)) {
                $campo = mysql_fetch_assoc($consulta);
                $retornoProduto["produto"]["variacaoID"] = $campo["id"];
                $retornoProduto["produto"]["estoque"] = $campo["estoque"];
            }

            $parcelamento = $this->parcelaSemJuros();
            if (isset($parcelamento["0"]["parcelaSemJuros"])) {
                $valorParcelado = $valores["0"]["valorPor"] / $parcelamento["0"]["parcelaSemJuros"];
                if ($valorParcelado >= 5) {
                    $retornoProduto["produto"]["textoParcelamento"] = $parcelamento["0"]["parcelaSemJuros"] . "x de R$ " . dinheiro($valorParcelado) . " sem juros";
                }
            }
        }

        $this->db->tabela = "produto_informacao";
        $consulta = $this->db->consulta("WHERE produtoFK = '$idProduto'");
        $conta = 1;

        $retornoProduto["informacao"][0]["titulo"] = "Descrição";
        $retornoProduto["informacao"][0]["texto"] = $produto["texto"];
        while ($campo = mysql_fetch_assoc($consulta)) {
            $retornoProduto["informacao"][$conta]["titulo"] = $campo["titulo"];
            $retornoProduto["informacao"][$conta]["texto"] = $campo["texto"];
            $conta++;
        }

        $this->db->tabela = "produto_categoria";
        $consulta = $this->db->consulta("WHERE produtoFK = '$idProduto'");
        $conta = 0;
        if (mysql_num_rows($consulta)) {
            $campo = mysql_fetch_assoc($consulta);

            $this->db->tabela = "categoria";
            $categoria = $this->db->consultaId($campo["categoriaFK"]);

            $retornoProduto["categoria"][0]["link"] = "/produto/categoria/" . $campo["categoriaFK"] . "/" . arrumaString($categoria["titulo"]) . ".html";
            $retornoProduto["categoria"][0]["titulo"] = $categoria["titulo"];
        }


        $this->db->tabela = "pesquisa_satisfacao_produto";
        $consulta = $this->db->consulta("WHERE produtoFK = '$idProduto' AND status = 'LIBERADO'");
        $conta = 0;
        while ($campo = mysql_fetch_assoc($consulta)) {
            $this->db->tabela = "pedido";
            $pedido = $this->db->consultaId($campo["pedidoFK"]);

            $this->db->tabela = "cliente";
            $cliente = $this->db->consultaId($pedido["clienteFK"]);

            $data = explode(" ", $campo["data"]);

            $retornoProduto["pesquisaSatisfacao"][$conta]["autor"] = $cliente["nome"];
            $retornoProduto["pesquisaSatisfacao"][$conta]["avaliacao"] = $campo["avaliacao"];
            $retornoProduto["pesquisaSatisfacao"][$conta]["pros"] = $campo["pros"];
            $retornoProduto["pesquisaSatisfacao"][$conta]["contras"] = $campo["contras"];
            $retornoProduto["pesquisaSatisfacao"][$conta]["comentario"] = $campo["comentario"];
            $retornoProduto["pesquisaSatisfacao"][$conta]["data"] = dataSite($data[0]);
            $conta++;
        }


        $this->db->tabela = "produto_comprejunto";
        $consulta = $this->db->consulta("WHERE produtoPaiFK = '$idProduto'");
        $conta = 0;
        while ($campo = mysql_fetch_assoc($consulta)) {

            $imagemPai = $this->imagemPrincipal($idProduto);
            $imagemFilho = $this->imagemPrincipal($campo["produtoFilhoFK"]);

            $this->db->tabela = "produto_combinacao";
            $consultaPai = $this->db->consulta("WHERE produtoFK = '" . $campo["produtoPaiFK"] . "' AND unico = 'U'");
            $valoresPai = $this->db->fetch($consultaPai);

            $consultaFilho = $this->db->consulta("WHERE produtoFK = '" . $campo["produtoFilhoFK"] . "' AND unico = 'U'");
            $valoresFilho = $this->db->fetch($consultaFilho);

            $this->db->tabela = "produto";
            $produto = $this->db->consultaId($campo["produtoFilhoFK"]);

            $retornoProduto["compreJunto"][$conta]["imagemPai"] = $imagemPai;
            $retornoProduto["compreJunto"][$conta]["imagemFilho"] = $imagemFilho;
            $retornoProduto["compreJunto"][$conta]["titulo"] = $produto["titulo"];
            $retornoProduto["compreJunto"][$conta]["idProduto"] = $produto["id"];
            $retornoProduto["compreJunto"][$conta]["valorPai"] = "R$ " . number_format($valoresPai["0"]["valorPor"] + (($valoresPai["0"]["valorPor"] * $campo["desconto"]) / 100), 2, ",", ".");
            $retornoProduto["compreJunto"][$conta]["valorFilho"] = "R$ " . number_format($valoresFilho["0"]["valorPor"] + (($valoresFilho["0"]["valorPor"] * $campo["desconto"]) / 100), 2, ",", ".");
            $conta++;
        }


        if (isset($_SESSION["VISITA"])) {
            $conta = 0;
            foreach ($_SESSION["VISITA"] as $ind => $produtoVisita) {
                if ($ind != $idProduto) {
                    $this->db->tabela = "produto";
                    $produto = $this->db->consultaId($ind);
                    $valores = $this->valoresProduto($ind);

                    $retornoProduto["produtoVisita"][$conta]["id"] = $ind;
                    $retornoProduto["produtoVisita"][$conta]["titulo"] = $produto["titulo"];
                    $retornoProduto["produtoVisita"][$conta]["imagem"] = $this->imagemPrincipal($ind);

                    if (isset($valores["0"]["maxDe"])) {
                        if (isset($valores["0"]["minDe"]) && ($valores["0"]["minDe"] != $valores["0"]["maxDe"])) {
//                            $retornoProduto["produtoVisita"][$conta]["valorDe"] = "R$ " . number_format($valores["0"]["minDe"], 2, ",", ".");
//                            $retornoProduto["produtoVisita"][$conta]["valorDe"] .= " - R$ " . number_format($valores["0"]["maxDe"], 2, ",", ".");
                        } else {
                            if ($valores["0"]["maxDe"] > 0) {
                                $retornoProduto["produtoVisita"][$conta]["valorDe"] = "R$ " . number_format($valores["0"]["maxDe"], 2, ",", ".");
                            }
                        }
                    } elseif (isset($valores["0"]["valorDe"])) {
                        if ($valores["0"]["valorDe"] > 0) {
                            $retornoProduto["produtoVisita"][$conta]["valorDe"] = "R$ " . number_format($valores["0"]["valorDe"], 2, ",", ".");
                        }
                    }

                    if (isset($valores["0"]["maxValor"])) {
                        if (isset($valores["0"]["minValor"]) && ($valores["0"]["minValor"] != $valores["0"]["maxValor"])) {
                            $retornoProduto["produtoVisita"][$conta]["valorPor"] = "R$ " . number_format($valores["0"]["minValor"], 2, ",", ".");
                            $retornoProduto["produtoVisita"][$conta]["valorPor"] .= " - R$ " . number_format($valores["0"]["maxValor"], 2, ",", ".");
                        } else {
                            $retornoProduto["produtoVisita"][$conta]["valorPor"] = "R$ " . number_format($valores["0"]["maxValor"], 2, ",", ".");
                        }
                    } elseif (isset($valores["0"]["valorPor"])) {
                        $retornoProduto["produtoVisita"][$conta]["valorPor"] = "R$ " . number_format($valores["0"]["valorPor"], 2, ",", ".");
                    }


                    // Estoque minimo e maximo
                    $retornoProduto["produtoVisita"][$conta]['estoque'] = $this->menorMaiorEstoque($ind);

                    $conta++;
                }
            }
        }


        $this->db->tabela = "produto";
        $consulta = $this->db->consulta("WHERE p.status = 'A'", "ORDER BY p.titulo ASC", "LIMIT 20", "JOIN produto_categoria pc2 ON pc2.produtoFK = '$idProduto' JOIN produto_categoria pc ON pc.produtoFK = p.id AND pc.categoriaFK = pc2.categoriaFK AND pc.produtoFK != '$idProduto'", "GROUP BY p.id", "p.id, p.titulo", "p", TRUE);
        $conta = 0;
        while ($campo = mysql_fetch_assoc($consulta)) {
            $valores = $this->valoresProduto($campo["id"]);

            $retornoProduto["produtoSemelhante"][$conta]["id"] = $campo["id"];
            $retornoProduto["produtoSemelhante"][$conta]["titulo"] = $campo["titulo"];
            $retornoProduto["produtoSemelhante"][$conta]["imagem"] = $this->imagemPrincipal($campo["id"]);

            if (isset($valores["0"]["maxDe"])) {
                if (isset($valores["0"]["minDe"]) && ($valores["0"]["minDe"] != $valores["0"]["maxDe"])) {
//                    $retornoProduto["produtoSemelhante"][$conta]["valorDe"] = "R$ " . number_format($valores["0"]["minDe"], 2, ",", ".");
//                    $retornoProduto["produtoSemelhante"][$conta]["valorDe"] .= " - R$ " . number_format($valores["0"]["maxDe"], 2, ",", ".");
                } else {
                    if ($valores["0"]["maxDe"] > 0) {
                        $retornoProduto["produtoSemelhante"][$conta]["valorDe"] = "R$ " . number_format($valores["0"]["maxDe"], 2, ",", ".");
                    }
                }
            } elseif ($valores["0"]["valorDe"]) {
                if ($valores["0"]["valorDe"] > 0) {
                    $retornoProduto["produtoSemelhante"][$conta]["valorDe"] = "R$ " . number_format($valores["0"]["valorDe"], 2, ",", ".");
                }
            }

            if (isset($valores["0"]["maxValor"])) {
                if (isset($valores["0"]["minValor"]) && ($valores["0"]["minValor"] != $valores["0"]["maxValor"])) {
                    $retornoProduto["produtoSemelhante"][$conta]["valorPor"] = "R$ " . number_format($valores["0"]["minValor"], 2, ",", ".");
                    $retornoProduto["produtoSemelhante"][$conta]["valorPor"] .= " - R$ " . number_format($valores["0"]["maxValor"], 2, ",", ".");
                } else {
                    $retornoProduto["produtoSemelhante"][$conta]["valorPor"] = "R$ " . number_format($valores["0"]["maxValor"], 2, ",", ".");
                }
            } else {
                $retornoProduto["produtoSemelhante"][$conta]["valorPor"] = "R$ " . number_format($valores["0"]["valorPor"], 2, ",", ".");
            }

            // Estoque minimo e maximo
            $retornoProduto["produtoSemelhante"][$conta]['estoque'] = $this->menorMaiorEstoque($campo["id"]);

            $conta++;
        }


        return $retornoProduto;
    }

    public function indicarProduto() {

        $this->db->tabela = "relatorio_indique_amigo";
        if (
                isset($this->parametros["nome"]) &&
                isset($this->parametros["email"]) &&
                isset($this->parametros["nomeAmigo"]) &&
                isset($this->parametros["emailAmigo"]) &&
                isset($this->parametros["produtoFK"])
        ) {
            $parametros = $this->parametros;
            $parametros["data"] = date("d-m-Y H:i:s");
            $this->db->importArray($parametros);
            $resultado = $this->db->persist();

            if (!is_array($resultado)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function salvarAviseme($idProduto) {

        $this->db->tabela = "cliente_produto_indisponivel";
        $consulta = $this->db->consulta("WHERE produtoFK = '$idProduto' AND email = '" . $_POST["email"] . "' AND enviado = 'N'");
        if (!mysql_num_rows($consulta)) {
            if (isset($_SESSION["CLIENTE"])) {
                $persist["clienteFK"] = $_SESSION["CLIENTE"]["id"];
                $persist["nome"] = $_SESSION["CLIENTE"]["nome"];
            }
            $persist["produtoFK"] = $idProduto;
            $persist["email"] = $_POST["email"];
            $persist["data"] = date("d-m-Y H:i:s");
            $persist["enviado"] = "N";
            $this->db->importArray($persist);
            $this->db->persist();
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
