<?php

/**
 * Controlador para os atendimentos do site
 * podendo ser cadastros de newsletter e contatos 
 */
class ControllerAtendimento extends MainController {

    /**
     * Carrega a p�gina "/atendimento/atendimento-online.html#conteudo"
     */
    public function index() {

        // Titulo da p�gina
        $this->title = "Atendimento On-line";

        // Chama o modelo de contru��o e envio de e-mail
        $modeloEmail = $this->loadModel("email/model-email");

        // Monta o e-mail de acordo com o tipo de e-mail que ser� enviado
        $modeloEmail->parametros = $this->parametros;
        $resultado = $modeloEmail->montarEmail();

        // Chama as view de corpo de e-mail nessas view o array $resultado
        // � utilizado para atribuir os parametros que s�o utilizados
        // para montar o e-mail
        ob_start();
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/atendimento/view-atendimento-online.php";
        require ABSPATH . "/view/_include/footer.php";
        $modeloEmail->conteudo = ob_get_clean();

        // Envia o e-mail
        $modeloEmail->enviar();
    }

    /**
     * Carrega a p�gina "/atendimento/contato/fale-conosco.html#conteudo"
     * Tamb�m efetua o envio do e-mail de contato caso tenha sido enviado
     * o formul�rio corretamente
     */
    public function contato() {


        // Cadastra o formul�rio de contato
        $modelo = $this->loadModel("atendimento/model-atendimento");
        $modelo->parametros = $_POST;
        $resultadoContato = $modelo->cadastrarContato();

        // Verifica se o formula�rio foi cadastrado corretamente
        // caso tenha conseguido dispara o e-mail com o contato
        if ($resultadoContato['status'] == "OK") {

            // Chama o modelo de contru��o e envio de e-mail
            $modeloEmail = $this->loadModel("email/model-email");

            // Monta o e-mail de acordo com o tipo de e-mail que ser� enviado
            $this->title = $modeloEmail->assunto = "Contato";
            $modeloEmail->parametros[0] = $this->parametros[0] = "contato";
            $modeloEmail->parametros["POST"] = $_POST;
            $resultado = $modeloEmail->montarEmail();

            // Chama as view de corpo de e-mail nessas view o array $resultado
            // � utilizado para atribuir os parametros que s�o utilizados
            // para montar o e-mail
            ob_start();
            require ABSPATH . "/view/email/view-cabecalho.php";
            require ABSPATH . "/view/email/view-corpo.php";
            require ABSPATH . "/view/email/view-rodape.php";
            $modeloEmail->conteudo = ob_get_clean();

            // Envia o e-mail
            $modeloEmail->enviar();
        }

        // Titulo da p�gina
        $this->title = "Fale Conosco";

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/atendimento/view-atendimento.php";
        require ABSPATH . "/view/_include/footer.php";
    }

}
