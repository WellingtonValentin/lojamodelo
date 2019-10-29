<section id="pagina-interna" class="pagina-finalizacao">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1 class="size-1-9 normal roboto"><?= $this->title ?></h1>
                <hr/>
                <form action="<?= HOME_URL ?>/pedido/salvar-pedido/enviar-pagamento.html" method="POST">
                    <div class="row">
                        <div class="col-md-6 quadro-dado-compra">
                            <h2 class="size-1-3 bold roboto">Dados da compra</h2>
                            <table class="table">
                                <tbody>
                                    <tr class="size-1-3 normal color-gray">
                                        <td align="left">
                                            <?= $modelo->totalProdutoCarrinho() ?> Produto<?= ($modelo->totalProdutoCarrinho() > 1) ? "s" : "" ?>
                                        </td>
                                        <td align="right">
                                            R$ <?= number_format($modelo->valorTotalCarrinho(), 2, ",", ".") ?>
                                        </td>
                                    </tr>
                                    <? if (chkArray($_SESSION["PEDIDO"], "FRETE")) { ?>
                                        <tr class="size-1-3 normal color-gray">
                                            <td align="left">
                                                Frete para <?= chkArray($enderecoSelecionado, "cidade") ?>
                                            </td>
                                            <td align="right">
                                                <?= (is_numeric($_SESSION["PEDIDO"]["FRETE"]["VALOR"])) ? "R$ " . number_format($_SESSION["PEDIDO"]["FRETE"]["VALOR"], 2, ",", ".") : $_SESSION["PEDIDO"]["FRETE"]["VALOR"] ?>
                                            </td>
                                        </tr>
                                    <? } ?>
                                    <? if (chkArray($_SESSION["PEDIDO"], "CUPOM")) { ?>
                                        <tr class="size-1-3 normal color-gray">
                                            <td align="left">
                                                Valor do desconto
                                            </td>
                                            <td align="right">
                                                R$ <?= number_format($modelo->verificaCupomDesconto($modelo->valorTotalCarrinho()), 2, ",", ".") ?>
                                            </td>
                                        </tr>
                                    <? } ?>
                                    <tr class="alert alert-success size-1-6">
                                        <td align="left">
                                            <strong>Total a pagar</strong>
                                        </td>
                                        <td align="right">
                                            R$ <?= number_format($modelo->valorTotalCarrinho(TRUE, TRUE), 2, ",", ".") ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h2 class="size-1-3 bold roboto">Endereço de entrega</h2>
                            <div class="quadro-finalizacao size-1-3">
                                <? if (chkArray($enderecoEntrega, 0)) { ?>
                                    <div class="modal fade" id="modal-endereco" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Escolha o endereço</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div onclick="mudarEndereco('')" class="quadro-endereco cursor-pointer roboto normal margin-0 alert <?= (!chkArray($_SESSION["PEDIDO"], "ENDERECO")) ? "alert-success" : "" ?>">
                                                        <div class="col-xs-1">
                                                            <input type="radio" name="enderecoSelecionado" <?= (!chkArray($_SESSION["PEDIDO"], "ENDERECO")) ? "checked" : "" ?> value="" onclick="mudarEndereco($(this).val())"> 
                                                        </div>
                                                        <div class="col-xs-10 padding-0">
                                                            <strong>Destinatário: </strong><?= chkArray($_SESSION['CLIENTE'], "nome") ?><br/>
                                                            <strong>Endereço: </strong><?= chkArray($_SESSION['CLIENTE'], "endereco") ?><br/>
                                                            <strong>Número: </strong><?= chkArray($_SESSION['CLIENTE'], "numero") ?>, <strong>Complemento: </strong><?= chkArray($_SESSION['CLIENTE'], "complemento") ?>, <strong>Bairro: </strong><?= chkArray($_SESSION['CLIENTE'], "bairro") ?><br/>
                                                            <strong>Cidade: </strong><?= chkArray($_SESSION['CLIENTE'], "cidade") ?> - <strong>UF: </strong><?= chkArray($_SESSION['CLIENTE'], "estado") ?> - <strong>CEP: </strong><?= chkArray($_SESSION['CLIENTE'], "cep") ?><br/><br/>
                                                        </div>
                                                    </div>
                                                    <? foreach ($enderecoEntrega as $valorEndereco) { ?> 
                                                        <div onclick="mudarEndereco('<?= $valorEndereco["id"] ?>')" class="quadro-endereco cursor-pointer roboto normal margin-0 alert <?= (chkArray($_SESSION["PEDIDO"], "ENDERECO")) ? ($_SESSION["PEDIDO"]["ENDERECO"]["ID"] == $valorEndereco["id"]) ? "alert-success" : "" : "" ?>" onclick="mudarFrete($('#seleciona-<?= $ind ?>').val(), '<?= chkArray($envio, "valor") ?>', '<?= chkArray($envio, "prazoDias") ?>')">
                                                            <div class="col-xs-1">
                                                                <input type="radio" name="enderecoSelecionado" <?= (chkArray($_SESSION["PEDIDO"], "ENDERECO")) ? ($_SESSION["PEDIDO"]["ENDERECO"]["ID"] == $valorEndereco["id"]) ? "checked" : "" : "" ?> value="<?= $valorEndereco["id"] ?>" onclick="mudarEndereco($(this).val())"> 
                                                            </div>
                                                            <div class="col-xs-10 padding-0">
                                                                <strong>Destinatário: </strong><?= chkArray($valorEndereco, "endereco") ?><br/>
                                                                <strong>Endereço: </strong><?= chkArray($valorEndereco, "endereco") ?><br/>
                                                                <strong>Número: </strong><?= chkArray($valorEndereco, "numero") ?>, <strong>Complemento: </strong><?= chkArray($valorEndereco, "complemento") ?>, <strong>Bairro: </strong><?= chkArray($valorEndereco, "bairro") ?><br/>
                                                                <strong>Cidade: </strong><?= chkArray($valorEndereco, "cidade") ?> - <strong>UF: </strong><?= chkArray($valorEndereco, "estado") ?> - <strong>CEP: </strong><?= chkArray($valorEndereco, "cep") ?><br/><br/>
                                                            </div>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary pull-right" onclick="location.href = '<?= HOME_URL ?>/cliente/endereco-entrega/meus-enderecos.htm#conteudo'">Cadastrar outro endereço</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                                <div class="row">
                                    <div class="col-md-12 color-gray" id="endereco-entrega">
                                        <strong>Destinatário: </strong><?= (chkArray($enderecoSelecionado, "destinatario")) ? $enderecoSelecionado['destinatario'] : $_SESSION['CLIENTE']['nome'] ?><br/>
                                        <strong>Endereço: </strong><?= chkArray($enderecoSelecionado, "endereco") ?><br/>
                                        <strong>Número: </strong><?= chkArray($enderecoSelecionado, "numero") ?>, <strong>Complemento: </strong><?= chkArray($enderecoSelecionado, "complemento") ?>, <strong>Bairro: </strong><?= chkArray($enderecoSelecionado, "bairro") ?><br/>
                                        <strong>Cidade: </strong><?= chkArray($enderecoSelecionado, "cidade") ?> - <strong>UF: </strong><?= chkArray($enderecoSelecionado, "estado") ?> - <strong>CEP: </strong><?= chkArray($enderecoSelecionado, "cep") ?><br/>
                                        <? if (chkArray($enderecoEntrega, 0)) { ?>
                                            <button type="button" class="btn btn-primary size-0-8" data-toggle="modal" data-target="#modal-endereco">
                                                <i class="glyphicon glyphicon-refresh"></i>&nbsp;&nbsp;&nbsp;ALTERAR ENDEREÇO
                                            </button>
                                        <? } else { ?>
                                            <button type="button" class="btn btn-primary size-0-8" onclick="location.href = '<?= HOME_URL ?>/cliente/endereco-entrega/meus-enderecos.htm#conteudo'">
                                                <i class="glyphicon glyphicon-refresh"></i>&nbsp;&nbsp;&nbsp;ALTERAR ENDEREÇO
                                            </button>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="size-1-3 bold roboto">Escolha a forma de entrega</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <? foreach ($formasEnvio as $ind => $envio) { ?>
                                <div class="col-xs-12 col-sm-4 col-md-2 cursor-pointer quadro-forma-entrega roboto normal size-1-2 margin-0 alert <?= (chkArray($_SESSION["PEDIDO"], "FRETE")) ? ($_SESSION["PEDIDO"]["FRETE"]["TIPO"] == $envio["tipo"]) ? "alert-info" : "" : "" ?> radios-0" onclick="mudarFrete($('#seleciona-<?= $ind ?>').val(), '<?= chkArray($envio, "valor") ?>', '<?= chkArray($envio, "prazoDias") ?>')">
                                    <input id="seleciona-<?= $ind ?>" type="radio" value="<?= $envio["tipo"] ?>" onclick="mudarFrete($(this).val(), '<?= chkArray($envio, "valor") ?>', '<?= chkArray($envio, "prazoDias") ?>')" name="formaEntrega" <?= (chkArray($_SESSION["PEDIDO"], "FRETE")) ? ($_SESSION["PEDIDO"]["FRETE"]["TIPO"] == $envio["tipo"]) ? "checked" : "" : "" ?>>&nbsp;&nbsp;
                                    <strong><?= $envio["titulo"] ?></strong><br/>
                                    <?= chkArray($envio, "frete") ?>
                                    <?= chkArray($envio, "prazo") ?>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <div  class="alert alert-danger text-left size-1-2">
                                <strong>Atenção!</strong> O prazo come&ccedil;a a contar a partir da aprova&ccedil;&atilde;o do pagamento e pode estar sujeito a altera&ccedil;&otilde;es sendo ele confirmado no ato da postagem de seu pedido.
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-xs-12 cursor-pointer" data-toggle="collapse" data-target="#quadro-cupom" aria-expanded="false" aria-controls="quadro-cupom">
                            <h2 class="size-1-3 bold roboto pull-left">Cupom de desconto</h2>
                            <i class="glyphicon glyphicon-chevron-down size-1-6 color-light-gray pull-right"></i>
                        </div>
                        <div class="col-xs-12">
                            <div class="collapse <?= (chkArray($_SESSION["PEDIDO"], "CUPOM")) ? "in" : "" ?>" id="quadro-cupom">
                                <div class="well">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-7">
                                            <div class="alert alert-success size-1-2 margin-0" role="alert">
                                                <strong><i class="fa fa-ticket"></i> Cupom: </strong> Caso possua algum cupom de desconto informe o código no campo ao lado.
                                            </div>
                                        </div>
                                        <div id="area-cupom" class="col-xs-12 col-sm-2 input-group">
                                            <input type="text" id="codigo-cupom" class="form-control" placeholder="Código" onkeyup="maiuscula(this)">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" onclick="validarCupom($('#codigo-cupom').val())">
                                                    <i class="glyphicon glyphicon-refresh"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <div class="col-xs-12 col-sm-3 size-2 roboto normal text-success" id="valor-cupom">
                                            <? if (chkArray($_SESSION["PEDIDO"], "CUPOM")) { ?>
                                                <strong>Desconto: <?= ($_SESSION['PEDIDO']['CUPOM']['TIPO'] == "PORCENTAGEM") ? number_format($_SESSION['PEDIDO']['CUPOM']['VALOR'], 0, ",", ".") . "%" : "R$ " . number_format($_SESSION['PEDIDO']['CUPOM']['VALOR'], 2, ",", ".") ?></strong>
                                            <? } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <? if (chkArray($_SESSION["PEDIDO"], "FRETE")) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="size-1-3 bold roboto">Escolha a forma de pagamento</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <? foreach ($formasPagamento as $ind => $pagamento) { ?>
                                    <div class="col-xs-12 col-sm-4 col-md-3 quadro-forma-pagamento text-center">
                                        <img src="<?= HOME_URL ?>/view/_image/forma_pagamento/<?= strtolower($pagamento["CLASSE"]) ?>-cartoes.png"/><br/><br/>
                                        <span class="roboto size-1-2 color-dark-gray light"><?= $pagamento["TITULO"] ?></span><br/>
                                        <button type="submit" value="<?= $pagamento["CLASSE"] ?>" name="formaPagamento" class="btn btn-sm btn-success">
                                            CONFIRMAR
                                        </button><br/>
                                        <? if ($pagamento['CLASSE'] == "BOLETO" && $configValores['descontoBoleto']) { ?>
                                            <br/>
                                            <span class="label label-danger size-1-2">Desconto de <?= $configValores['descontoBoleto'] ?>%</span>
                                        <? } else { ?>
                                            <br/>
                                            <span class="text-info cursor-pointer" data-toggle="modal" data-target="#modal-pagamento-<?= strtolower($pagamento["CLASSE"]) ?>">Informações de parcelamento <i class="fa fa-info-circle"></i></span>

                                            <div class="modal fade" id="modal-pagamento-<?= strtolower($pagamento["CLASSE"]) ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">
                                                                <?= $pagamento["TITULO"] ?>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table parcelamento size-1-4">
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="2" class="text-center">
                                                                            Confira as opções de parcelamento da forma de pagamento.<br/>
                                                                            Estes valores s&atilde;o aproximados e podem sofrer altera&ccedil;&otilde;es.
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <div class="col-md-6 text-center">
                                                                                <b>1x</b> <strong>R$ <?= number_format($modelo->valorTotalCarrinho(TRUE, TRUE), 2, ",", ".") ?></strong> <b>sem juros</b>
                                                                            </div>
                                                                            <? if (isset($pagamento["parcela"])) { ?>
                                                                                <? foreach ($pagamento["parcela"] as $parcela => $valor) { ?>
                                                                                    <div class="col-md-6 text-center">
                                                                                        <b><?= $parcela ?>x</b> <strong>R$ <?= number_format($valor, 2, ",", ".") ?></strong> <?= ($parcela <= $pagamento["semJuros"]) ? "<b>sem juros</b>" : "" ?>
                                                                                    </div>
                                                                                <? } ?>
                                                                            <? } ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <hr/>
                                                            <p class="color-gray normal size-1-2 text-left text-justify">
                                                                <strong>Informações adicionais.</strong><br/>
                                                                &bull; Para finalizar a sua compra primeiro selecione a forma de entregra desejada.<br/>
                                                                &bull; Uma vez selecionado irá surgir para você as formas de pagamento, revise bem seu pedido primeiro e depois selecione a forma de pagamento desejada.<br/>
                                                                &bull; Caso por algum motivo o frete de seu pedido não pode ser cálculado no ato da compra pode finalizar o pedido normalmente, pois ele será salvo e em breve calcularemos por você o frete e adicionaremos o valor ao pedido e então será liberado o pagamento de seu pedido.<br/>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        <hr/>
                    <? } ?>
                </form>
            </div>
        </div>
    </div>
</section>