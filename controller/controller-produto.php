<?

/**
 * Controlador da página de produtos e ações relativas a produtos
 */
class ControllerProduto extends MainController {

    /**
     * Carrega a página "/produto/detalhes/ID_DO_PRODUTO/NOME_DO_PRODUTO.html#conteudo"
     */
    public function detalhes() {

        // Pega o ID do produto
        $id = $this->parametros["0"];

        // Define a tabela de produtos e pega o produto para testar a existencia
        $this->db->tabela = "produto";
        $testaProduto = $this->db->consultaId($id);

        // Testa a exitencia do produto
        if (is_array($testaProduto)) {

            // Aciona o modelo do produtos e tráz ele detalhado adicionando também a visita ao produto
            $modelo = $this->loadModel("produto/model-produto");
            $produto = $modelo->detalharProduto($id);
            $modelo->adicionarVisita($id);

            // Verifica se o produto permite parcelamento, caso permita monta a tabela com os valores parcelados
            if (chkArray($produto["produto"], "parmiteParcelamento")) {
                $modeloFormaPagamento = $this->loadModel("formapagamento/model-formapagamento");
                $this->db->tabela = "forma_pagamento";
                $consultaFormaPagamento = $this->db->consulta("WHERE status = 'A'");
                $conta = 0;
                while ($formaPagamento = mysql_fetch_assoc($consultaFormaPagamento)) {
                    $modeloFormaPagamento->formaPagamento = $formaPagamento["classe"];
                    $produto["formapagamento"][$conta]["titulo"] = $formaPagamento["titulo"];
                    $produto["formapagamento"][$conta]["classe"] = $formaPagamento["classe"];
                    $produto["formapagamento"][$conta]["semJuros"] = $modeloFormaPagamento->encontrarParcelaSemJuros($formaPagamento["classe"]);
                    $produto["formapagamento"][$conta]["parcela"] = $modeloFormaPagamento->calcularJuros($produto["produto"]["valorReal"]);
                    $conta++;
                }
            }

            // Verifica se o cliente solicitou o avise-me quando chegar
            if (isset($_POST["aviseme"])) {
                $resultado = $modelo->salvarAviseme($id);
            }

            // Se o produto estiver em alguma categoria cria um breadcrumb para ela
            if (isset($produto["categoria"])) {
                $paginas[0]["link"] = $produto["categoria"][0]["link"];
                $paginas[0]["titulo"] = $produto["categoria"][0]["titulo"];
            }

            // Desmarca a variação selecionada
            unset($_SESSION["VARIACAO"]);

            // Titulo da página
            $this->title = $produto["produto"]["titulo"];
            $this->description = $produto["produto"]["description"];
            $this->keyword = $produto["produto"]["keyword"];
            if (isset($produto["fotos"][0]["foto"])) {
                $this->image = UPLOAD_URL . "/produto/" . $id . "/" . $produto["fotos"][0]["foto"];
            }

            // Carrega os arquivos da view
            require ABSPATH . "/view/_include/header.php";
            require ABSPATH . "/view/produto/view-produto.php";

            if (isset($produto["informacao"])) {
                require ABSPATH . "/view/produto/view-informacao.php";
            }
            if (isset($produto["pesquisaSatisfacao"])) {
                require ABSPATH . "/view/produto/view-avaliacao.php";
            }
            if (isset($produto["compreJunto"])) {
                require ABSPATH . "/view/produto/view-compreJunto.php";
            }
            if (isset($produto["produtoVisita"])) {
                require ABSPATH . "/view/produto/view-produtoVisitado.php";
            }
            if (isset($produto["produtoSemelhante"])) {
                require ABSPATH . "/view/produto/view-produtoSemelhante.php";
            }

            require ABSPATH . "/view/_include/footer.php";
        } else {
            require ABSPATH . "/view/_include/header.php";
            require ABSPATH . '/include/404.php';
            require ABSPATH . "/view/_include/footer.php";
        }
    }

