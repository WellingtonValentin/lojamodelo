<section id="quadro-produto-semelhante">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="size-2 normal roboto">
                    Produtos Semelhantes
                </h2>
                <hr/>
                <div class="quadro-listagem">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="carousel-interna">
                                <? foreach ($produto["produtoSemelhante"] as $produtoSemelhante) { ?>
                                    <a href="<?= HOME_URL ?>/produto/detalhes/<?= $produtoSemelhante["id"] ?>/<?= arrumaString($produtoSemelhante["titulo"]) ?>.html">
                                        <div  class="item lista-produtos text-center">
                                            <div class="imagem-produto-interna">
                                                <img src="<?= $produtoSemelhante["imagem"] ?>" onload="resizeToRatioUncut(this, $('.imagem-produto-interna').width(), 260, true, true)"/>
                                            </div><br/><br/>
                                            <span class="roboto normal color-gray size-1-6"><?= $produtoSemelhante["titulo"] ?></span><br/><br/>
                                            <? if ($produtoSemelhante["estoque"]['maior'] > 0) { ?>
                                                <? if (isset($produtoSemelhante["valorDe"]) && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="roboto bold color-gray linha-sobre size-1-2">De: <?= $produtoSemelhante["valorDe"] ?></span><br/>
                                                <? } ?>
                                                <? if (isset($produtoSemelhante["valorPor"]) && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="roboto bold size-1-6 color-red">Por: <?= $produtoSemelhante["valorPor"] ?></span><br/>
                                                    <? } ?>
                                                <? } else { ?>
                                                <img src="<?= IMG_URL ?>/produto-indisponivel.jpg"/>
                                            <? } ?>
                                        </div>
                                    </a>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>