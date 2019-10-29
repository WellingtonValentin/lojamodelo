<?php if (!defined('ABSPATH')) exit; ?>
<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3">
                <? require_once ABSPATH . '/view/cliente/view-menu.php'; ?>
            </div>
            <div class="col-md-9 borda-padrao">
                <h2>Meus Cupons</h2>
                <div id="no-more-tables">
                    <table class="table col-md-12 padding-0  table-striped table-hover cf">
                        <thead>
                            <tr>
                                <th class="text-center size-1-4">
                                    <strong>Código</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Data do Cadastro</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Data de Vencimento</strong>
                                </th>
                                <th class="text-left size-1-4">
                                    <strong>Status</strong>
                                </th>
                                <th class="text-left size-1-4">
                                    <strong>Valor</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Visualizar</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ((array) $listagemCupom as $cupom) { ?>
                                <tr <?= ($cupom["dataFim"] < date("Y-m-d") || ($cupom["status"] == "CANCELADO")) ? "class=\"danger\"" : "" ?>>
                                    <td data-title="Código" class="text-center size-1-4">
                                        <?= $cupom["codigo"] ?>
                                    </td>
                                    <td data-title="Cadastro" class="text-center size-1-4">
                                        <?= dataSite($cupom["dataInicio"]) ?>
                                    </td>
                                    <td data-title="Vencimento" class="text-center size-1-4">
                                        <?= dataSite($cupom["dataFim"]) ?>
                                    </td>
                                    <td data-title="Status" class="text-left size-1-4">
                                        <?= $cupom["status"] ?>
                                    </td>
                                    <td data-title="Valor" class="text-center size-1-4">
                                        <?= ($cupom["tipoDesconto"] == "VALOR") ? "R$ " . number_format($cupom["valor"], 2, ",", ".") : number_format($cupom["valor"], 0, "", ".") . "%" ?>
                                    </td>
                                    <td data-title="Visualizar" class="text-center size-1-4">
                                        <a  data-toggle="modal" data-target="#myModal<?= $cupom["id"] ?>">
                                            <i class="glyphicon glyphicon-search color-padrao"></i>
                                        </a>
                                    </td>
                                </tr>
                            <div class="modal fade" id="myModal<?= $cupom["id"] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body background-padrao roboto color-contraste">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <img src="<?= HOME_URL ?>/view/_image/logo.png" alt="" class="img-thumbnail"><br/><br/><br/>
                                                    <div class="light size-4 text-center"><?= ($cupom["tipoDesconto"] == "VALOR") ? "R$ " . number_format($cupom["valor"], 2, ",", ".") : number_format($cupom["valor"], 0, "", ".") . "%" ?></div><br/><br/><br/>
                                                    <div class="light size-1-2 text-center">Válido até dia: <?= dataSite($cupom["dataFim"]) ?></div>
                                                </div>
                                                <div class="col-md-8">
                                                    <h2>
                                                        Código do cupom
                                                        <span><?= $cupom["codigo"] ?></span>
                                                    </h2>
                                                    <p class="size-1-2">
                                                        Atenção cupom valido apenas para uma compra, ou seja, assim que utilizado ele sera marcado como resgatado mesmo se a compra for em um valor inferior ao mesmo recomendamos adicionar mais produtos caso o valor total de sua compra não alcance o valor do cupom
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>