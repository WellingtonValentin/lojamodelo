<section id="quadro-informacoes">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="size-2 normal roboto">
                    Informações sobre o produto
                </h2>
                <hr/>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs" role="tablist">
                    <? foreach ($produto["informacao"] as $ind => $informacao) { ?>
                        <li role="tab-<?= arrumaString($informacao["titulo"]) ?>" class="<?= ($ind == 0) ? "active" : "" ?>">
                            <a href="#tab-<?= arrumaString($informacao["titulo"]) ?>" class="size-1-6 roboto normal color-gray" aria-controls="tab-<?= arrumaString($informacao["titulo"]) ?>" role="tab" data-toggle="tab">
                                <?= $informacao["titulo"] ?>
                            </a>
                        </li>
                    <? } ?>
                </ul>
                <div class="tab-content">
                    <? foreach ($produto["informacao"] as $ind => $informacao) { ?>
                        <div role="tabpanel" class="tab-pane texto-informacao roboto normal color-gray size-1-4 <?= ($ind == 0) ? "active" : "" ?>" id="tab-<?= arrumaString($informacao["titulo"]) ?>">
                            <?= $informacao["texto"] ?>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</section>