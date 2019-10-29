<?

/**
 * Modelo para gerenciar as formas de pagamento
 * 
 */
class ModelNotificacoes extends MainModel {

    /**
     * ID do pedido salvo
     *
     * @var int 
     */
    public $idPedido = "";

    /**
     * Timeout em segundos
     *
     * @var type 
     */
    private $timeout = 20;

    /**
     * Token da loja no pagseguro
     *
     * @var type 
     */
    public $token = "";

    /**
     * E-mail da loja no pagseguro
     *
     * @var type 
     */
    public $emailLoja = "";

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
     * Função para cálcular o coeficiente de juros dos meios de pagamentos
     */
    public function receberNotificacao() {

        if (isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction') {

            $this->db->tabela = "config_valores";
            $configValores = $this->db->consultaId(1);
            if ($configValores["pagamentoSandbox"] == "N") {
                $url = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/' . $_POST['notificationCode'] . '?email=' . $this->emailLoja . '&token=' . $this->token;
            } else {
                $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications/' . $_POST['notificationCode'] . '?email=' . $this->emailLoja . '&token=' . $this->token;
            }

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $transaction = curl_exec($curl);
            curl_close($curl);

            if ($transaction != 'Unauthorized') {
                $transaction = simplexml_load_string($transaction);

                $json = json_encode($transaction);
                $arrayRetorno = json_decode($json, TRUE);

                $parametros["pedidoFK"] = $transaction->reference;
                $parametros["formaPagamento"] = "Pagseguro";
                $parametros["retorno"] = serialize($arrayRetorno);

                $this->db->tabela = "log_retorno_pagamento";
                $this->db->importArray($parametros);
                $this->db->persist();

                unset($parametros["formaPagamento"]);
                unset($parametros["retorno"]);

                switch ($transaction->status) {
                    // Aguardando pagamento
                    case 1:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'AGUARDANDO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "AGUARDANDO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        } else {
                            $this->db->tabela = "pedido_pagamento";
                            $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'AGUARDANDO'");
                            $pedido_pagamento = mysql_fetch_assoc($consulta);
                            
                            $parametros["formaPagamento"] = "PAGSEGURO";
                            $parametros["numeroParcela"] = $transaction->installmentCount;
                            switch ($transaction->paymentMethod->type) {
                                case 1:
                                    $parametros["tipoPagamento"] = "Cartão de crédito";
                                    break;
                                case 2:
                                    $parametros["tipoPagamento"] = "Boleto";
                                    break;
                                case 3:
                                    $parametros["tipoPagamento"] = "Débito online (TEF)";
                                    break;
                                case 4:
                                    $parametros["tipoPagamento"] = "Saldo PagSeguro";
                                    break;
                                case 5:
                                    $parametros["tipoPagamento"] = "Oi Paggo";
                                    break;
                                case 7:
                                    $parametros["tipoPagamento"] = "Depósito em conta";
                                    break;
                            }

                            $this->db->importArray($parametros);
                            $this->db->persist($pedido_pagamento['id']);
                        }
                        break;
                    // Em análise
                    case 2:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'AGUARDANDO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "AGUARDANDO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Paga
                    case 3:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'PAGO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "PAGO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }

                        $this->db->tabela = "pedido_pagamento";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'PAGO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "PAGO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["formaPagamento"] = "PAGSEGURO";
                            $parametros["numeroParcela"] = $transaction->installmentCount;

                            switch ($transaction->paymentMethod->type) {
                                case 1:
                                    $parametros["tipoPagamento"] = "Cartão de crédito";
                                    break;
                                case 2:
                                    $parametros["tipoPagamento"] = "Boleto";
                                    break;
                                case 3:
                                    $parametros["tipoPagamento"] = "Débito online (TEF)";
                                    break;
                                case 4:
                                    $parametros["tipoPagamento"] = "Saldo PagSeguro";
                                    break;
                                case 5:
                                    $parametros["tipoPagamento"] = "Oi Paggo";
                                    break;
                                case 7:
                                    $parametros["tipoPagamento"] = "Depósito em conta";
                                    break;
                            }

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Disponível
                    case 4:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'CREDITADO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "CREDITADO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Em disputa
                    case 5:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'DISPUTA'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "DISPUTA";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Devolvida
                    case 6:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'DEVOLVIDO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "DEVOLVIDO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Cancelada
                    case 7:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'CANCELADO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "CANCELADO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Chargeback debitado
                    case 8:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'DEVOLVIDO'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "DEVOLVIDO";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                    // Em contestação
                    case 9:
                        $this->db->tabela = "pedido_status";
                        $consulta = $this->db->consulta("WHERE pedidoFK = '" . $transaction->reference . "' AND status = 'DISPUTA'");
                        if (!mysql_num_rows($consulta)) {
                            $parametros["status"] = "DISPUTA";
                            $parametros["data"] = date("d/m/Y H:i:s");
                            $parametros["observacao"] = "Atualização de status vinda do Pagseguro.";

                            $this->db->importArray($parametros);
                            $this->db->persist();
                        }
                        break;
                }
            } else {
                $parametros["formaPagamento"] = "Pagseguro";
                $parametros["retorno"] = "Post não autorizado no model";

                $this->db->tabela = "log_retorno_pagamento";
                $this->db->importArray($parametros);
                $this->db->persist();
            }
        } else {
            $parametros["formaPagamento"] = "Pagseguro";
            $parametros["retorno"] = "Post não validado no model";

            $this->db->tabela = "log_retorno_pagamento";
            $this->db->importArray($parametros);
            $this->db->persist();
        }
    }

}
