<?

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerFrete extends MainController {

    /**
     * Carrega a página "/view/home/index.php"
     */
    public function calcularFrete() {

        require_once ABSPATH . '/enum.php';

        $modelo = $this->loadModel("frete/model-frete");

        $this->db->tabela = "config";
        $modelo->enderecoLoja = $this->db->consultaId(1);
        $modelo->cep = $_POST["cep"];
        $modelo->idCombinacao = $_POST["idCombinacao"];
        $modelo->telaFinalizacao = FALSE;
        $modelo->idPedido = "";
        $modelo->tipoEnvio = "";

        $this->db->tabela = "produto_combinacao";
        $produtoCombinacao = $this->db->consultaId($modelo->idCombinacao);

        $this->db->tabela = "produto";
        $produto = $this->db->consultaId($produtoCombinacao["produtoFK"]);

        $this->db->tabela = "frete_regiao_entrega";
        $consultaRegiaoEntrega = $this->db->consulta("WHERE '" . $_POST["cep"] . "' BETWEEN cepMinimo AND cepMaximo");

        if ($produto["freteTransportadora"] == "N") {
            $valorFrete = $modelo->calcularFrete();
            if (is_array($valorFrete)) {
                if (!isset($valorFrete["erro"])) {
                    ?>
                    <table class="table table-hover tabela-frete">
                        <thead>
                            <tr>
                                <th>
                                    Entrega
                                </th>
                                <th>
                                    Valor
                                </th>
                                <th>
                                    Prazo
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            foreach ($valorFrete as $ind => $frete) {
                                ?>
                                <tr>
                                    <td>
                                        <span><?= $ind ?></span>
                                    </td>
                                    <td>
                                        <?= ($ind == "PAC" && ($produto["freteGratis"] == "S" || !is_numeric($frete["valor"]))) ? "<span class=\"color-red\">GR&Aacute;TIS</span>" : "<span>R$ " . number_format($frete["valor"], 2, ",", ".") . "</span>" ?>
                                    </td>
                                    <td>
                                        <span><?= $frete["prazo"] ?> Dias &uacute;teis</span>
                                    </td>
                                </tr>
                                <?
                            }
                            if (mysql_num_rows($consultaRegiaoEntrega)) {
                                $regiaoEntrega = mysql_fetch_assoc($consultaRegiaoEntrega);
                                ?>
                                <tr>
                                    <td>
                                        <span>Entrega Pr&oacute;pria</span>
                                    </td>
                                    <td>
                                        <?= ($regiaoEntrega['valor'] <= 0) ? "<span class=\"color-red\">GR&Aacute;TIS</span>" : "<span>R$ " . number_format($regiaoEntrega['valor'], 2, ",", ".") . "</span>" ?>
                                    </td>
                                    <td>
                                        <span><?= $regiaoEntrega["prazo"] ?> Dias &uacute;teis</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>Retirada na Loja</span>
                                    </td>
                                    <td>
                                        <span class="color-red">GR&Aacute;TIS</span>
                                    </td>
                                    <td></td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>
                    <?
                } else {
                    ?>
                    <div class="col-md-12 pull-left alert alert-danger" role="alert">
                        <?= utf8_encode($modelo->freteErro($valorFrete["erro"])) ?>
                    </div>
                    <?
                }
            }
        } else {
            $modeloTransportadora = $this->loadModel("frete/model-transportadora");
            $modeloTransportadora->cep = $_POST["cep"];
            $modeloTransportadora->peso = $produtoCombinacao['peso'];
            $modeloTransportadora->valor = $produtoCombinacao["valorPor"];
            $modeloTransportadora->encontraPeso(array(
                'altura' => $produtoCombinacao['altura'],
                'largura' => $produtoCombinacao['largura'],
                'profundidade' => $produtoCombinacao['profundidade']
            ));
            $modeloTransportadora->montaCalculo();
            if (!$modeloTransportadora->erro) {
                ?>
                <table class="table table-hover tabela-frete">
                    <thead>
                        <tr>
                            <th>
                                Entrega
                            </th>
                            <th>
                                Valor
                            </th>
                            <th>
                                Prazo
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span>Transportadora</span>
                            </td>
                            <td class="nowrap">
                                R$ <?= number_format($modeloTransportadora->total, 2, ',', '.') ?>
                            </td>
                            <td class="nowrap">
                                <span><?= $modeloTransportadora->prazo ?> Dias &uacute;teis</span>
                            </td>
                        </tr>
                        <?
                        if (mysql_num_rows($consultaRegiaoEntrega)) {
                            $regiaoEntrega = mysql_fetch_assoc($consultaRegiaoEntrega);
                            ?>
                            <tr>
                                <td>
                                    <span>Entrega Pr&oacute;pria</span>
                                </td>
                                <td>
                                    <?= ($regiaoEntrega['valor'] <= 0) ? "<span class=\"color-red\">GR&Aacute;TIS</span>" : "<span>R$ " . number_format($regiaoEntrega['valor'], 2, ",", ".") . "</span>" ?>
                                </td>
                                <td>
                                    <span><?= $regiaoEntrega["prazo"] ?> Dias &uacute;teis</span>
                                </td>
                            </tr>
                        <? } ?>
                    </tbody>
                </table>
            <? } else { ?>
                <div class="col-md-12 pull-left alert alert-danger" role="alert">
                    <?= utf8_encode($modelo->freteErro(1)) ?>
                </div>
            <? } ?>
            <? if (chkArray($_SESSION['CLIENTE'], 'email') == "desenvolvimento6@byteabyte.com.br") { ?>
                <table class="table table-hover tabela-frete">
                    <thead>
                        <tr>
                            <th>
                                Taxa
                            </th>
                            <th>
                                Valor Taxa
                            </th>
                            <th>
                                Valor calculado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Peso utilizado</td>
                            <td></td>
                            <td><?= number_format($modeloTransportadora->peso, 2, ',', '.') ?>Kg</td>
                        </tr>
                        <tr>
                            <td>Frete peso</td>
                            <td></td>
                            <td>R$ <?= number_format($modeloTransportadora->frete_peso, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Despacho</td>
                            <td>R$ <?= number_format($modeloTransportadora->taxas['tx_despacho'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_despacho, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Valor Fluvial</td>
                            <td><?= number_format($modeloTransportadora->taxas['tx_fluvial'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_fluvial, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Frete Valor</td>
                            <td><?= number_format($modeloTransportadora->tabela['tx_fv'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_fv, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>GRIS</td>
                            <td><?= number_format($modeloTransportadora->taxas['tx_gris'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_gris, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Ped&aacute;gio</td>
                            <td>R$ <?= number_format($modeloTransportadora->taxas['tx_pedagio'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_pedagio, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Taxa Portuaria</td>
                            <td>R$ <?= number_format($modeloTransportadora->taxas['tx_portuaria'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_portuaria, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Seguro Fluvial</td>
                            <td><?= number_format($modeloTransportadora->taxas['tx_seguro_aquaviario'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_seguro_aquaviario, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>SUFRAMA</td>
                            <td>R$ <?= number_format($modeloTransportadora->taxas['tx_suframa'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_suframa, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>TAS</td>
                            <td>R$ <?= number_format($modeloTransportadora->taxas['tx_tas'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_tas, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>TRT1</td>
                            <td><?= number_format($modeloTransportadora->taxas['tx_trt1'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_trt1, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>TRT2</td>
                            <td><?= number_format($modeloTransportadora->taxas['tx_trt2'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_trt2, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>ICMS</td>
                            <td><?= number_format($modeloTransportadora->taxas['tx_icms'], 2, ',', '.') ?>%</td>
                            <td>R$ <?= number_format($modeloTransportadora->tx_icms, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
                <?
            }
        }
    }

    public function calcularFreteCarrinho() {

        require_once ABSPATH . '/enum.php';

        $modelo = $this->loadModel("frete/model-frete");

        $this->db->tabela = "config";
        $modelo->enderecoLoja = $this->db->consultaId(1);
        $modelo->cep = $_POST["cep"];
        $modelo->telaFinalizacao = FALSE;
        ?>
        <td colspan="1"></td>
        <td colspan="3" class="roboto normal size-1-4 color-dark-gray">
            <i class="fa fa-exclamation-triangle"></i> Aten&ccedil;&atilde;o! Seu pedido ser&aacute; processado a partir da aprova&ccedil;&atilde;o do pagamento 
            de acordo com as pol&iacute;ticas de prazo de entrega do website.
        </td>
        <td colspan="3" class="faixa-frete-carrinho">
            <?
            if (strlen($modelo->cep) == 9) {
                if (chkArray($_SESSION, "PEDIDO")) {
                    if (chkArray($_SESSION["PEDIDO"], "CARRINHO")) {
                        $freteTransportadora = FALSE;
                        foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                            $this->db->tabela = "produto";
                            $produto = $this->db->consultaId($produtoCarrinho["ID"]);
                            if ($produto["freteTransportadora"] == "S") {
                                $freteTransportadora = TRUE;
                            }
                        }

                        $this->db->tabela = "frete_regiao_entrega";
                        $consultaRegiaoEntrega = $this->db->consulta("WHERE '" . $_POST["cep"] . "' BETWEEN cepMinimo AND cepMaximo");
                        if ($freteTransportadora) {
                            $modeloTransportadora = $this->loadModel("frete/model-transportadora");
                            $modeloCarrinho = $this->loadModel("carrinho/model-carrinho");
                            $modeloTransportadora->cep = $_POST["cep"];
                            $modeloTransportadora->valor = $modeloCarrinho->valorTotalCarrinho(TRUE);
                            $modeloTransportadora->encontraPeso(array(), TRUE);
                            $modeloTransportadora->montaCalculo();

                            if (!$modeloTransportadora->erro) {
                                ?>
                                <div class="row">
                                    <div id="no-more-tables" class="col-md-12">
                                        <table class="table col-md-12 padding-0  table-striped table-hover cf">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="5%"></th>
                                                    <th class="text-center size-1-4">
                                                        Forma de Entrega
                                                    </th>
                                                    <th class="text-center size-1-4">
                                                        Valor
                                                    </th>
                                                    <th class="text-center size-1-4">
                                                        Prazo
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="linhas-resultado-frete">
                                                <tr onclick="$('.botoes-frete').attr('checked', false);
                                                                                    $('#seleciona-TRANSPORTADORA').attr('checked', true);
                                                                                    trocaValorFrete($('#seleciona-TRANSPORTADORA').val(), '<?= $modeloTransportadora->total ?>', '<?= number_format($modeloTransportadora->total, 2, ",", ".") ?>', '#seleciona-TRANSPORTADORA');">
                                                    <td data-title="Selecionar" class="text-center size-1-4">
                                                        <input id="seleciona-TRANSPORTADORA" class="botoes-frete" type="radio" name="freteSelecionado" onclick="trocaValorFrete($(this).val(), '<?= $modeloTransportadora->total ?>', '<?= number_format($modeloTransportadora->total, 2, ",", ".") ?>', '#seleciona-TRANSPORTADORA')" value="TRANSPORTADORA"/>
                                                    </td>
                                                    <td data-title="Forma de Entrega" class="text-center size-1-4">
                                                        Transportadora
                                                    </td>
                                                    <td data-title="Valor" class="text-center size-1-4">
                                                        <? if (is_numeric($modeloTransportadora->total)) { ?>
                                                            R$ <?= number_format($modeloTransportadora->total, 2, ",", ".") ?>
                                                        <? } else { ?>
                                                            GR&Aacute;TIS
                                                        <? } ?>
                                                    </td>
                                                    <td data-title="Prazo" class="text-center size-1-4">
                                                        <?= $modeloTransportadora->prazo ?> Dias &uacute;teis
                                                    </td>
                                                </tr>
                                                <?
                                                if (mysql_num_rows($consultaRegiaoEntrega)) {
                                                    $regiaoEntrega = mysql_fetch_assoc($consultaRegiaoEntrega);
                                                    ?>
                                                    <tr onclick="$('.botoes-frete').attr('checked', false);
                                                                                            $('#seleciona-MOTOBOY').attr('checked', true);
                                                                                            trocaValorFrete($('#seleciona-MOTOBOY').val(), '<?= $regiaoEntrega["valor"] ?>', '<?= number_format($regiaoEntrega["valor"], 2, ",", ".") ?>', '#seleciona-MOTOBOY');">
                                                        <td data-title="Selecionar" class="text-center size-1-4">
                                                            <input id="seleciona-MOTOBOY" class="botoes-frete" type="radio" name="freteSelecionado" onclick="trocaValorFrete($(this).val(), '<?= $regiaoEntrega["valor"] ?>', '<?= number_format($regiaoEntrega["valor"], 2, ",", ".") ?>', '#seleciona-MOTOBOY')" value="MOTOBOY"/>
                                                        </td>
                                                        <td data-title="Forma de Entrega" class="text-center size-1-4">
                                                            Entrega Pr&oacute;pria
                                                        </td>
                                                        <td data-title="Valor" class="text-center size-1-4">
                                                            <? if (is_numeric($regiaoEntrega["valor"])) { ?>
                                                                R$ <?= number_format($regiaoEntrega["valor"], 2, ",", ".") ?>
                                                            <? } else { ?>
                                                                GR&Aacute;TIS
                                                            <? } ?>
                                                        </td>
                                                        <td data-title="Prazo" class="text-center size-1-4">
                                                            <?= $regiaoEntrega["prazo"] ?> Dias &uacute;teis
                                                        </td>
                                                    </tr>
                                                <? } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <? } else { ?>
                                <div id="mensagem-retorno-frete" class="col-md-12 alert alert-warning text-center" role="alert">
                                    <?= utf8_encode($modelo->freteErro(1)) ?>
                                </div>
                                <?
                            }
                        } else {
                            $valorFrete = $modelo->calcularFrete();
                            if (is_array($valorFrete)) {
                                if (!isset($valorFrete["erro"])) {
                                    ?>
                                    <div class="row">
                                        <div id="no-more-tables" class="col-md-12">
                                            <table class="table col-md-12 padding-0  table-striped table-hover cf">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%"></th>
                                                        <th class="text-center size-1-4">
                                                            Forma de Entrega
                                                        </th>
                                                        <th class="text-center size-1-4">
                                                            Valor
                                                        </th>
                                                        <th class="text-center size-1-4">
                                                            Prazo
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="linhas-resultado-frete">
                                                    <? foreach ($valorFrete as $fretes => $valor) { ?>
                                                        <tr onclick="$('.botoes-frete').attr('checked', false);
                                                                                                    $('#seleciona-<?= $fretes ?>').attr('checked', true);
                                                                                                    trocaValorFrete($('#seleciona-<?= $fretes ?>').val(), '<?= $valor["valor"] ?>', '<?= number_format($valor["valor"], 2, ",", ".") ?>', '#seleciona-<?= $fretes ?>');">
                                                            <td data-title="Selecionar" class="text-center size-1-4">
                                                                <input id="seleciona-<?= $fretes ?>" class="botoes-frete" type="radio" name="freteSelecionado" onclick="trocaValorFrete($(this).val(), '<?= $valor["valor"] ?>', '<?= number_format($valor["valor"], 2, ",", ".") ?>', '#seleciona-<?= $fretes ?>')" value="<?= $fretes ?>"/>
                                                            </td>
                                                            <td data-title="Forma de Entrega" class="text-center size-1-4">
                                                                <?= $tipoFrete[$fretes] ?>
                                                            </td>
                                                            <td data-title="Valor" class="text-center size-1-4">
                                                                <? if (is_numeric($valor["valor"])) { ?>
                                                                    R$ <?= number_format($valor["valor"], 2, ",", ".") ?>
                                                                <? } else { ?>
                                                                    GR&Aacute;TIS
                                                                <? } ?>
                                                            </td>
                                                            <td data-title="Prazo" class="text-center size-1-4">
                                                                <?= $valor["prazo"] ?> Dias &uacute;teis
                                                            </td>
                                                        </tr>
                                                    <? } ?>
                                                    <?
                                                    if (mysql_num_rows($consultaRegiaoEntrega)) {
                                                        $regiaoEntrega = mysql_fetch_assoc($consultaRegiaoEntrega);
                                                        ?>
                                                        <tr onclick="$('.botoes-frete').attr('checked', false);
                                                                                                    $('#seleciona-MOTOBOY').attr('checked', true);
                                                                                                    trocaValorFrete($('#seleciona-MOTOBOY').val(), '<?= $regiaoEntrega["valor"] ?>', '<?= number_format($regiaoEntrega["valor"], 2, ",", ".") ?>', '#seleciona-MOTOBOY');">
                                                            <td data-title="Selecionar" class="text-center size-1-4">
                                                                <input id="seleciona-MOTOBOY" class="botoes-frete" type="radio" name="freteSelecionado" onclick="trocaValorFrete($(this).val(), '<?= $regiaoEntrega["valor"] ?>', '<?= number_format($regiaoEntrega["valor"], 2, ",", ".") ?>', '#seleciona-MOTOBOY')" value="MOTOBOY"/>
                                                            </td>
                                                            <td data-title="Forma de Entrega" class="text-center size-1-4">
                                                                Entrega Pr&oacute;pria
                                                            </td>
                                                            <td data-title="Valor" class="text-center size-1-4">
                                                                <? if (is_numeric($regiaoEntrega["valor"])) { ?>
                                                                    R$ <?= number_format($regiaoEntrega["valor"], 2, ",", ".") ?>
                                                                <? } else { ?>
                                                                    GR&Aacute;TIS
                                                                <? } ?>
                                                            </td>
                                                            <td data-title="Prazo" class="text-center size-1-4">
                                                                <?= $regiaoEntrega["prazo"] ?> Dias &uacute;teis
                                                            </td>
                                                        </tr>
                                                    <? } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?
                                } else {
                                    ?>
                                    <div id="mensagem-retorno-frete" class="col-md-12 alert alert-warning text-center" role="alert">
                                        <?= utf8_encode($modelo->freteErro($valorFrete["erro"])) ?>
                                    </div>
                                    <?
                                }
                            }
                        }
                    } else {
                        ?>
                        <div id="mensagem-retorno-frete" class="col-md-12 pull-left alert alert-warning text-center margin-0" role="alert">
                            Adicione produtos ao seu carrinho para realizar o c&aacute;lculo do frete
                        </div>
                        <?
                    }
                } else {
                    ?>
                    <div id="mensagem-retorno-frete" class="col-md-12 pull-left alert alert-warning text-center margin-0" role="alert">
                        Adicione produtos ao seu carrinho para realizar o c&aacute;lculo do frete
                    </div>
                <? } ?>
            <? } else { ?>
                <div id="mensagem-retorno-frete" class="col-md-12 pull-left alert alert-warning text-center margin-0" role="alert">
                    Informe o CEP para que seja feito o c&aacute;lculo do frete.
                </div>
            <? } ?>
        </td>
        <?
    }

    public function buscarEndereco() {

        $cep = str_replace("-", "", $this->parametros[0]);
        $modelo = $this->loadModel("frete/model-frete");
        $modelo->cep = $cep;
        $endereco = $modelo->buscarEndereco();
        $endereco["cidade"] = utf8_decode($endereco["cidade"]);
        $endereco["logradouro"] = utf8_decode($endereco["logradouro"]);
        $endereco["bairro"] = utf8_decode($endereco["bairro"]);
        echo json_encode($endereco);
    }

}
