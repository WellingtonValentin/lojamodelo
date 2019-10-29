<section id="quadro-produto-visitado">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="size-2 normal roboto">
                    Produtos Visitados
                </h2>
                <hr/>
                <div class="quadro-listagem">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="carousel-interna">
                                <? foreach ($produto["produtoVisita"] as $produtoVisitado) { ?>
                                    <a href="<?= HOME_URL ?>/produto/detalhes/<?= $produtoVisitado["id"] ?>/<?= arrumaString($produtoVisitado["titulo"]) ?>.html">
                                        <div  class="item lista-produtos text-center">
                                            <div class="imagem-produto-interna">
                                                <img src="<?= $produtoVisitado["imagem"] ?>" onload="resizeToRatioUncut(this, $('#quadro-produto-visitado .quadro-listagem .imagem-produto-interna').width(), 300, true, true)"/>
                                            </div><br/><br/>
                                            <span class="roboto normal color-gray size-1-6"><?= $produtoVisitado["titulo"] ?></span><br/><br/>
                                            <? if ($produtoVisitado["estoque"]['maior'] > 0) { ?>
                                                <? if (isset($produtoVisitado["valorDe"]) && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="roboto bold color-gray linha-sobre size-1-2">De: <?= $produtoVisitado["valorDe"] ?></span><br/>
                                                <? } ?>
                                                <? if (isset($produtoVisitado["valorPor"]) && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="roboto bold size-1-6 color-red">Por: <?= $produtoVisitado["valorPor"] ?></span><br/>
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