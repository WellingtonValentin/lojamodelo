<?

/**
 * Modelo para gerenciar os clientes
 * 
 */
class ModelCliente extends MainModel {

    /**
     * $resultadoPorPagina
     * 
     * Receber� o n�mero de resultados por p�gina para configurar
     * a listagem e tamb�m para ser utilizada na pagina��o
     * 
     * @access public
     */
    public $resultadoPorPagina = 12;

    /**
     * $ordenacao
     * 
     * Receber� a ordena��o dos resultados da consulta
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

    /**
     * Cadastro de cliente com valida��o de campos obrigat�rios
     * 
     * @param array $parametro Conteudo do POST de cadastro
     * @return array Array com resultado do cadastro e mensagem para retorno
     */
    public function cadastrar() {

        $this->db->tabela = "cliente";
        $parametro = $this->parametros;
        $retorno = array();

        // Configura os campos obrigat�rios
        $this->db->required = array(
            "nome" => "Nome",
            "sobrenome" => "Sobrenome",
            "telefone1" => "Telefone",
            "cep" => "CEP",
            "endereco" => "Endere�o",
            "numero" => "N�mero",
            "bairro" => "Bairro",
            "cidade" => "Cidade",
            "email" => "E-mail",
            "senha" => "Senha"
        );

        // Adaptando nome do campo de acordo com o tipo de cadastro e definindo se estara ativo ou n�o o cadastro
        if (chkArray($parametro, "tipo") == "FISICA") {
            $this->db->required["documento"] = "CPF";
        } else {
            $this->db->required["documento2"] = "CNPJ";
            $parametro["documento"] = $parametro["documento2"];
            $parametro["status"] = "I";
        }

        // Verificando se as senhas s�o compat�veis
        if (chkArray($parametro, "senha") != chkArray($parametro, "senhaConfirma")) {
            $retorno["STATUS"] = "ERRO";
            $retorno["MSG"] = "Senha e Confirma��o de senha n�o batem!";
            return $retorno;
        }

        // Consulta se e-mail e documento j� est�o cadastrados
        $consulta = $this->db->consulta("WHERE email = '$parametro[email]'");
        $consulta2 = $this->db->consulta("WHERE documento = '$parametro[documento]'");
        if (mysql_num_rows($consulta)) {
            $retorno["STATUS"] = "ERRO";
            $retorno["MSG"] = "E-mail j� se encontra cadastrado!";
            return $retorno;
        } elseif (mysql_num_rows($consulta2)) {
            $retorno["STATUS"] = "ERRO";
            $retorno["MSG"] = $parametro["documento"] . " j� se encontra cadastrado!";
            return $retorno;
        }

        if (!validaEmail($parametro["email"])) {
            $retorno["STATUS"] = "ERRO";
            $retorno["MSG"] = "E-mail inv�lido!";
            return $retorno;
        }

        if ($parametro["tipo"] == "FISICA") {
            if (!validarCPF($parametro["documento"])) {
                $retorno["STATUS"] = "ERRO";
                $retorno["MSG"] = "CPF inv�lido!";
                return $retorno;
            }
        } else {
            if (!validarCNPJ($parametro["documento"])) {
                $retorno["STATUS"] = "ERRO";
                $retorno["MSG"] = "CNPJ inv�lido!";
                return $retorno;
            }
        }

        // Formatando os campos que restam para o cadastro
        $parametro["senha"] = md5($parametro["senha"]);
        $parametro["data"] = date("d-m-Y");

        // Efetua o cadastro
        $this->db->importArray($parametro);
        $id = $this->db->persist();

        // Verifica��o se foi feito o cadastro, caso n�o apresenta a msg de erro
        if ($this->db->status == "ERRO") {
            $retorno["STATUS"] = "ERRO";
            $retorno["MSG"] = $this->db->mensagem();
        } else {
            $retorno["STATUS"] = "SUCESSO";
            $retorno["MSG"] = "Cadastro efetuado com sucesso.";
        }
        return $retorno;
    }

