<section id="quadro-avaliacao">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="size-2 normal roboto">
                    Avaliações dos Clientes
                </h2>
                <hr/>
                <?
                foreach ($produto["pesquisaSatisfacao"] as $pesquisa) {
                    ?>
                    <div class="quadro-pesquisa roboto normal size-1-2 color-dark-gray">
                        <div class="row">
                            <div class="col-md-3">
                                <? if ($pesquisa["avaliacao"]) { ?>
                                    <? for ($i = 0; $i < $pesquisa["avaliacao"]; $i++) { ?>
                                        <div class="estrela-avaliacao">
                                            <i class="fa fa-star"></i>
                                        </div>
                                    <? } ?>
                                    <? for ($i = $pesquisa["avaliacao"]; $i < 5; $i++) { ?>
                                        <div class="estrela-avaliacao estrela-inativa">
                                            <i class="fa fa-star"></i>
                                        </div>
                                    <? } ?>
                                    <br/><br/>
                                <? } ?>
                                <strong>Autor: </strong><?= $pesquisa["autor"] ?><br/>
                                <strong>Data: </strong><?= $pesquisa["data"] ?>
                            </div>
                            <div class="col-md-3">
                                <div class="quadro-pro-contra">
                                    <strong>PRÓS</strong><br/>
                                    <?= $pesquisa["pros"] ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quadro-pro-contra">
                                    <strong class="color-red">CONTRAS</strong><br/>
                                    <?= $pesquisa["contras"] ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quadro-pro-contra">
                                    <strong class="color-blue">COMENTÁRIO</strong><br/>
                                    <?= $pesquisa["comentario"] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
    <br/><br/>
</section>