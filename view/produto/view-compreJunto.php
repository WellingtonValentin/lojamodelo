<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12 quadro-produto-interno">
                <h2>
                    Compre Junto
                </h2>
                <? foreach ($produto["compreJunto"] as $compreJunto) { ?>
                    <div class="quadro-compre-junto">
                        <div class="row">
                            <div class="col-md-2 imagem-lista text-center" data-toggle="tooltip" data-placement="bottom" title="<?= $this->title ?>">
                                <img src="<?= $compreJunto["imagemPai"] ?>" onload="resizeToRatioUncut(this, 130, 120, true, true)"/>
                            </div>
                            <div class="col-md-1 text-center">
                                <div class="icone-circulo">
                                    <i class="glyphicon glyphicon-plus"></i>
                                </div>
                            </div>
                            <div class="col-md-2 imagem-lista text-center" data-toggle="tooltip" data-placement="bottom" title="<?= $compreJunto["titulo"] ?>">
                                <img src="<?= $compreJunto["imagemFilho"] ?>" onload="resizeToRatioUncut(this, 130, 120, true, true)"/>
                            </div>
                            <div class="col-md-4 titulo-produto-lista">
                                <?= $this->title ?> + <?= $compreJunto["titulo"] ?>
                            </div>
                            <div class="col-md-3 titulo-produto-lista valor-lista">
                                <strong class="color-laranja"><?= $compreJunto["valorPai"] ?> - <?= $compreJunto["valorFilho"] ?></strong><br/>
                                <button type="submit" name="loginCliente" class="btn btn-success">
                                    <i class="fa fa-shopping-cart"></i>
                                    COMPRAR
                                </button>
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
    <br/><br/>
</section>