    /**
     * Alterar dados do cliente
     * 
     * @param int $idCliente ID do cliente
     * @return boolean
     */
    public function alterarDados($idCliente) {

        $this->db->tabela = "cliente";
        $parametros = $this->parametros;

        // Efetua o cadastro
        $this->db->importArray($parametros);
        $id = $this->db->persist($idCliente);

        if (!is_array($id)) {
            $cliente = $this->db->consultaId($id);

            return $cliente;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param type $idCliente
     * @param type $tabela
     * @param type $filtro
     * @param type $order
     * @return type
     */
    public function listarTabelaFilha($idCliente, $tabela, $filtro = "", $order = "ORDER BY id DESC", $join = "") {

        $this->db->tabela = $tabela;
        if ($join) {
            $consulta = $this->db->consulta("WHERE clienteFK = '$idCliente' $filtro", "ORDER BY p.id DESC", "", 
                    $join, "GROUP BY p.id", "p.*", "p", TRUE, FALSE);
        } else {
            $consulta = $this->db->consulta("WHERE clienteFK = '$idCliente' $filtro", $order);
        }
        $resultado = $this->db->fetchAll($consulta);
        return $resultado;
    }

    /**
     * Fun��o para altera��o da senha do clietne para uma senha aleat�ria
     * 
     * @return boolean
     */
    public function esqueciSenha() {

        $this->db->tabela = "cliente";
        $consulta = $this->db->consulta("WHERE email = '" . $this->parametros["email"] . "'");
        if (mysql_num_rows($consulta)) {
            $campo = mysql_fetch_assoc($consulta);
            $retorno["id"] = $campo["id"];

            $parametros["codigoAlterarSenha"] = $codigo = gerarCodigo("cliente", "codigoAlterarSenha", "20", FALSE, TRUE, TRUE, FALSE);
            if (isset($campo["id"])) {
                $this->db->importArray($parametros);
                $resultado = $this->db->persist($campo["id"]);
                if (!is_array($resultado)) {
                    $retorno["status"] = TRUE;
                    return $retorno;
                }
            }
        }
        $retorno["status"] = FALSE;
        return $retorno;
    }

    /**
     * Fun��o para atualizar a senha do cliente
     * 
     * @return string
     */
    public function trocarSenha() {

        $this->db->tabela = "cliente";
        $consulta = $this->db->consulta("WHERE codigoAlterarSenha = '" . $this->parametros["codigoConfirmacao"] . "'");

        if (!$this->parametros["senha"]) {
            $retorno = "Favor informar a senha!";
        } elseif ($this->parametros["senha"] != $this->parametros["confirmar_senha"]) {
            $retorno = "Aten��o as senhas n�o batem!";
        } elseif (!mysql_num_rows($consulta)) {
            $retorno = "Cliente n�o encontrado!";
        } else {

            $cliente = mysql_fetch_assoc($consulta);
            $persist["senha"] = md5($this->parametros["senha"]);
            $persist["codigoAlterarSenha"] = "";
            $this->db->importArray($persist);
            $this->db->persist($cliente["id"]);
            $retorno = "OK";
        }

        return $retorno;
    }

    /**
     * Fun��o para guardar p�ginas visitadas pelo cliente
     * 
     * @param type $pagina
     */
    public function paginaVisitada($pagina = "") {

        $resultadoNavegador = getBrowser();
        $navegador = $resultadoNavegador['name'] . " v" . $resultadoNavegador['version'] . " - " . $resultadoNavegador['platform'];
        $ip = $_SERVER["REMOTE_ADDR"];
        if (isset($_SESSION["CLIENTE"])) {
            $idCliente = $_SESSION["CLIENTE"]["id"];
        } else {
            $idCliente = "";
        }
        $link = HOME_URL . str_replace("site/", "", $_SERVER ['REQUEST_URI']);

        $this->db->tabela = "relatorio_visita";
        $consulta = $this->db->consulta("WHERE (clienteFK = '$idCliente' OR ip = '$ip') AND status = 'ABERTO' ");
        if (mysql_num_rows($consulta)) {

            $relatorioVisita = mysql_fetch_assoc($consulta);
            $persist["clienteFK"] = $idCliente;
            $persist["ip"] = $ip;
            $this->db->importArray($persist);
            $this->db->persist($relatorioVisita["id"]);

            $visitaFK = $relatorioVisita["id"];
            
        } else {

            $persist["clienteFK"] = $idCliente;
            $persist["status"] = "ABERTO";
            $persist["data"] = date("d-m-Y H:i:s");
            $persist["ip"] = $ip;
            $persist["navegador"] = $navegador;
            $this->db->importArray($persist);
            $idRelatorio = $this->db->persist();

            if (!is_array($idRelatorio)) {
                $visitaFK = $idRelatorio;
            }
        }

        if (isset($visitaFK)) {
            $persist = array();
            $this->db->tabela = "relatorio_visita_pagina";
            $persist["visitaFK"] = $visitaFK;
            $persist["pagina"] = str_replace(array("'","'"), "", $pagina);
            $persist["link"] = $link;
            $persist["data"] = date("d-m-Y H:i:s");
            switch ($pagina) {
                case "Efetue seu login ou cadastre-se":
                case "Contato":
                case "Enviando Pedido":
                    $persist["conversao"] = "S";
                    break;
                default:
                    $persist["conversao"] = "N";
                    break;
            }
            $this->db->importArray($persist);
            $this->db->persist();
        }
    }

}
