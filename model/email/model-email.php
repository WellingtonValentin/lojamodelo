<?php

require_once ABSPATH . '/_utilitarios/phpMailer/PHPMailerAutoload.php';

/**
 * Modelo para gerenciar os e-mail
 * 
 */
class ModelEmail extends MainModel {

    public $destinatario = array();
    public $copia = array();
    public $conteudo = "";
    public $assunto = "";

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

        // Tras os dados da loja
        $this->db->tabela = "config";
        $empresa = $this->db->consultaId(1);

// Configurações de e-mail e SMTP
        $this->db->tabela = "config_email";
        $configEmail = $this->db->consultaId(1);

        $assuntoEmail = array(
            "contato" => "Contato on-line",
            "esqueci_senha" => "Alteração de senha",
            "indicar_amigo" => "Indicação de produto",
            "indicar_site" => "Indicação de site",
            "cadastro" => "Cadastro realizado",
            "cupom" => "Cupom de desconto",
            "pedido" => "Pedido realizado com sucesso",
            "produto_disponivel" => "Produto disponível na loja",
            "atualizacao_status" => "Alteração de status de pedido",
            "pesquisa_satisfacao" => "Pesquisa de satisfação"
        );

        $this->assunto = $assuntoEmail[$this->parametros[0]];

        // Configura o banco e seleciona a tabela
        $company = utf8_encode($empresa["titulo"]);