    public function home() {

        // Titulo da página
        $this->title = "Home";

        $modelo = $this->loadModel("produto/model-produto");
        if (isset($_SESSION["FILTRAR"]["LIMITE"])) {
            $modelo->resultadoPorPagina = $_SESSION["FILTRAR"]["LIMITE"];
        } else {
            $modelo->resultadoPorPagina = 15;
        }
        $resultadoProd = $modelo->listarProdutos("WHERE destaque = 'S'");
        $resultadoProdPromo = $modelo->listarProdutos("WHERE promocao = 'S'");

        $this->db->tabela = "categoria";
        $this->db->limit = 9999;
        $consulta = $this->db->consulta("WHERE topo = 'S'");
        while ($cat = mysql_fetch_assoc($consulta)) {
            $resultadoProdCat[] = array(
                "titulo" => $cat["titulo"],
                "produtos" => $modelo->listarProdutos("WHERE id IN (SELECT produtoFK FROM produto_categoria WHERE categoriaFK = '" . $cat["id"] . "')")
            );
        }
        $this->db->tabela = "banner_secundario";
        $consultaBannerSec = $this->db->consulta("WHERE local = 'TOPO-2' AND status = 'A'");

        $caminho = HOME_URL . "/produto/home/";

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        
        $mostraBanner = FALSE;
        require ABSPATH . "/view/home/view-home.php";

        require ABSPATH . "/view/_include/footer.php";
    }

