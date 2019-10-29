<section id="pagina-interna" class="compra-realizada">
    <div class="container">
        <br/>
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills"> 
                    <li role="presentation" class="active size-1-8 text-center">
                        <a href="<?= HOME_URL ?>" class="btn background-padrao color-contraste"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;&nbsp;&nbsp;Novo pedido</a>
                    </li>
                    <li role="presentation" class="active size-1-8 text-center">
                        <a href="<?= HOME_URL ?>/cliente/pedido/meus-pedidos.html#conteudo" class="btn background-padrao color-contraste"><span class="glyphicon glyphicon-credit-card"></span>&nbsp;&nbsp;&nbsp;Meus pedidos</a>
                    </li>
                    <li role="presentation" class="active size-1-8 text-center">
                        <a href="<?= HOME_URL ?>/cliente/dados/meus-dados.html#conteudo" class="btn background-padrao color-contraste"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;&nbsp;Meus dados</a>
                    </li>
                </ul>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-12">
                <h1 class="size-2-2 normal roboto"><?= $this->title ?></h1>
                <div class="row">
                    <div class="col-md-3">
                        <p class="text-center quadro-numero-pedido-retorno background-padrao color-contraste size-1-4 roboto">
                            <b>Número de Pedido:</b><br/>
                            <strong class="size-1-4"><?= $numeroPedido ?></strong><br/><br/>
                            A confirmação do pedido foi enviada ao seu endereço de e-mail.
                        </p>
                    </div>
                    <div class="col-md-8 col-md-offset-1 roboto normal size-1-4 color-gray">
                        <? if (chkArray($pedidoPagamento, "formaPagamento")) { ?>
                            <strong>Forma de Pagamento:</strong> <img src="<?= HOME_URL ?>/view/_image/forma_pagamento/<?= strtolower($pedidoPagamento["formaPagamento"]) ?>.png" height="25"/><br/><br/>
                            <? if ($pedidoPagamento["formaPagamento"] == "BOLETO") { ?>
                                <button type="button" class="btn btn-success" onclick="window.open('<?= HOME_URL ?>/pedido/gerarBoleto/<?= cr($idPedido) ?>/gerar-boleto.html#conteudo', '_blank');">
                                    <i class="glyphicon glyphicon-barcode"></i> GERAR BOLETO
                                </button><br/><br/>
                            <? } ?>
                        <? } ?>
                        <strong>Endereço de Entrega:</strong><br/>
                        Destinatário: <?= chkArray($pedido, "pedidoDestinatario") ?><br/>
                        <?= chkArray($pedido, "pedidoEndereco") ?>, N° <?= chkArray($pedido, "pedidoNumero") ?>, <?= chkArray($pedido, "pedidoComplemento") ?>, <?= chkArray($pedido, "pedidoBairro") ?><br/>
                        <?= chkArray($pedido, "pedidoCidade") ?> - <?= chkArray($pedido, "pedidoEstado") ?>, <?= chkArray($pedido, "pedidoCEP") ?><br/>
                    </div>
                </div>
                <br/><br/>
                <div class="row">
                    <div id="no-more-tables" class="col-md-12">
                        <table class="table col-md-12 padding-0  table-striped table-hover cf">
                            <thead>
                                <tr>
                                    <th class="text-center size-1-4" width="5%">
                                        <strong>Código</strong>
                                    </th>
                                    <th class="size-1-4" colspan="2" width="45%">
                                        <strong>Produto(s) no Meu Carrinho</strong>
                                    </th>
                                    <th class="text-center size-1-4" width="15%">
                                        <strong>Quantidade</strong>
                                    </th>
                                    <th class="text-center size-1-4" width="15%">
                                        <strong>Preço Unitário</strong>
                                    </th>
                                    <th class="text-center size-1-4" width="15%">
                                        <strong>Valor Total</strong>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                foreach ($pedido["produtoPedido"] as $ind => $produtoPedido) {
                                    $modelo->db->tabela = "produto_combinacao";
                                    $combinacao = $modelo->db->consultaId($produtoPedido["combinacaoFK"]);
                                    $produto = $modeloProduto->detalharProduto($combinacao["produtoFK"]);
                                    $imagem = $modeloProduto->imagemPrincipal($combinacao["produtoFK"]);
                                    ?>
                                    <tr>
                                        <td data-title="Código" class="text-center roboto size-1-4 color-padrao">
                                            <strong><?= $combinacao["produtoFK"] ?></strong>
                                        </td>
                                        <td class="roboto">
                                            <a href="<?= HOME_URL ?>/produto/detalhes/<?= $combinacao["produtoFK"] ?>/<?= arrumaString($produto["produto"]["titulo"]) ?>.html" class="color-gray">
                                                <img src="<?= $imagem ?>" alt="<?= arrumaString($produto["produto"]["titulo"]) ?>" title="<?= arrumaString($produto["produto"]["titulo"]) ?>" width="100" class="img-thumbnail">
                                            </a>
                                        </td>
                                        <td data-title="Produto(s)" class="roboto text-left size-1-4">
                                            <a href="<?= HOME_URL ?>/produto/detalhes/<?= $combinacao["produtoFK"] ?>/<?= arrumaString($produto["produto"]["titulo"]) ?>.html" class="color-gray">
                                                <?= $produto["produto"]["titulo"] ?>
                                            </a>
                                            <?
                                            $modelo->db->tabela = "produto_combinacao_valor";
                                            $consulta = $modelo->db->consulta("WHERE combinacaoFK = '" . $produtoPedido["combinacaoFK"] . "'");
                                            while ($combinacaoValor = mysql_fetch_assoc($consulta)) {
                                                $modelo->db->tabela = "variacao_valor";
                                                $variacaoValor = $modelo->db->consultaId($combinacaoValor["variacaoFK"]);
                                                $modelo->db->tabela = "variacao";
                                                $variacao = $modelo->db->consultaId($variacaoValor["variacaoFK"]);
                                                ?>
                                                <br/><span class="size-1"><strong><?= $variacao["titulo"] ?>: </strong><?= $variacaoValor["titulo"] ?></span>
                                                <?
                                            }
                                            ?>
                                        </td>
                                        <td data-title="Quantidade" class="text-center roboto size-1-4">
                                            <span><?= $produtoPedido["quantidade"] ?></span>
                                        </td>
                                        <td data-title="Preço Unitário" class="text-center roboto size-1-4">
                                            <span>R$ <?= number_format($produtoPedido["valor"], 2, ",", ".") ?></span>
                                        </td>
                                        <td data-title="Valor Total" class="text-center roboto hidden-xs size-1-4">
                                            <span>R$ <?= number_format($produtoPedido["valor"] * $produtoPedido["quantidade"], 2, ",", ".") ?></span>
                                        </td>
                                    </tr>
                                <? } ?>
                                <tr>
                                    <td data-title="Frete" colspan="6" valign="middle" class="text-right size-1-4">
                                        <? if ($pedido["tipoFrete"] == "FRETE A CALCULAR") { ?>
                                            <strong class="hidden-xs">Frete: </strong>A calcular
                                        <? } else { ?>
                                            <strong class="hidden-xs">Frete: </strong><?= $pedido["valorFrete"] ?>
                                        <? } ?>
                                    </td>
                                </tr>
                                <? if (isset($pedido["valorDesconto"])) { ?>
                                    <tr>
                                        <td data-title="Desconto" colspan="6" valign="middle" class="text-right size-1-4">
                                            <strong class="hidden-xs">Desconto: </strong><?= $pedido["valorDesconto"] ?>
                                        </td>
                                    </tr>
                                <? } ?>
                                <tr>
                                    <td data-title="Total" colspan="6" class="text-right size-1-8 color-red">
                                        <span><strong class="hidden-xs">Total: </strong><?= $pedido["valorFinal"] ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-title="Forma de Entrega" colspan="6" valign="middle" class="text-right size-1-4">
                                        <strong class="hidden-xs">Forma de Entrega: </strong><?= ($pedido["tipoFrete"] == "MOTOBOY") ? "Entrega Própria" : $pedido["tipoFrete"] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="alert alert-danger hidden-xs size-1-4">
                                        <i class="fa fa-exclamation-triangle"></i> <strong>Atenção! </strong>O prazo começa a contar a partir da aprovação do pedido. O prazo apresentado pode estar sujeito a alterações sendo ele confirmado no ato da postagem de seu pedido.
                                    </td>
                                    <td data-title="Prazo de Entrega" colspan="2" class="text-right size-1-4">
                                        <strong class="hidden-xs">Prazo de Entrega: </strong><?= $pedido["prazoEstimado"] ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>