        $this->mail = new PHPMailer();
//        $this->mail->IsSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->CharSet = "UTF-8";
        $this->mail->Port = 587;
        $this->mail->IsHTML(true);
        $this->mail->Host = $configEmail["host"];
        $this->mail->Username = $configEmail["username"];
        $this->mail->Password = $configEmail["password"];
        $this->mail->From = $configEmail["email"];
        $this->mail->Sender = $configEmail["email"];
        $this->mail->FromName = $company;
        $this->mail->AddReplyTo($configEmail["email"], $company);
    }

    /**
     * Monta o corpo do e-mail para ser enviado
     * 
     * @return type
     */
    public function montarEmail() {
        $this->db->tabela = "email_conteudo";
        $consulta = $this->db->consulta("WHERE classe = '" . strtoupper($this->parametros[0]) . "'");
        $emailConteudo = $this->db->fetch($consulta);

        $this->db->tabela = "config";
        $empresa = $this->db->consultaId(1);

        $retorno = array();

        if (chkArray($emailConteudo, 0)) {
            if (chkArray($emailConteudo[0], "listaProduto") == "S") {
                $retorno["listaProduto"] = $this->listarProdutos();

                $this->db->tabela = "pedido";
                $pedido = $this->db->consultaId($this->parametros[1]);
                $retorno["fretePedido"] = $pedido["valorFrete"];
                $retorno["descontoPedido"] = $pedido["valorDesconto"];
                $retorno["formaEntrega"] = $pedido["tipoFrete"];
                $retorno["prazoEstimado"] = $pedido["prazoEstimado"];

                $this->db->tabela = "cliente";
                $cliente = $this->db->consultaId($pedido["clienteFK"]);
                $retorno["clienteNome"] = $cliente["nome"];
                $nomeCliente = $cliente["nome"];
                $destinatario["nome"] = $cliente["nome"];
                $destinatario["email"] = $cliente["email"];

                $this->db->tabela = "pedido_endereco";
                $consulta = $this->db->consulta("WHERE pedidoFK = '" . $this->parametros[1] . "'");
                $enderecoPedido = $this->db->fetch($consulta);
                $retorno["endereco"]["destinatario"] = $enderecoPedido[0]["destinatario"];
                $retorno["endereco"]["endereco"] = $enderecoPedido[0]["endereco"];
                $retorno["endereco"]["numero"] = $enderecoPedido[0]["numero"];
                $retorno["endereco"]["complemento"] = $enderecoPedido[0]["complemento"];
                $retorno["endereco"]["bairro"] = $enderecoPedido[0]["bairro"];
                $retorno["endereco"]["cidade"] = $enderecoPedido[0]["cidade"];
                $retorno["endereco"]["estado"] = $enderecoPedido[0]["estado"];
                $retorno["endereco"]["cep"] = $enderecoPedido[0]["cep"];

                if ($this->parametros[0] == "atualizacao_status") {
                    $this->db->tabela = "pedido_status";
                    $consulta = $this->db->consulta("WHERE pedidoFK = '" . $this->parametros[1] . "'", "ORDER BY data DESC");
                    $statusAtual = $this->db->fetch($consulta);


                    $data = explode(" ", $statusAtual[0]["data"]);
                    $data[0] = dataSite($data[0]);
                    $data = implode(" ", $data);
                    $retorno["status"]["titulo"] = $statusAtual[0]["status"];
                    $retorno["status"]["data"] = $data;
                }
            } elseif (chkArray($emailConteudo[0], "vincularProduto") == "S") {
                $retorno["produtos"] = $this->mostrarProduto();
            }
        }

        switch ($this->parametros[0]) {
            case "contato":
                $this->destinatario["nome"] = $empresa["titulo"];
                $this->destinatario["email"] = $empresa["email"];
                $retorno["contato"] = TRUE;
                $link = HOME_URL . "/admin";
                break;
            case "esqueci_senha":
                $this->db->tabela = "cliente";
                $cliente = $this->db->consultaId($this->parametros[1]);
                $nomeCliente = $cliente["nome"];
                $this->destinatario["nome"] = $cliente["nome"];
                $this->destinatario["email"] = $cliente["email"];
                $link = HOME_URL . "/cliente/alterarSenha/" . cr($cliente["codigoAlterarSenha"]) . "/esquei-minha-senha.html";
                break;
            case "indicar_amigo":
                $nomeCliente = $this->parametros["POST"]["nome"];
                $nomeAmigo = $this->parametros["POST"]["nomeAmigo"];
                $nomeProduto = $retorno["produtos"]["titulo"];
                $this->destinatario["nome"] = $this->parametros["POST"]["nomeAmigo"];
                $this->destinatario["email"] = $this->parametros["POST"]["emailAmigo"];
                $this->copia = array(
                    0 => array(
                        "nome" => $empresa["titulo"],
                        "email" => $empresa["email"]
                    ),
                    1 => array(
                        "nome" => $this->parametros["POST"]["nome"],
                        "email" => $this->parametros["POST"]["email"]
                    )
                );
                $link = HOME_URL . "/produto/detalhes/" . $this->parametros[1] . "/produto.html";
                break;
            case "indicar_site":
                $this->destinatario["nome"] = $this->parametros["POST"]["nomeAmigo"];
                $this->destinatario["email"] = $this->parametros["POST"]["emailAmigo"];
                $this->copia = array(
                    0 => array(
                        "nome" => $empresa["titulo"],
                        "email" => $empresa["email"]
                    ),
                    1 => array(
                        "nome" => $this->parametros["POST"]["nome"],
                        "email" => $this->parametros["POST"]["email"]
                    )
                );
                $nomeCliente = $this->parametros["POST"]["nome"];
                $nomeAmigo = $this->parametros["POST"]["nomeAmigo"];
                $link = HOME_URL;
                break;
            case "cadastro":
                $this->destinatario["nome"] = $this->parametros["POST"]["nome"];
                $this->destinatario["email"] = $this->parametros["POST"]["email"];
                $this->copia = array(
                    0 => array(
                        "nome" => $empresa["titulo"],
                        "email" => $empresa["email"]
                    )
                );
                $nomeCliente = $this->parametros["POST"]["nome"];
                $link = HOME_URL;
                break;
            case "cupom":
                $this->db->tabela = "cliente";
                $cliente = $this->db->consultaId($this->parametros[1]);
                $this->destinatario["nome"] = $cliente["nome"];
                $this->destinatario["email"] = $cliente["email"];
                $nomeCliente = $cliente["nome"];
                $link = HOME_URL . "/cliente/cupom/cupons-desconto.htm#conteudo";
                break;
            case "pedido":
                $this->db->tabela = "pedido";
                $pedido = $this->db->consultaId($this->parametros[1]);
                $this->db->tabela = "cliente";
                $cliente = $this->db->consultaId($pedido["clienteFK"]);
                $this->destinatario["nome"] = $cliente["nome"];
                $this->destinatario["email"] = $cliente["email"];
                $this->copia = array(
                    0 => array(
                        "nome" => $empresa["titulo"],
                        "email" => $empresa["email"]
                    )
                );
                $link = HOME_URL . "/cliente/detalhe-pedido/" . cr($this->parametros[1]) . "/detalhes-do-pedido.htm#conteudo";
                break;
            case "produto_disponivel":
                $this->db->tabela = "produto";
                $produto = $this->db->consultaId($this->parametros[1]);
                $nomeProduto = $produto["titulo"];
                $this->destinatario["nome"] = $cliente["nome"];
                $this->destinatario["email"] = $cliente["email"];
                $link = HOME_URL . "/produto/detalhes/" . $this->parametros[1] . "/produto.html";
                break;
            case "atualizacao_status":
                $this->db->tabela = "pedido";
                $pedido = $this->db->consultaId($this->parametros[1]);
                $this->db->tabela = "cliente";
                $cliente = $this->db->consultaId($pedido["clienteFK"]);
                $this->destinatario["nome"] = $cliente["nome"];
                $this->destinatario["email"] = $cliente["email"];
                $this->copia = array(
                    0 => array(
                        "nome" => $empresa["titulo"],
                        "email" => $empresa["email"]
                    )
                );
                $link = HOME_URL . "/cliente/detalhe-pedido/" . cr($this->parametros[1]) . "/detalhes-do-pedido.htm#conteudo";
                break;
            case "pesquisa_satisfacao":
                $this->db->tabela = "pedido";
                $pedido = $this->db->consultaId($this->parametros[1]);

                $this->db->tabela = "cliente";
                $cliente = $this->db->consultaId($pedido["clienteFK"]);
                $this->destinatario["nome"] = $cliente["nome"];
                $this->destinatario["email"] = $cliente["email"];
                $nomeCliente = $cliente["nome"];
                $link = HOME_URL . "/cliente/pesquisaSatisfacao/CODIGO/pesquisa-satisfacao.html";
                break;
        }

        if (chkArray($emailConteudo, 0)) {
            $conteudo = chkArray($emailConteudo[0], "conteudo");
        } else {
            $conteudo = "";
        }
        preg_match_all('/{(.*)}/U', $conteudo, $termos);
        if (isset($termos[1])) {
            foreach ($termos[1] as $ind => $termo) {
                switch ($termo) {
                    case "NOME_EMPRESA":
                        $conteudo = preg_replace('/{NOME_EMPRESA}/U', $empresa["titulo"], $conteudo);
                        break;
                    case "NOME_CLIENTE":
                        $conteudo = preg_replace('/{NOME_CLIENTE}/U', $nomeCliente, $conteudo);
                        break;
                    case "NOME_AMIGO":
                        $conteudo = preg_replace('/{NOME_AMIGO}/U', $nomeAmigo, $conteudo);
                        break;
                    case "NOME_PRODUTO":
                        $conteudo = preg_replace('/{NOME_PRODUTO}/U', $nomeProduto, $conteudo);
                        break;
                    case "LINK":
                        $link = "<a href='$link' target='_blank'>aqui</a>";
                        $conteudo = preg_replace('/{LINK}/U', $link, $conteudo);
                        break;
                    case "NUMERO_PEDIDO":
                        $conteudo = preg_replace('/{NUMERO_PEDIDO}/U', $this->parametros[1], $conteudo);
                        break;
                }
            }
        }
        $retorno["texto"] = $conteudo;

        return $retorno;
    }

    /**
     * Caso seja necessário esta função lista os produtos e os apresenta no corpo do e-mail
     * 
     * @return type
     */
    public function listarProdutos() {

        $retorno = array();
        $this->db->limit = 999999;
        $this->db->tabela = "pedido_produto";
        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $this->parametros[1] . "'");

        $cont = 0;
        while ($pedidoProduto = mysql_fetch_assoc($consulta)) {
            $this->db->tabela = "produto_combinacao";
            $combinacao = $this->db->consultaId($pedidoProduto["combinacaoFK"]);
            $this->db->tabela = "produto";
            $produto = $this->db->consultaId($combinacao["produtoFK"]);

            $titulo = $produto["titulo"];

            $this->db->tabela = "produto_combinacao_valor";
            $consultaProdutoCombi = $this->db->consulta("WHERE combinacaoFK = '" . $pedidoProduto["combinacaoFK"] . "'");
            while ($combinacaoValor = mysql_fetch_assoc($consultaProdutoCombi)) {
                $this->db->tabela = "variacao_valor";
                $variacaoValor = $this->db->consultaId($combinacaoValor["variacaoFK"]);
                $this->db->tabela = "variacao";
                $variacao = $this->db->consultaId($variacaoValor["variacaoFK"]);
                $titulo .= "<br/><span class=\"size-1\"><strong>" . $variacao["titulo"] . ": </strong>" . $variacaoValor["titulo"] . "</span>";
            }

            $retorno[$cont]["id"] = $produto["id"];
            $retorno[$cont]["titulo"] = $titulo;
            $retorno[$cont]["quantidade"] = $pedidoProduto["quantidade"];
            if (chkArray($pedidoProduto, 'valorEmbalagem')) {
                $retorno[$cont]["valor"] = $pedidoProduto["valor"] + $pedidoProduto['valorEmbalagem'];
                $retorno[$cont]["total"] = $pedidoProduto["quantidade"] * ($pedidoProduto["valor"] + $pedidoProduto['valorEmbalagem']);
            } else {
                $retorno[$cont]["valor"] = $pedidoProduto["valor"];
                $retorno[$cont]["total"] = $pedidoProduto["quantidade"] * $pedidoProduto["valor"];
            }
            $cont++;
        }

        return $retorno;
    }

    /**
     * Função para e-mail vinculado a produtos
     * 
     * @return type
     */
    public function mostrarProduto() {


        $model = $this->controller->loadModel("produto/model-produto");
        $detalhesProd = $model->detalharProduto($this->parametros[1]);

        $this->db->tabela = "produto";
        $produto = $this->db->consultaId($this->parametros[1]);

        $retorno["titulo"] = $produto["titulo"];
        $retorno["foto"] = $model->imagemPrincipal($this->parametros[1]);
        $retorno["valorDe"] = $detalhesProd["produto"]["valorDe"];
        $retorno["valorPor"] = $detalhesProd["produto"]["valorPor"];

        return $retorno;
    }

    /**
     * Dispara o e-mail para o cliente e salva o conteúdo no relatório de email
     * 
     * @param type $conteudo
     */
    public function enviar() {

        $this->mail->AddAddress($this->destinatario["email"], $this->destinatario["nome"]);
        if (isset($this->copia)) {
            foreach ($this->copia as $ind => $bcc) {
                $this->mail->AddBCC($bcc["email"], $bcc["nome"]);
            }
        }
        $this->mail->AddBCC("desenvolvimento6@byteabyte.com.br", "teste");

        $this->mail->Subject = utf8_encode($this->assunto);

        $this->mail->Body = utf8_encode($this->conteudo);
        $envio = $this->mail->Send();

        if ($envio) {
//            $this->db->tabela = "relatorio_email";
//            $parametros["emailFK"] = "";
//            $parametros["email"] = "";
//            $parametros["erro"] = "";
//            $parametros["data"] = "";
//            
//            $this->db->importArray($parametros);
//            $this->db->persist();
        } else {
            echo $erro = $this->mail->ErrorInfo;
//            $this->db->tabela = "relatorio_email";
//            $parametros["emailFK"] = "";
//            $parametros["email"] = "";
//            $parametros["erro"] = "";
//            $parametros["data"] = "";
//            
//            $this->db->importArray($parametros);
//            $this->db->persist();
        }
    }

}
