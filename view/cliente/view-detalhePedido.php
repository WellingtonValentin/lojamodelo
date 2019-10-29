<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3">
                <? require_once ABSPATH . '/view/cliente/view-menu.php'; ?>
            </div>
            <div class="col-md-9">
                <h2>Pedido</h2>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h3 class="size-1-6">DETALHES DO SEU PEDIDO</h3> <br/>
                        <p class="size-1-2">Número do seu pedido</p>
                        <span class="size-1-4"><?= str_pad($pedido["id"], 10, "0", STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="col-md-4 text-center">
                        <h3 class="size-1-6">DADOS DO CLIENTE</h3> <br/>
                        <p class="size-1-2">
                            <?= $pedido["clienteEndereco"] ?>, <?= $pedido["clienteNumero"] ?><br/>
                            <?= $pedido["clienteComplemento"] ?> - <?= $pedido["clienteBairro"] ?><br/>
                            CEP: <?= $pedido["clienteCEP"] ?> - <?= $pedido["clienteCidade"] ?> - <?= $pedido["clienteEstado"] ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h3 class="size-1-6">ENDEREÇO DE ENTREGA</h3> <br/>
                        <p class="size-1-2">
                            <?= $pedido["pedidoEndereco"] ?>, <?= $pedido["pedidoNumero"] ?><br/>
                            <?= $pedido["pedidoComplemento"] ?> - <?= $pedido["pedidoBairro"] ?><br/>
                            CEP: <?= $pedido["pedidoCEP"] ?> - <?= $pedido["pedidoCidade"] ?> - <?= $pedido["pedidoEstado"] ?>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-2 col-md-offset-1 text-center <?= ($pedido["valorStatus"] >= 20) ? "status-pedidos-sucesso" : "" ?>">
                        <i class="fa fa-shopping-cart size-3"></i>
                        <h3 class="size-1-6">PEDIDO REALIZADO</h3>
                    </div>
                    <div class="col-md-2 text-center <?= ($pedido["valorStatus"] >= 40) ? "status-pedidos-sucesso" : "" ?>">
                        <i class="fa fa-credit-card size-3"></i>
                        <h3 class="size-1-6">PAGAMENTO CONFIRMADO</h3>
                    </div>
                    <div class="col-md-2 text-center <?= ($pedido["valorStatus"] >= 60) ? "status-pedidos-sucesso" : "" ?>">
                        <i class="fa fa-archive size-3"></i>
                        <h3 class="size-1-6">PREPARADO PARA ENVIO</h3>
                    </div>
                    <div class="col-md-2 text-center <?= ($pedido["valorStatus"] >= 80) ? "status-pedidos-sucesso" : "" ?>">
                        <i class="fa fa-truck size-3"></i>
                        <h3 class="size-1-6">DESPACHADO</h3>
                    </div>
                    <div class="col-md-2 text-center <?= ($pedido["valorStatus"] == 100) ? "status-pedidos-sucesso" : "" ?>">
                        <i class="fa fa-check-square-o size-3"></i>
                        <h3 class="size-1-6">ENTREGA EFETUADA</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 hidden-sm hidden-xs">
                        <div class="progress">
                            <div class="progress-bar active" role="progressbar" aria-valuenow="<?= $pedido["valorStatus"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $pedido["valorStatus"] ?>%"></div>
                        </div>
                    </div>
                </div>
                <br/>
                <br/>
                <br/>
                <h2>Produtos</h2>
                <div id="no-more-tables">
                    <table class="table col-md-12 padding-0  table-striped table-hover cf">
                        <thead>
                            <tr>
                                <th class="text-center size-1-4">
                                    <strong>Código</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Foto</strong>
                                </th>
                                <th class="text-left size-1-4">
                                    <strong>Título</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Quantidade</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Valor Unit.</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Total</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            foreach ((array) $pedido["produtoPedido"] as $produtoPedido) {
                                $this->db->tabela = "produto_combinacao";
                                $combinacao = $this->db->consultaId($produtoPedido["combinacaoFK"]);

                                $this->db->tabela = "produto_combinacao_valor";
                                $consultaCombinacaoValor = $this->db->consulta("WHERE combinacaoFK = '" . $produtoPedido["combinacaoFK"] . "'");

                                $this->db->tabela = "produto";
                                $produto = $this->db->consultaId($combinacao["produtoFK"]);

                                $textoCombinacao = "";
                                if (mysql_num_rows($consultaCombinacaoValor)) {
                                    while ($combinacaoValor = mysql_fetch_assoc($consultaCombinacaoValor)) {

                                        $this->db->tabela = "variacao_valor";
                                        $variacaoValor = $this->db->consultaId($combinacaoValor["variacaoFK"]);

                                        $this->db->tabela = "variacao";
                                        $variacao = $this->db->consultaId($variacaoValor["variacaoFK"]);

                                        $textoCombinacao = "<b>" . $variacao["titulo"] . ": </b>" . $variacaoValor["titulo"] . "<br/>";
                                    }
                                    $textoCombinacao = "<br/>" . $textoCombinacao;
                                }

                                $modeloProduto = $this->loadModel("produto/model-produto");
                                $imagem = $modeloProduto->imagemPrincipal($produto["id"]);
                                ?>
                                <tr>
                                    <td  data-title="Código" valign="middle" class="text-center size-1-4">
                                        <?= $combinacao["produtoFK"] ?>
                                    </td>
                                    <td data-title="Foto" class="text-center size-1-4">
                                        <img src="<?= $imagem ?>" alt="<?= $produto["titulo"] ?>" title="<?= $produto["titulo"] ?>" width="100" class="img-thumbnail">
                                    </td>
                                    <td data-title="Título" class="text-left size-1-4">
                                        <?= $produto["titulo"] . $textoCombinacao ?>
                                        <? if (chkArray($produtoPedido, 'valorEmbalagem')) { ?>
                                            <br/><br/><span class="roboto normal size-1 color-dark-gray cursor-pointer" data-toggle="modal" data-target="#modal-presente-<?= $produtoPedido['id'] ?>"><i class="glyphicon glyphicon-gift color-light-blue"></i> Embrulhar para presente.</span>
                                            <? if (chkArray($produtoPedido, 'descEmbalagem')) { ?>
                                                <div class="modal fade bs-example-modal-sm" id="modal-presente-<?= $produtoPedido['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="embrulharParaPresente">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <div class="modal-body roboto normal size-1-2 color-dark-gray text-justify">
                                                                <?= $produtoPedido['descEmbalagem'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <? } ?>
                                        <? } ?>
                                    </td>
                                    <td data-title="Quantidade" class="text-center size-1-4">
                                        <?= $produtoPedido["quantidade"] ?>
                                    </td>
                                    <td data-title="Valor Unit" class="text-center size-1-4">
                                        <? if (chkArray($produtoPedido, 'valorEmbalagem')) { ?>
                                            R$ <?= number_format($produtoPedido["valor"] + $produtoPedido['valorEmbalagem'], 2, ",", ".") ?>
                                        <? } else { ?>
                                            R$ <?= number_format($produtoPedido["valor"], 2, ",", ".") ?>
                                        <? } ?>
                                    </td>
                                    <td data-title="Total" class="text-center size-1-4">
                                        <? if (chkArray($produtoPedido, 'valorEmbalagem')) { ?>
                                            R$ <?= number_format(($produtoPedido["quantidade"] + $produtoPedido['valorEmbalagem']) * $produtoPedido["valor"], 2, ",", ".") ?>
                                        <? } else { ?>
                                            R$ <?= number_format($produtoPedido["quantidade"] * $produtoPedido["valor"], 2, ",", ".") ?>
                                        <? } ?>
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table><br/><br/>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center size-1-4">
                                    <strong>Total em Produtos</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Valor do Frete</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Valor do Desconto</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Total</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Tipo de Envio</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Prazo de Entrega</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-title="Total Carrinho" class="text-center size-1-4">
                                    <?= $pedido["valorTotal"] ?>
                                </td>
                                <td data-title="Frete" class="text-center size-1-4">
                                    <?= $pedido["valorFrete"] ?>
                                </td>
                                <td data-title="Desconto" class="text-center size-1-4">
                                    <?= chkArray($pedido, "valorDesconto") ?>
                                </td>
                                <td data-title="Total" class="text-center size-1-4">
                                    <?= $pedido["valorFinal"] ?>
                                </td>
                                <td data-title="Envio" class="text-center size-1-4">
                                    <?= ($pedido["tipoFrete"] == "MOTOBOY") ? "Entrega Própria" : $pedido["tipoFrete"] ?>
                                </td>
                                <td data-title="Prazo" class="text-center size-1-4">
                                    <?= $pedido["prazoEstimado"] ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-danger size-1-2" role="alert">
                    <strong><i class="glyphicon glyphicon-warning-sign" ></i> Atenção: </strong>O prazo começa a contar a partir da aprovação do pedido. Pedidos pagos por boleto bancário tem 3 dias úteis acrescidos ao prazo de entrega.
                </div>
                <? if (isset($formasPagamento)) { ?>
                    <br/>
                    <br/>
                    <br/>
                    <h2>Efetuar Pagamento</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <form action="<?= HOME_URL ?>/pedido/enviarPagamento/<?= $this->parametros[0] ?>/enviando-pagamento.html" method="POST">
                                <? foreach ($formasPagamento as $ind => $pagamento) { ?>
                                    <div class="col-md-3 col-sm-3 col-xs-12 quadro-forma-pagamento text-center size-1-4">
                                        <img src="<?= HOME_URL ?>/view/_image/forma_pagamento/<?= strtolower($pagamento["CLASSE"]) ?>-icon.png"/><br/><br/><br/>
                                        <?= $pagamento["TITULO"] ?><br/><br/><br/>
                                        <? if ($pagamento["CLASSE"] == "BOLETO" && $_SESSION['CLIENTE']['email'] == "desenvolvimento6@byteabyte.com.br") { ?>
                                            <button type="button" value="<?= $pagamento["CLASSE"] ?>"  onclick="window.open('<?= HOME_URL ?>/pedido/gerarBoleto/<?= $this->parametros[0] ?>/gerar-boleto.html#conteudo', '_blank');" class="btn btn-success">CONFIRMAR</button>
                                        <? } elseif ($pagamento["CLASSE"] != "BOLETO") { ?>
                                            <button type="submit" value="<?= $pagamento["CLASSE"] ?>" name="formaPagamento" class="btn btn-success"><i class="glyphicon glyphicon-usd"></i>&nbsp;&nbsp;PAGAR</button>
                                        <? } ?>
                                    </div>
                                <? } ?>
                            </form>
                        </div>
                    </div>
                <? } ?>
                <br/>
                <br/>
                <br/>
                <h2>Rastreamento de Produtos</h2>
                <div id="no-more-tables">
                    <table class="table col-md-12 padding-0  table-striped table-hover cf">
                        <thead>
                            <tr>
                                <th class="text-center size-1-4">
                                    <strong>Código de rastreio</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Produto(s)</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Prazo</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Ultima atualização</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($pedido["rastreioPedido"] as $rastreio) { ?>
                                <tr>
                                    <td data-title="Código" class="text-center size-1-4">
                                        <?= $rastreio["codigoRastreio"] ?>
                                    </td>
                                    <td data-title="Produto(s)" class="text-center size-1-4">
                                        <?
                                        $this->db->tabela = "pedido_entrega_produto";
                                        $consulta = $this->db->consulta("WHERE pedidoEntregaFK = '" . $rastreio["id"] . "'");
                                        while ($produtoEntrega = mysql_fetch_assoc($consulta)) {
                                            $this->db->tabela = "produto_combinacao";
                                            $combinacao = $this->db->consultaId($produtoEntrega["combinacaoFK"]);

                                            $this->db->tabela = "produto";
                                            $produto = $this->db->consultaId($combinacao["produtoFK"]);
                                            ?>
                                            <?= $produto["titulo"]; ?><br/>
                                            <?
                                        }
                                        ?>
                                    </td>
                                    <td data-title="Prazo" class="text-center size-1-4">
                                        <?= $rastreio["prazo"] ?> Dias úteis
                                    </td>
                                    <td data-title="Ultima atualização" class="text-center size-1-4">
                                        <?= dataHoraSite($rastreio["ultimaAlteracao"]) ?>
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>