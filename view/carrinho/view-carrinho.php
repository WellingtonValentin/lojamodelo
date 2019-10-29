<section id="pagina-interna" class="pagina-carrinho">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
        </div>
        <? if (!chkArray(chkArray($_SESSION, "PEDIDO"), "CARRINHO")) { ?>
            <div class="alert alert-danger text-center size-1-4" role="alert">
                Seu carrinho se encontra vazio no momento, adicione produtos ao seu carrinho para poder dar sequência a sua compra.
            </div>
        <? } else { ?>
            <div class="row">
                <div class="col-xs-12">
                    <h1 class="size-2-2 normal roboto margin-0"><?= $this->title ?></h1>
                    <div class="row">
                        <div id="no-more-tables" class="col-xs-12">
                            <table class="table col-xs-12 padding-0  table-striped cf">
                                <thead>
                                    <tr>
                                        <th class="text-center size-1-4" width="5%">
                                            <strong>Código</strong>
                                        </th>
                                        <th colspan="2" class="size-1-4" width="45%">
                                            <strong>Produto(s)</strong>
                                        </th>
                                        <th class="numeric text-center size-1-4" width="15%">
                                            <strong>Quantidade</strong>
                                        </th>
                                        <th class="numeric text-center size-1-4" width="15%">
                                            <strong>Preço Unitário</strong>
                                        </th>
                                        <th class="numeric text-center size-1-4 hidden-xs" width="15%">
                                            <strong>Valor Total</strong>
                                        </th>
                                        <th class="text-center size-1-4" width="5%">
                                            <strong>Excluir</strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    $subTotal = 0;
                                    foreach ($_SESSION["PEDIDO"]["CARRINHO"] as $ind => $produtoCarrinho) {
                                        $produto = $modeloProduto->detalharProduto($produtoCarrinho["ID"]);
                                        $imagem = $modeloProduto->imagemPrincipal($produtoCarrinho["ID"]);
                                        $modelo->db->tabela = "produto_combinacao";
                                        $combinacao = $modelo->db->consultaId($produtoCarrinho["COMBINACAO"]);
                                        if (isset($_SESSION["CLIENTE"]) && isset($combinacao["valorAtacado"])) {
                                            if (chkArray($_SESSION["CLIENTE"], "tipo") == "JURIDICA") {
                                                $combinacao["valorPor"] = $combinacao["valorAtacado"];
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td data-title="Código" class="text-center roboto size-1-4 color-padrao">
                                                <strong><?= $produtoCarrinho["ID"] ?></strong>
                                            </td>
                                            <td width="15%" data-title="" class="roboto text-center">
                                                <a href="<?= HOME_URL ?>/produto/detalhes/<?= $produtoCarrinho["ID"] ?>/<?= arrumaString($produto["produto"]["titulo"]) ?>.html" class="color-cinza">
                                                    <img class="hidden-xs img-thumbnail" src="<?= $imagem ?>" alt="<?= arrumaString($produto["produto"]["titulo"]) ?>" title="<?= arrumaString($produto["produto"]["titulo"]) ?>"/>
                                                </a>
                                            </td>
                                            <td data-title="Produto(s)" class="text-left">
                                                <a href="<?= HOME_URL ?>/produto/detalhes/<?= $produtoCarrinho["ID"] ?>/<?= arrumaString($produto["produto"]["titulo"]) ?>.html" class="roboto normal size-1-4 color-dark-gray">
                                                    <?= $produto["produto"]["titulo"] ?>
                                                </a>
                                                <?
                                                $modelo->db->tabela = "produto_combinacao_valor";
                                                $consulta = $modelo->db->consulta("WHERE combinacaoFK = '" . $produtoCarrinho["COMBINACAO"] . "'");
                                                if (mysql_num_rows($consulta)) {
                                                    ?>
                                                    <p class="roboto normal color-dark-gray size-1 margin-0">
                                                        <?
                                                        while ($combinacaoValor = mysql_fetch_assoc($consulta)) {
                                                            $modelo->db->tabela = "variacao_valor";
                                                            $variacaoValor = $modelo->db->consultaId($combinacaoValor["variacaoFK"]);
                                                            $modelo->db->tabela = "variacao";
                                                            $variacao = $modelo->db->consultaId($variacaoValor["variacaoFK"]);
                                                            ?>
                                                            <br/><strong><?= $variacao["titulo"] ?>: </strong><?= $variacaoValor["titulo"] ?>
                                                        <? } ?>
                                                    </p>
                                                <? } ?>
                                                <? if (chkArray($combinacao, 'valorPresente')) { ?>
                                                    <br/><br/><span class="roboto normal size-1 color-dark-gray cursor-pointer" data-toggle="modal" data-target="#modal-presente-<?= $ind ?>"><i class="glyphicon glyphicon-gift <?= (isset($produtoCarrinho['VLR_PRESENTE'])) ? "color-light-blue" : "" ?>"></i> Embrulhar para presente?</span>
                                                <? } ?>
                                                <div class="modal fade bs-example-modal-sm" id="modal-presente-<?= $ind ?>" tabindex="-1" role="dialog" aria-labelledby="embrulharParaPresente">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <div class="modal-body roboto normal size-1-2 color-dark-gray text-justify">
                                                                <? if (chkArray($combinacao, 'valorPresente')) { ?>
                                                                    <strong>Atenção! </strong>para o envio embalado para presente é cobrado uma taxa de R$ <?= number_format($combinacao['valorPresente'], 2, ',', '.') ?>
                                                                    por unidade do produto.<br/>
                                                                <? } ?>
                                                                Caso queira gravar alguma mensagem para ser enviada junto com o presente informe no campo abaixo.
                                                                <br/><br/>
                                                                <textarea class="form-control mensagem-presente" rows="3"></textarea>
                                                                <input type="hidden" id="indice-produto" value="<?= $ind ?>"/>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i>&nbsp;&nbsp;&nbsp;Cancelar</button>
                                                                <button type="button" onclick="adicionarPresente('<?= $ind ?>')" class="btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-ok"></i>&nbsp;&nbsp;&nbsp;Confirmar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-title="Quantidade" class="text-center roboto size-1-4">
                                                <div id="quadro-quantidade-<?= $ind ?>" class="input-group">
                                                    <input type="text" id="quantidade<?= $ind ?>" class="form-control num6" name="quantidade" value="<?= $produtoCarrinho["QTD"] ?>">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="button" onclick="mudarQuantidade($('#quantidade<?= $ind ?>').val(), '<?= $produtoCarrinho["COMBINACAO"] ?>', '<?= $ind ?>')">
                                                            <i class="glyphicon glyphicon-refresh"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                                <div class="display-none" id="resposta-quantidade-<?= $ind ?>">
                                                    <div class="alert alert-danger size-0-8" role="alert">
                                                        Quantidade indisponível em estoque!
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-title="Preço Unitário" class="text-center roboto nowrap numeric size-1-4">
                                                <? if (chkArray($produtoCarrinho, 'VLR_PRESENTE')) { ?>
                                                    <span>R$ <?= number_format($combinacao["valorPor"] + $produtoCarrinho['VLR_PRESENTE'], 2, ",", ".") ?></span>
                                                <? } else { ?>
                                                    <span>R$ <?= number_format($combinacao["valorPor"], 2, ",", ".") ?></span>
                                                <? } ?>
                                            </td>
                                            <td data-title="Valor Total" class="text-center roboto nowrap numeric size-1-4 hidden-xs">
                                                <? if (chkArray($produtoCarrinho, 'VLR_PRESENTE')) { ?>
                                                    <span>R$ <?= number_format(($combinacao["valorPor"] + $produtoCarrinho['VLR_PRESENTE']) * $produtoCarrinho["QTD"], 2, ",", ".") ?></span>
                                                <? } else { ?>
                                                    <span>R$ <?= number_format($combinacao["valorPor"] * $produtoCarrinho["QTD"], 2, ",", ".") ?></span>
                                                <? } ?>
                                            </td>
                                            <td data-title="Excluir" class="text-center">
                                                <a href="<?= HOME_URL ?>/carrinho/meus-produtos/<?= $ind ?>/apagar-produto.html#conteudo" class="color-dark-gray roboto numeric size-1-4">
                                                    <span><i class="fa fa-trash"></i></span>
                                                </a>
                                            </td>
                                        </tr>
                                        <?
                                        $subTotal += $combinacao["valorPor"] * $produtoCarrinho["QTD"];
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="" colspan="4" class="text-left">
                                            <span class="roboto normal size-1 color-dark-gray">Cupom de desconto informe-o na etapa de Pagamento</span>
                                        </td>
                                        <td data-title="Total:" colspan="3" class="text-right">
                                            <span class="roboto size-2-2 color-dark-gray"><strong class="hidden-xs">Total do Carrinho: </strong>R$ <?= number_format($modelo->valorTotalCarrinho(), 2, ",", ".") ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-title="Frete" colspan="7" class="faixa-valores-carrinho margin-0">
                                            <div class="row">
                                                <div class="col-xs-12 text-right">
                                                    <div class="col-xs-12 col-sm-8 col-md-10">
                                                        <br/>
                                                        <span class="roboto normal size-1 color-dark-gray"><i class="fa fa-truck"></i> Consulte o prazo de entrega e o frete para seu CEP.</span>
                                                    </div>
                                                    <div class="input-group grupo-frete col-md-2 col-sm-4 col-xs-12 pull-right">
                                                        <input type="text" class="form-control cep" id="buscaCep" placeholder="CEP">
                                                        <div class="input-group-btn">
                                                            <button class="btn btn-default" type="button" onclick="
                                                                        calcularFrete($('#buscaCep').val(), '', '', 'CARRINHO');
                                                                    ">
                                                                <i class="glyphicon glyphicon-search"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!--<td id="area-valor-frete" colspan="2" valign="middle" class="hidden-xs faixa-valores-carrinho size-1-4 text-right"></td>-->
                                    </tr>
                                    <tr id="resultado-frete" class="border-none"></tr>
                                    <tr> 
                                        <td data-title="Total Final:" colspan="7" id="valor-total-pedido" class="faixa-valores-carrinho text-right">
                                            <span class="roboto size-2-2 color-padrao"><strong class="hidden-xs">Total: </strong>R$ <?= number_format($modelo->valorTotalCarrinho(TRUE), 2, ",", ".") ?></span>
                                            <?
                                            $parcelamento = $modeloProduto->parcelaSemJuros();
                                            if (isset($parcelamento["0"]["parcelaSemJuros"])) {
                                                ?>
                                                <br/>
                                                <span class="roboto normal size-1 color-dark-gray">Em até em <?= $parcelamento["0"]["parcelaSemJuros"] ?>x sem juros de R$ <?= number_format($modelo->valorTotalCarrinho(TRUE) / $parcelamento["0"]["parcelaSemJuros"], 2, ",", ".") ?></span>
                                            <? } ?>
                                            <? if ($configValores['descontoBoleto']) { ?>
                                                <br/>
                                                <span class="roboto normal size-1 color-dark-gray">Ou no boleto com <?= $configValores['descontoBoleto'] ?>% de desconto R$ <?= number_format($modelo->valorTotalCarrinho(TRUE) - (($modelo->valorTotalCarrinho(TRUE) * $configValores['descontoBoleto']) / 100), 2, ",", ".") ?></span>
                                            <? } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br/><br/>
                    <div class="row">
                        <div class="col-md-3 col-xs-12 text-left botao-carrinho">
                            <a href="<?= HOME_URL ?>" class="btn btn-block btn-primary btn-warning"><span class="glyphicon glyphicon-chevron-left"></span> CONTINUAR COMPRANDO</a>
                        </div>
                        <div class="col-md-3 col-md-offset-6 col-xs-12 text-right botao-carrinho">
                            <a href="<?= HOME_URL ?>/carrinho/finalizar/finalizar-compra.html" class="btn btn-block btn-primary btn-success">FINALIZAR COMPRA <span class="glyphicon glyphicon-chevron-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</section>