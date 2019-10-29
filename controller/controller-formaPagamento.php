<?

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerFormaPagamento extends MainController {

    /**
     * Retorno da forma de pagamentos
     */
    public function receberNotificacao() {
        $this->db->tabela = "forma_pagamento";

        if ($this->parametros[0] == "pagseguro") {
            $consulta = $this->db->consulta("WHERE classe = 'PAGSEGURO'");

            if (mysql_num_rows($consulta)) {
                $formaPagamento = mysql_fetch_assoc($consulta);
                
                $modelo = $this->loadModel("formapagamento/pagseguro/model-notificacoes");
                $modelo->token = $formaPagamento["token"];
                $modelo->emailLoja = $formaPagamento["email"];
                $modelo->receberNotificacao();
            }
        } else {
            $parametros["formaPagamento"] = "Não especificada";
            $parametros["retorno"] = "Post não validado no controller - " . serialize($_POST);

            $this->db->tabela = "log_retorno_pagamento"; 
            $this->db->importArray($parametros);
            $this->db->persist();
        }
    }

}
