<?php

/**
 * Controlador da p�gina principal e controlador
 * padr�o para quando n�o encontrar algum m�todo
 * 
 */
class ControllerEmail extends MainController {

    /**
     * Carrega a p�gina "/view/home/index.php"
     */
    public function index() {

        require_once ABSPATH . '/enum.php';

        // Titulo da p�gina
        $assunto = $assuntoEmail[$this->parametros[0]];
        $this->title = $assunto;

        $modelo = $this->loadModel("email/model-email");

        ob_start();
        if ((chkArray($this->parametros, 0) == "cadastro" || chkArray($this->parametros, 0) == "contato" || chkArray($this->parametros, 0) == "indicar_amigo" || chkArray($this->parametros, 0) == "indicar_site") && $_POST) {
            $modelo->parametros["POST"] = $_POST;
        } else {
            $modelo->parametros = $this->parametros;
        }
        
        $resultado = $modelo->montarEmail();
        require ABSPATH . "/view/email/view-cabecalho.php";
        require ABSPATH . "/view/email/view-corpo.php";
        require ABSPATH . "/view/email/view-rodape.php";
        $modelo->conteudo = ob_get_clean();
        
        $modelo->enviar($conteudo);
        
    }

}
