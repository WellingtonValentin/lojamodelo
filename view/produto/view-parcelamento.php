<div class="row produto-disponivel">
    <div class="col-md-12">
        <span for="formaPagamento">Formas de Pagamento</span>
    </div>
</div>
<div class="row produto-disponivel">
    <div class="col-md-12">
        <ul class="nav nav-tabs" role="tablist">
            <? foreach ($produto["formapagamento"] as $ind => $formaPagamento) { ?>
                <li role="presentation" >
                    <a href="#<?= $formaPagamento["classe"] ?>" aria-controls="<?= $formaPagamento["classe"] ?>" role="tab" data-toggle="tab">
                        <img src="<?= IMG_URL ?>/forma_pagamento/<?= strtolower($formaPagamento["classe"]) ?>-icon.png" height="15" title="<?= $formaPagamento["titulo"] ?>" alt="<?= $formaPagamento["titulo"] ?>"/>
                    </a>
                </li>
            <? } ?>
        </ul>
        <div class="tab-content">
            <? foreach ($produto["formapagamento"] as $ind => $formaPagamento) { ?>
                <div role="tabpanel" class="tab-pane " id="<?= $formaPagamento["classe"] ?>">
                    <table class="table parcelamento size-1-4">
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center">
                                    Estes valores s&atilde;o aproximados e podem sofrer altera&ccedil;&otilde;es.
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="col-md-6 text-center">
                                        <b>1x</b> <strong><?= $retorno["valorPor"] ?></strong> <b>sem juros</b>
                                    </div>
                                    <? if (isset($formaPagamento["parcela"])) { ?>
                                        <? foreach ($formaPagamento["parcela"] as $parcela => $valor) { ?>
                                            <div class="col-md-6 text-center">
                                                <b><?= $parcela ?>x</b> <strong>R$ <?= number_format($valor, 2, ",", ".") ?></strong> <?= ($parcela <= $formaPagamento["semJuros"]) ? "<b>sem juros</b>" : "" ?>
                                            </div>
                                        <? } ?>
                                    <? } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <? } ?>
        </div>
    </div>
</div>