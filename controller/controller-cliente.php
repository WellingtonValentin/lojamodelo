<?

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerCliente extends MainController {

    /**
     * Carrega a página "/view/home/index.php"
     */
    public function index() {
        // Titulo da página
        $this->title = "";

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/atendimento/view-atendimento.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function login() {

        // Titulo da página
        $this->title = "Efetue seu login ou cadastre-se";

        $login = new Login();
        if (isset($_POST["cadastrarCliente"])) {
            $model = $this->loadModel("cliente/model-cliente");
            $model->parametros = $_POST;
            $resultadoCadastro = $model->cadastrar();

            if ($resultadoCadastro["STATUS"] == "SUCESSO") {
                require_once ABSPATH . '/controller/controller-email.php';

                $controladoEmail = new ControllerEmail();
                $controladoEmail->parametros[0] = "cadastro";
                $controladoEmail->index();

                $_POST["CLIENTE"]["email"] = $_POST["email"];
                $_POST["CLIENTE"]["senha"] = md5($_POST["senha"]);
                $login->VerificaLogin();
            }
        } elseif (isset($_POST["loginCliente"])) {
            $_POST["CLIENTE"]["email"] = $_POST["email"];
            $_POST["CLIENTE"]["senha"] = md5($_POST["senha"]);
            $login->VerificaLogin();

            $resultadoLogin = $login->login_error;
        }

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        if ($login->logado) {
            $urlRedirecionamento = HOME_URL . "/cliente/dados/meus-dados.html";
            $login->gotoPage($urlRedirecionamento);
        } else {
            require ABSPATH . "/view/cliente/view-login.php";
        }
        require ABSPATH . "/view/_include/footer.php";
    }

    public function esqueciSenha() {

        $model = $this->loadModel("cliente/model-cliente");
        $model->parametros = $_POST;
        $resultado = $model->esqueciSenha();
        if (chkArray($resultado, "status")) {
            require_once ABSPATH . '/controller/controller-email.php';

            $controladoEmail = new ControllerEmail();
            $controladoEmail->parametros[0] = "esqueci_senha";
            $controladoEmail->parametros[1] = $resultado["id"];
            $controladoEmail->index();

            echo "ENVIADO";
        } else {
            echo "ERRO";
        }
    }

    public function alterarSenha() {

        $this->title = "Alterar Senha";
        $model = $this->loadModel("cliente/model-cliente");

        $resultado = "";
        if (isset($_POST["alterar_senha"])) {
            $model->parametros = $_POST;
            $model->parametros["codigoConfirmacao"] = dr($this->parametros[0]);
            $resultado = $model->trocarSenha();
        }

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-alterarSenha.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function sair() {

        $redirecionamento = HOME_URL . "/cliente/login/login.html";
        $login = new Login();
        $login->logout($redirecionamento);
    }

    public function dados() {

        $this->title = "Meus dados";

        $login = new Login();
        $login->VerificaLogin();
        if (!$login->logado) {
            $redirecionamento = HOME_URL . "/cliente/login/login.html";
            $login->logout($redirecionamento);
        }

        $modelo = $this->loadModel("cliente/model-cliente");
        if (isset($_POST["meusDados"])) {
            if (isset($_POST["data_nascimento"])) {
                unset($_POST["data_nascimento"]);
            }
            if (isset($_POST["documento"])) {
                unset($_POST["documento"]);
            }
            if (isset($_POST["email"])) {
                unset($_POST["email"]);
            }
            if (isset($_POST["tipo"])) {
                unset($_POST["tipo"]);
            }

            $modelo->parametros = $_POST;
            $modelo->db->required = array(
                "nome" => "Nome"
            );

            $resultado = $modelo->alterarDados($login->userdata["id"]);
        } elseif (isset($_POST["enderecoPrincipal"])) {
            $modelo->parametros = $_POST;
            $resultado = $modelo->alterarDados($login->userdata["id"]);
        } elseif (isset($_POST["alterarSenha"])) {
            $consulta = $this->db->consulta("WHERE senha = '" . md5($_POST["senhaAtual"]) . "' AND id = '" . $login->userdata["id"] . "'");
            if (mysql_num_rows($consulta)) {
                if ($_POST["senhaNova"] != $_POST["senhaConfirma"]) {
                    $erro = "Nova senha e confirmação não conferem!";
                } else {
                    $_POST["senha"] = md5($_POST["senhaNova"]);
                    $modelo->parametros = $_POST;
                    $resultado = $modelo->alterarDados($login->userdata["id"]);
                    $sucesso = "Senha alterada com sucesso!";
                }
            } else {
                $erro = "Senha informada não confere com  senha atual!";
            }
        }

        if (isset($resultado)) {
            $_SESSION["CLIENTE"] = $resultado;
            $login = new Login();
            $login->VerificaLogin();
        }

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-meusDados.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function enderecoEntrega() {

        $this->title = "Endereço de Entrega";
        $enderecoCliente = array();
        require_once ABSPATH . '/function/function-criptografia.php';

        $login = new Login();
        $login->VerificaLogin();
        if (!$login->logado) {
            $redirecionamento = HOME_URL . "/cliente/login/login.html";
            $login->logout($redirecionamento);
        }

        if (isset($_POST)) {
            if (count($_POST) > 0) {
                $idEndereco = "";
                if (is_numeric(dr($this->parametros["0"]))) {
                    $idEndereco = dr($this->parametros["0"]);
                }

                $_POST["clienteFK"] = $login->userdata["id"];
                $this->db->tabela = "cliente_endereco";
                $this->db->importArray($_POST);
                $this->db->persist($idEndereco);
            }
        }

        $modelo = $this->loadModel("cliente/model-cliente");
        $listagemEndereco = $modelo->listarTabelaFilha($login->userdata["id"], "cliente_endereco");

        if (is_numeric(dr($this->parametros["0"]))) {

            $idEndereco = dr($this->parametros["0"]);
            $this->db->tabela = "cliente_endereco";
            $enderecoCliente = $this->db->consultaId($idEndereco, " AND clienteFK = '" . $login->userdata["id"] . "'");
        } elseif ($this->parametros["0"] == "apagar") {
            if (is_numeric(dr($this->parametros["1"]))) {
                $idEndereco = dr($this->parametros["1"]);

                $this->db->tabela = "cliente_endereco";
                $this->db->apagaId("", "id = '$idEndereco' AND clienteFK = '" . $login->userdata["id"] . "'");
            }
        }

        $paginas["0"]["link"] = "/cliente/dados/meus-dados.htm";
        $paginas["0"]["titulo"] = "Meus dados";
        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-enderecoEntrega.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function favoritos() {

        $this->title = "Meus Favoritos";

        $login = new Login();
        $login->VerificaLogin();
        if (!$login->logado) {
            $redirecionamento = HOME_URL . "/cliente/login/login.html";
            $login->logout($redirecionamento);
        }

        if ($this->parametros["0"] == "apagar") {
            if (is_numeric(dr($this->parametros["1"]))) {
                $id = dr($this->parametros["1"]);

                $this->db->tabela = "cliente_lista_desejo";
                $this->db->apagaId("", "id = '$id' AND clienteFK = '" . $login->userdata["id"] . "'");
            }
        }

        $modelo = $this->loadModel("cliente/model-cliente");
        $listagemFavorito = $modelo->listarTabelaFilha($login->userdata["id"], "cliente_lista_desejo");

        $paginas["0"]["link"] = "/cliente/dados/meus-dados.htm";
        $paginas["0"]["titulo"] = "Meus dados";
        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-favorito.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function cupom() {

        $this->title = "Meus Cupons";

        $login = new Login();
        $login->VerificaLogin();
        if (!$login->logado) {
            $redirecionamento = HOME_URL . "/cliente/login/login.html";
            $login->logout($redirecionamento);
        }

        $modelo = $this->loadModel("cliente/model-cliente");
        $listagemCupom = $modelo->listarTabelaFilha($login->userdata["id"], "cupom", " OR documento = '" . $login->userdata["documento"] . "'", "ORDER BY status ASC");

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-cupom.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function pedido() {

        $this->title = "Meus Pedidos";

        $login = new Login();
        $login->VerificaLogin();
        if (!$login->logado) {
            $redirecionamento = HOME_URL . "/cliente/login/login.html";
            $login->logout($redirecionamento);
        }

        if (isset($_POST["status"])) {
            $order = "";
            $join = "JOIN pedido_status ps ON ps.pedidoFK = p.id AND ps.`status` = '" . $_POST["status"] . "'";
            switch ($_POST["status"]) {
                case "AGUARDANDO":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('CALCULANDO FRETE','PAGO','EMBALANDO','DESPACHADO','ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "CALCULANDO FRETE":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('PAGO','EMBALANDO','DESPACHADO','ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "PAGO":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('EMBALANDO','DESPACHADO','ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "EMBALANDO":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('DESPACHADO','ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "DESPACHADO":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "ENTREGUE":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "DISPUTA":
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "CANCELADO":
                case "DEVOLVIDO":
                    break;
                default:
                    $order = "ORDER BY id DESC";
                    $join = "";
                    $where = "";
                    break;
            }
        } else {
            $order = "";
            switch ($this->parametros[0]) {
                case "pedido-aberto":
                    $join = "JOIN pedido_status ps ON ps.pedidoFK = p.id AND ps.`status` = 'AGUARDANDO'";
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('CALCULANDO FRETE','PAGO','EMBALANDO','DESPACHADO','ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "pedido-pago":
                    $join = "JOIN pedido_status ps ON ps.pedidoFK = p.id AND ps.`status` = 'PAGO'";
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('EMBALANDO','DESPACHADO','ENTREGUE','DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                case "pedido-entregue":
                    $join = "JOIN pedido_status ps ON ps.pedidoFK = p.id AND ps.`status` = 'ENTREGUE'";
                    $where = "AND p.id NOT IN ("
                        . "SELECT ps2.pedidoFK FROM pedido_status ps2 "
                        . "WHERE ps2.`status` "
                        . "IN ('DISPUTA','CANCELADO','CREDITADO','DEVOLVIDO'))";
                    break;
                default:
                    $order = "ORDER BY id DESC";
                    $join = "";
                    $where = "";
                    break;
            }
        }

        $modelo = $this->loadModel("cliente/model-cliente");
        $listagemPedido = $modelo->listarTabelaFilha($login->userdata["id"], "pedido", $where, $order, $join);

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-pedido.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function detalhePedido() {

        $this->title = "Detalhes do Pedido";

        $login = new Login();
        $login->VerificaLogin();
        if (!$login->logado) {
            $redirecionamento = HOME_URL . "/cliente/login/login.html";
            $login->logout($redirecionamento);
        }

        require_once ABSPATH . '/function/function-criptografia.php';
        $idPedido = dr($this->parametros["0"]);
        $modelo = $this->loadModel("pedido/model-pedido");
        $pedido = $modelo->detalharPedido($idPedido, $login->userdata["id"]);

        if (chkArray($pedido, "tipoFrete") != "TRANSPORTADORA" && chkArray($pedido, "tipoFrete") != "FRETE A CALCULAR" && $pedido["valorStatus"] == 20 && $pedido["valorFinal"] != "R$ 0,00") {
            $this->db->tabela = "forma_pagamento";
            $consulta = $this->db->consulta("WHERE status = 'A'");
            $cont = 0;
            while ($linha = mysql_fetch_array($consulta)) {
                $formasPagamento[$cont]["CLASSE"] = $linha["classe"];
                $formasPagamento[$cont]["TITULO"] = $linha["titulo"];
                $cont++;
            }
        }

        $paginas["0"]["link"] = "/cliente/pedido/pedido-aberto/meus-pedidos.htm";
        $paginas["0"]["titulo"] = "Meus Pedidos";
        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/cliente/view-detalhePedido.php";
        require ABSPATH . "/view/_include/footer.php";
    }

}
