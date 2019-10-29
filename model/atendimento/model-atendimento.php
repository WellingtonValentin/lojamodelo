<?php

/**
 * Modelo para gerenciar os atendimentos
 * sendo estes atendimento o cadastro nas newsletter e contatos
 * 
 */
class ModelAtendimento extends MainModel {

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
     * Fun��o de cadastro de newsletter
     * 
     * @return string Mensagem de resposta
     */
    public function cadastrarNewsletter() {

        // Definindo a tabela qe ser� utilizada nesta fun��o
        $this->db->tabela = "newsletter";


        // Verifica se foi mandado o nome e o email no post
        if (!chkArray($this->parametros, "email")) {
            $retorno["newsletter"]["msg"] = "Aten��o os campos s�o obrigat�rios!";
            $retorno["newsletter"]["erro"] = TRUE;
            return $retorno;
        }

        // Consulta se ja foi cadastrado o email e o nome do cliente
        $consulta = $this->db->consulta("WHERE email = '" . $this->parametros["email"] . "'");
        if (mysql_num_rows($consulta)) {
            $retorno["newsletter"]["msg"] = "Seu e-mail j� se encontra cadastrados no nosso sistema!";
            $retorno["newsletter"]["erro"] = TRUE;
            return $retorno;
        }

        // Grava a newsletter
        $this->db->importArray($this->parametros);
        $this->db->persist();
        $retorno["newsletter"]["msg"] = "Cadastro efetuado com sucesso!";
        $retorno["newsletter"]["erro"] = FALSE;
        return $retorno;
    }

    /**
     * Fun��o de cadastro de contato e envio de email.
     * 
     * @return boolean Retorna verdadeiro se conseguiu salvar o contato e falso se n�o conseguiu
     */
    public function cadastrarContato() {

        if (count($this->parametros) > 2) {
            // Complementa os parametros do post com a data atual e o assunto
            $this->parametros["data"] = date("d-m-Y H:i:s");
            $this->parametros["assunto"] = "Contato";

            // Gravando contato
            $this->db->required = array(
                "nome" => "Nome",
                "email" => "E-mail",
                "texto" => "Mensagem"
            );
            $this->db->tabela = "contato";
            $this->db->importArray($this->parametros);
            $this->db->persist();
            $retorno['msg'] = $this->db->mensagem();
            $retorno['status'] = $this->db->status;

            return $retorno;
        } else {
            return FALSE;
        }
    }

}
