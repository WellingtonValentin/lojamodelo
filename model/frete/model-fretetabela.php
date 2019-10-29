<?php

class ModelFreteTabela extends MainModel {

    /**
     * CEP do destinatário
     *
     * @var type 
     */
    public $cep = "";

    /**
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    function calculaFrete($cep, $peso, $tipoEnvio = "") {
        $regiao = $this->checaRegiao();
        $divisa = $this->checaDivisa();
        $destino = $this->checaCapital();

        $objFrete = $this->controller->loadModel("frete/model-frete");
        $objFrete->cep = $this->cep;
        $enderecoCliente = $objFrete->buscarEndereco();

        $this->db->tabela = "config_frete";
        $configFrete = $this->db->consultaId(1);

        $this->db->tabela = "config";
        $empresa = $this->db->consultaId(1);
        if ($enderecoCliente["status"] == "erro") {
            $valorFrete["erro"] = 1;
            return $valorFrete;
        } elseif (chkArray($enderecoCliente, "uf")) {


            if ($tipoEnvio == "CARTA REGISTRADA" && $peso > 0.5) {

                $valorFrete["erro"] = 2;
                return $valorFrete;
            } else {

                if (isset($empresa["estado"])) {
                    $estadoEmpresa = $empresa["estado"];
                } else {
                    $estadoEmpresa = "";
                }

                if ($regiao || ( $enderecoCliente["cidade"] == $empresa["cidade"] && $enderecoCliente["uf"] == $estadoEmpresa)) {
                    $campo = "valorLocal";
                } elseif ($divisa) {
                    $campo = "valor" . strtoupper($empresa["uf"]);
                } else {
                    $campo = "valor" . strtoupper(substr($enderecoCliente["uf"], 0, 2));
                }
                if ($tipoEnvio) {
                    switch ($tipoEnvio) {
                        case "PAC":
                            $tipoEnvio = "PAC";
                            break;
                        case "SEDEX":
                            $tipoEnvio = "SEDEX";
                            break;
                        case "ESEDEX":
                            $tipoEnvio = "ESEDEX";
                            break;
                        case "CARTA REGISTRADA":
                            $tipoEnvio = "CARTA";
                            break;
                    }

                    $this->db->tabela = "frete_tabela";
                    $consulta = $this->db->consulta("WHERE destino= '" . $destino . "' AND tipo = '" . $tipoEnvio . "' AND '" . $peso . "' BETWEEN pesoMinimo AND pesoMaximo", "", "", "", "", "$campo as valor");

                    if (mysql_num_rows($consulta)) {

                        $preco = mysql_fetch_assoc($consulta);
                        if ($peso > 10) {
                            $preco["valor"] = $preco["valor"] * $peso;
                        }
                        $valorFrete[$tipoEnvio]["valor"] = $preco["valor"];
                    } else {
                        $valorFrete["erro"] = 9;
                        return $valorFrete;
                    }
                } else {
                    $tipoEnvio = array(0 => "PAC", 1 => "SEDEX");
                    if ($configFrete["codigoESEDEX"] && $configFrete["senhaESEDEX"] && $this->checaEsedex()) {
                        $tipoEnvio[] = "ESEDEX";
                    }

                    foreach ($tipoEnvio as $envio) {
                        $this->db->tabela = "frete_tabela";
                        $consulta = $this->db->consulta("WHERE destino= '" . $destino . "' AND tipo = '" . $envio . "' AND '" . $peso . "' BETWEEN pesoMinimo AND pesoMaximo", "", "", "", "", "$campo as valor");

                        if (mysql_num_rows($consulta)) {
                            $preco = mysql_fetch_assoc($consulta);
                            if ($peso > 10 && ($envio == "PAC" || $envio == "SEDEX")) {
                                $preco["valor"] = $preco["valor"] * $peso;
                            } elseif ($peso > 15 && $envio == "ESEDEX") {
                                $preco["valor"] = $preco["valor"] * $peso;
                            }
                            $valorFrete[$envio]["valor"] = $preco["valor"];
                        } else {
                            $valorFrete["erro"] = 9;
                            return $valorFrete;
                        }
                    }
                }
                return $valorFrete;
            }
        } else {

            $valorFrete["erro"] = 1;
            return $valorFrete;
        }
    }

    public function checaRegiao() {

        $this->db->tabela = "frete_municipio";
        $consulta = $this->db->consulta("WHERE '" . $this->cep . "' BETWEEN cepMinimo AND cepMaximo");
        if (mysql_num_rows($consulta)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function checaDivisa() {

        $this->db->tabela = "frete_divisa";
        $consulta = $this->db->consulta("WHERE '" . $this->cep . "' BETWEEN cepMinimo AND cepMaximo");
        if (mysql_num_rows($consulta)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function checaEsedex() {

        $this->db->tabela = "frete_local_esedex";
        $consulta = $this->db->consulta("WHERE '" . $this->cep . "' BETWEEN cepMinimo AND cepMaximo");
        if (mysql_num_rows($consulta)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function checaCapital() {

        $this->db->tabela = "config";
        $empresa = $this->db->consultaId(1);

        $objFrete = $this->controller->loadModel("frete/model-frete");

        $this->db->tabela = "estado";
        $consulta = $this->db->consulta("WHERE capital = '" . $empresa["cidade"] . "'");

        if (!mysql_num_rows($consulta)) {
            return "INTERIOR";
        } else {
            $objFrete->cep = $this->cep;
            $enderecoCliente = $objFrete->buscarEndereco();

            $this->db->tabela = "estado";
            $consulta = $this->db->consulta("WHERE capital = '" . $enderecoCliente["cidade"] . "'");

            if (!mysql_num_rows($consulta)) {
                return "INTERIOR";
            } else {
                return "CAPITAL";
            }
        }
    }

}

?>