    public function busca() {

        $modelo = $this->loadModel("produto/model-produto");

        // Carrega o modelo para este view
        if (chkArray($_GET, "busca") || chkArray($this->parametros, 1)) {
            $resultadoProd = $modelo->buscaProduto();
            $caminho = HOME_URL . "/produto/busca/";

            if (count($resultadoProd) <= 6) {
                $modelo->resultadoPorPagina = 6;
                $resultadoProSemelhante = $modelo->buscaProduto(TRUE);

                $idExcluido = array();
                foreach ((ARRAY) $resultadoProd as $ind => $prod) {
                    if (is_numeric($ind)) {
                        foreach ((ARRAY) $resultadoProSemelhante as $ind2 => $prod2) {
                            if (is_numeric($ind2)) {
                                if ($prod['id'] == $prod2['id']) {
                                    unset($resultadoProSemelhante[$ind2]);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $resultadoProd = $modelo->listarProdutos("WHERE destaque = 'S' AND id NOT IN (SELECT produtoFK FROM produto_combinacao WHERE estoque <= 0)");
            $caminho = HOME_URL . "/produto/home/";
        }
        $resultadoProdPromo = $modelo->listarProdutos("WHERE promocao = 'S'");
        
        $this->db->tabela = "banner_secundario";
        $consultaBannerSec = $this->db->consulta("WHERE local = 'TOPO-2' AND status = 'A'");

        // Titulo da página
        $this->title = "Home";

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        $mostraBanner = FALSE;
        require ABSPATH . "/view/home/view-home.php";

        require ABSPATH . "/view/_include/footer.php";
    }

    public function categoria() {
        $idCategoria = $this->parametros[0];

        $this->db->tabela = "categoria";
        $categoria = $this->db->consultaId($idCategoria);
        $consultaSubCatMenu = $this->db->consulta("WHERE categoriaFK = '$idCategoria'");

        // Titulo da página
        $this->title = $categoria["titulo"];

        if (!is_numeric($this->parametros[1])) {
            $this->parametros[0] = 1;
        } else {
            $this->parametros[0] = $this->parametros[1];
        }

        $modelo = $this->loadModel("produto/model-produto");
        $modelo->ordenacao = "ORDER BY id DESC";

        if (isset($_SESSION["FILTRAR"]["LIMITE"])) {
            $modelo->resultadoPorPagina = $_SESSION["FILTRAR"]["LIMITE"];
        } else {
            $modelo->resultadoPorPagina = 15;
        }
        $resultadoProd = $modelo->listarProdutos("WHERE id IN (SELECT produtoFK FROM produto_categoria WHERE categoriaFK = '" . $idCategoria . "') AND status = 'A'");
        $resultadoProdPromo = $modelo->listarProdutos("WHERE promocao = 'S' AND status = 'A'");
        if ($categoria['categoriaFK'] != "" && count($resultadoProd) <= 6) {
            $modelo->resultadoPorPagina = 6;
            $resultadoProSemelhante = $modelo->listarProdutos("WHERE id IN (SELECT produtoFK FROM produto_categoria WHERE categoriaFK = '" . $categoria['categoriaFK'] . "') AND status = 'A'");
        }
        $caminho = HOME_URL . "/produto/categoria/" . $idCategoria . "/";
        
        $this->db->tabela = "banner_secundario";
        $consultaBannerSec = $this->db->consulta("WHERE local = 'TOPO-2' AND status = 'A'");

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        $mostraBanner = FALSE;
        require ABSPATH . "/view/home/view-home.php";
        if ($categoria["texto"] != "") {
            require ABSPATH . "/view/produto/view-categoriaDescricao.php";
        }
        require ABSPATH . "/view/_include/footer.php";
    }

    public function ajaxVerificaVariacao() {

        $idProduto = $_POST["produto"];
        $idOpcao = $_POST["opcao"];
        $outrasVariacoes = $_POST["arrayVariacoes"];
        $outrasVariacoes = explode(",", $outrasVariacoes);

        $this->db->tabela = "ligacao_produto_variacoes";
        $this->db->limite = 9999999;
        $consulta = $this->db->consulta("WHERE variacao_valor_id = '$idOpcao' AND produto_fk = '$idProduto'", "", "", "", "", "", "", TRUE, TRUE);
        while ($linha = mysql_fetch_assoc($consulta)) {
            foreach ($outrasVariacoes as $ind => $variacao) {
                $variacao = intval($variacao);
                if ($variacao > 0) {
                    $consultaRelacao = $this->db->consulta("WHERE variacao_valor_id = '$variacao' AND combinacao_id = '" . $linha["combinacao_id"] . "'", "", "", "", "", "", "", TRUE, TRUE);
                    if (!mysql_num_rows($consultaRelacao)) {
                        $variacoesReprovadas[] = $variacao;
                    } else {
                        $variacoesAprovadas[] = $variacao;
                    }
                }
            }
        }


        if (isset($variacoesReprovadas)) {
            foreach ($variacoesReprovadas as $valor) {
                if (!in_array($valor, $variacoesAprovadas)) {
                    $retorno[] = $valor;
                }
            }
        } elseif (isset($variacoesAprovadas)) {
            foreach ($variacoesAprovadas as $valor) {
                $retorno[] = $valor;
            }
        } else {
            
        }

        if (!isset($retorno)) {
            $retorno = array();
        }

        echo json_encode($retorno);
    }

    public function atribuiVariacao() {

        $idProduto = $_POST["produto"];
        $variacoes = $_POST["arrayVariacoes"];
        $variacoes = explode(", ", $variacoes);
        $modelo = $this->loadModel("produto/model-produto");
        unset($_SESSION["VARIACAO"]);


        $cont = 0;
        $join = "";
        foreach ($variacoes as $variacao) {
            $variacao = intval($variacao);
            if ($variacao > 0) {
                $this->db->tabela = "variacao_valor";
                $variacaoValor = $this->db->consultaId($variacao);
                $_SESSION["VARIACAO"][$variacaoValor["variacaoFK"]] = $variacao;

                $cont++;
                $join .= "JOIN produto_combinacao_valor pcv{$cont} ON pcv{$cont}.variacaoFK = '$variacao' AND pcv{$cont}.combinacaoFK = pc.id ";
            }
        }

        $this->db->tabela = "produto_combinacao";
        $consultaVariacao = $this->db->consulta("WHERE produtoFK = '$idProduto'", "", "", $join, "", "pc.*", "pc", TRUE, TRUE);
        if (mysql_num_rows($consultaVariacao)) {
            $opcao = mysql_fetch_assoc($consultaVariacao);

            if ($opcao["estoque"] <= 0) {
                echo "INDISPONIVEL";
            } else {

                ob_start();

                $parcelamento = $modelo->parcelaSemJuros();
                if (isset($parcelamento["0"]["parcelaSemJuros"])) {
                    $valorParcelado = $opcao["valorPor"] / $parcelamento["0"]["parcelaSemJuros"];
                    if ($valorParcelado >= 5) {
                        $textoParcelamento = $parcelamento["0"]["parcelaSemJuros"] . "x de R$ " . dinheiro($valorParcelado) . " sem juros";
                    }
                }
                ?>
                <span id="valorDe" class="light size-1-2 linha-sobre"><?= (chkArray($opcao, "valorDe")) ? "De " . chkArray($opcao, "valorDe") : "" ?></span><br/>
                <span class="size-1">Por Apenas</span><br/>
                <strong id="valorPor" class="size-3 color-red"><?= (is_numeric($opcao["valorPor"])) ? "R$ " . number_format($opcao["valorPor"], 2, ",", ".") : $opcao["valorPor"] ?></strong><br/>
                <span class="size-1-2 textoParcelamento color-dark-gray"><?= (isset($textoParcelamento)) ? $textoParcelamento : "" ?></span>
                <?
                $retorno["valores"] = ob_get_clean();

                ob_start();
                $this->db->tabela = "forma_pagamento";
                $modeloFormaPagamento = $this->loadModel("formaPagamento/model-formaPagamento");
                $consultaFormaPagamento = $this->db->consulta("WHERE status = 'A'");
                $conta = 0;
                while ($formaPagamento = mysql_fetch_assoc($consultaFormaPagamento)) {
                    $modeloFormaPagamento->formaPagamento = $formaPagamento["classe"];
                    $produto["formapagamento"][$conta]["titulo"] = $formaPagamento["titulo"];
                    $produto["formapagamento"][$conta]["classe"] = $formaPagamento["classe"];
                    $produto["formapagamento"][$conta]["semJuros"] = $modeloFormaPagamento->encontrarParcelaSemJuros($formaPagamento["classe"]);
                    $produto["formapagamento"][$conta]["parcela"] = $modeloFormaPagamento->calcularJuros($opcao["valorPor"]);
                    $conta++;
                }
                $retorno["valorPor"] = $opcao["valorPor"];
                require ABSPATH . "/view/produto/view-parcelamento.php";
                $retorno["parcelamento"] = ob_get_clean();

                $retorno["estoque"] = $opcao["estoque"];
                $retorno["variacao"] = $opcao["id"];

                echo json_encode($retorno);
            }
        }
    }

    public function ajaxVariacao() {

        $modelo = $this->loadModel("produto/model-produto");

        $this->db->tabela = "variacao_valor";
        $variacaoValor = $this->db->consultaId($_POST["variacaoFK"]);

        $_SESSION["VARIACAO"][$variacaoValor["variacaoFK"]] = $_POST["variacaoFK"];

        $this->db->tabela = "produto_combinacao_valor";
        $consulta = $this->db->consulta("WHERE pcv.variacaoFK = '" . $_POST["variacaoFK"] . "'", "", "", "JOIN produto_combinacao_valor pcv2 ON pcv.combinacaoFK = pcv2.combinacaoFK AND pcv.variacaoFK != pcv2.variacaoFK", "", "pcv2.*", "pcv", TRUE);
        while ($campo = mysql_fetch_assoc($consulta)) {
            $retorno["variacaoDisponivel"][] = $campo["variacaoFK"];
        }

        $join = "";
        $variacoes = array();
        $cont = 1;
        foreach ((array) $_SESSION["VARIACAO"] as $ind => $variacao) {
            if ($_POST["variacaoFK"] != $variacao) {
                $consulta = $this->db->consulta("WHERE pcv.variacaoFK = '" . $_POST["variacaoFK"] . "' AND pcv2.variacaoFK = '" . $variacao . "'", "", "", "JOIN produto_combinacao_valor pcv2 ON pcv.combinacaoFK = pcv2.combinacaoFK AND pcv.variacaoFK != pcv2.variacaoFK", "", "pcv2.*", "pcv", TRUE);
                if (!mysql_num_rows($consulta)) {
                    unset($_SESSION["VARIACAO"][$ind]);
                }
            }

            $join .= "JOIN produto_combinacao_valor pcv{$cont} ON pcv{$cont}.combinacaoFK = pc.id AND pcv{$cont}.variacaoFK = '$variacao'";
            $variacoes[] = $variacao;
            $cont++;
        }

        $this->db->tabela = "produto_combinacao";
        $consulta = $this->db->consulta("WHERE pc.produtoFK = '" . $_POST["produtoFK"] . "'", "", "", $join, "", "pc.*", "pc", TRUE);
        $liberar = TRUE;
        if (mysql_num_rows($consulta) == 1) {
            $produtoVariacao = mysql_fetch_assoc($consulta);
            $this->db->tabela = "produto_combinacao_valor";
            $consulta = $this->db->consulta("WHERE combinacaoFK = '" . $produtoVariacao["id"] . "'");
            while ($campo = mysql_fetch_assoc($consulta)) {
                if (!in_array($campo["variacaoFK"], $variacoes)) {
                    $liberar = FALSE;
                }
            }

            if ($liberar) {
                $retorno["liberar"] = true;
                if ($produtoVariacao["estoque"] <= 0) {
                    $retorno["indisponivel"] = true;
                } else {
                    $retorno["indisponivel"] = false;
                }
                $retorno["variacao"] = $produtoVariacao["id"];
                $retorno["estoque"] = $produtoVariacao["estoque"];
                $retorno["valorDe"] = ($produtoVariacao["valorDe"]) ? "De R$ " . number_format($produtoVariacao["valorDe"], 2, ",", ".") : "";
                $retorno["valorPor"] = "R$ " . number_format($produtoVariacao["valorPor"], 2, ",", ".");
                $parcelamento = $modelo->parcelaSemJuros();
                if (isset($parcelamento["0"])) {
                    if ($parcelamento["0"]["parcelaSemJuros"] > 0) {
                        $retorno["textoParcelamento"] = $parcelamento["0"]["parcelaSemJuros"] . "x de R$ " . dinheiro($produtoVariacao["valorPor"] / $parcelamento["0"]["parcelaSemJuros"]) . " sem juros";
                    }
                }

                ob_start();
                $this->db->tabela = "forma_pagamento";
                $modeloFormaPagamento = $this->loadModel("formaPagamento/model-formaPagamento");
                $consultaFormaPagamento = $this->db->consulta("WHERE status = 'A'");
                $conta = 0;
                while ($formaPagamento = mysql_fetch_assoc($consultaFormaPagamento)) {
                    $modeloFormaPagamento->formaPagamento = $formaPagamento["classe"];
                    $produto["formapagamento"][$conta]["titulo"] = $formaPagamento["titulo"];
                    $produto["formapagamento"][$conta]["classe"] = $formaPagamento["classe"];
                    $produto["formapagamento"][$conta]["semJuros"] = $modeloFormaPagamento->encontrarParcelaSemJuros($formaPagamento["classe"]);
                    $produto["formapagamento"][$conta]["parcela"] = $modeloFormaPagamento->calcularJuros($produtoVariacao["valorPor"]);
                    $conta++;
                }
                require ABSPATH . "/view/produto/view-parcelamento.php";
                $retorno["parcelamento"] = ob_get_clean();
            }
        } else {
            $retorno = array();
        }

        echo json_encode($retorno);
    }

    public function indicarProduto() {

        $model = $this->loadModel("produto/model-produto");
        $model->parametros["nome"] = $name = strip_tags($_POST['nome']);
        $model->parametros["email"] = $email = strip_tags($_POST['email']);
        $model->parametros["nomeAmigo"] = $nomeAmigo = strip_tags($_POST['nomeAmigo']);
        $model->parametros["emailAmigo"] = $emailAmigo = strip_tags($_POST['emailAmigo']);
        $model->parametros["produtoFK"] = $produtoFK = strip_tags($_POST['produtoFK']);
        $resultado = $model->indicarProduto();

        if ($resultado) {
            require_once ABSPATH . '/controller/controller-email.php';

            $controladoEmail = new ControllerEmail();
            $controladoEmail->parametros[0] = "indicar_amigo";
            $controladoEmail->parametros[1] = $produtoFK;
            $controladoEmail->parametros["POST"] = $model->parametros;
            $controladoEmail->index();

            echo "ENVIADO";
        } else {
            echo "ERRO";
        }
    }

    public function ajaxFiltrar() {

        $tipoFiltro = $this->parametros[0];
        $valorFiltro = $this->parametros[1];
        
        switch ($tipoFiltro) {
            case "cat":
                $_SESSION['FILTRO']['CATEGORIA']['WHERE'] = "AND id IN (SELECT produtoFK FROM produto_categoria WHERE categoriaFK = '" . (int) $valorFiltro . "')";
                $_SESSION['FILTRO']['CATEGORIA']['OPT'] = (int) $valorFiltro;
                break;
            default:
                switch ($valorFiltro) {
                    case 1:
                        $valor1 = 0;
                        $valor2 = 99.99;
                        break;
                    case 2:
                        $valor1 = 100;
                        $valor2 = 299.99;
                        break;
                    case 3:
                        $valor1 = 300;
                        $valor2 = 499.99;
                        break;
                    case 4:
                        $valor1 = 500;
                        $valor2 = 999.99;
                        break;
                    case 5:
                        $valor1 = 1000;
                        $valor2 = 1499.99;
                        break;
                    case 6:
                        $valor1 = 1500;
                        $valor2 = 999999.99;
                        break;
                }
                $_SESSION['FILTRO']['PRECO']['WHERE'] = "AND id IN (SELECT produtoFK FROM produto_combinacao WHERE valorPor BETWEEN $valor1 AND $valor2)";
                $_SESSION['FILTRO']['PRECO']['OPT'] = $valorFiltro;
                break;
        }
        
    }
    
    public function ajaxLimpaFiltro() {
        unset($_SESSION['FILTRO']);
    }

    public function ajaxCategoriaLateral() {

        $idCategoria = $_POST["idCat"];
        $this->db->tabela = "categoria";
        $subCategoria = $this->db->consultaId($idCategoria);
        ?>
        <li>
            <div class="titulo-barra-lateral source size-1-6 color-dark-gray semi-bold">
                <i class="glyphicon glyphicon-menu-left size-2-2 light color-light-gray" onclick="carregaCategoriaLateral('', 'sub')"></i>   <span><?= $subCategoria["titulo"] ?></span>
            </div>
        </li>
        <?
        $consultaCat = $this->db->consulta("WHERE categoriaFK = '$idCategoria'", "ORDER BY titulo ASC");
        while ($linhaCat = mysql_fetch_assoc($consultaCat)) {
            ?>
            <li>
                <span>
                    <a href="<?= HOME_URL ?>/produto/categoria/<?= $linhaCat["id"] ?>/<?= arrumaString($linhaCat["titulo"]) ?>.html#conteudo" class="color-padrao source normal size-1-6">
                        <?= $linhaCat["titulo"] ?>
                    </a>
                </span>
            </li>
        <? } ?>
        <?
    }

}
