<?php if (!defined('ABSPATH')) exit; ?>
<? if (chkArray($produto["produto"], "variacaoUnica")) { ?>
    <style>
        .aguarda-liberacao{
            display: block !important;
        }
        <? if (chkArray($produto["produto"], "estoque") <= 0 || chkArray($produto["produto"], "status") == "I") { ?>
            .produto-indisponivel{
                display: block !important;
            }
            .produto-disponivel{
                display: none !important;
            }
        <? } ?>
    </style>
<? } ?>
<section id="pagina-interna" class="pagina-produto">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="size-2-2 normal roboto v-bottom">
                            <?= $produto["produto"]["titulo"] ?>&nbsp;&nbsp;<small class="size-0-6 color-gray normal roboto">(cód. <?= $produto["produto"]["codigo"] ?>)</small>
                        </h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <? if (chkArray($produto, "avaliacao")) { ?>
                            <? if (chkArray($produto["avaliacao"], "total")) { ?>
                                <div class="row">
                                    <div class="col-xs-12 quadro-estrelas">
                                        <?
                                        $avaliacao = $produto["avaliacao"]["media"] / $produto["avaliacao"]["total"];
                                        $avaliacao2 = floor($avaliacao);
                                        for ($i = 0; $i < $avaliacao2; $i++) {
                                            ?>
                                            <div class="estrela-avaliacao">
                                                <i class="fa fa-star"></i>
                                            </div>
                                            <?
                                        }
                                        for ($i = $avaliacao2; $i < 5; $i++) {
                                            if ($i == $avaliacao2 AND ! is_int($avaliacao)) {
                                                ?>
                                                <div class="estrela-avaliacao estrela-metade">
                                                    <i class="fa fa-star-half-o"></i>
                                                </div>
                                            <? } else { ?>
                                                <div class="estrela-avaliacao estrela-inativa">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            <? } ?>
                                        <? } ?>
                                        <small class="roboto light size-1 color-gray">(<?= $produto["avaliacao"]["total"] ?> avaliações)</small>
                                    </div>
                                </div>
                            <? } ?>
                        <? } ?>
                        <div class="row hidden-xs">
                            <? if (chkArray($produto, "fotos")) { ?>
                                <div class="col-md-2 hidden-sm hidden-xs">
                                    <? if (count($produto["fotos"]) >= 4) { ?>
                                        <div class="cursor-pointer text-center color-dark-gray size-1-8" onclick="$('#pagina-interna.pagina-produto .quadro-galeria a:first-child').appendTo('.quadro-galeria')">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </div>
                                    <? } ?>
                                    <div class="quadro-galeria">
                                        <div  id="galleria-zoom">
                                            <?
                                            $conta = 1;
                                            foreach ($produto["fotos"] as $foto) {
                                                $srcFoto = UPLOAD_URL . "/produto/" . $produto["produto"]["id"] . "/" . $foto["foto"];
                                                if ($conta == 1) {
                                                    $fotoPrincipal = $srcFoto;
                                                    $tamanhoImagem = @getimagesize($fotoPrincipal);
                                                }
                                                $conta++;
                                                ?>
                                                <a  <?= ($tamanhoImagem[0] > 370 && $tamanhoImagem[1] > 330) ? "data-image='" . $srcFoto . "' data-zoom-image='" . $srcFoto . "' class='elevatezoom-gallery'" : "href='$srcFoto' class=\"fancyboxGaleria\"" ?>>
                                                    <img src="<?= $srcFoto ?>" width="100%" class="img-thumbnail"/>
                                                </a>
                                            <? } ?>
                                        </div>
                                        <? foreach ($produto["videos"] as $video) { ?>
                                            <a class="fancyboxVarious fancybox.iframe" href="http://www.youtube.com/embed/<?= $video["codigo"] ?>?autoplay=1">
                                                <img src="http://i1.ytimg.com/vi/<?= $video["codigo"] ?>/default.jpg" width="100%" class="img-thumbnail"/>
                                            </a>
                                        <? } ?>
                                    </div>
                                    <? if (count($produto["fotos"]) >= 4) { ?>
                                        <div class="cursor-pointer text-center color-dark-gray size-1-8" onclick="$('#pagina-interna.pagina-produto .quadro-galeria a:last-child').prependTo('.quadro-galeria')">
                                            <i class="glyphicon glyphicon-chevron-down"></i>
                                        </div>
                                    <? } ?>
                                </div>
                                <div class="col-md-10 col-xs-12">
                                    <div class="foto-produto-principal">
                                        <? if ($tamanhoImagem[0] > 370 && $tamanhoImagem[1] > 330) { ?>
                                            <div class="quadro-ampliar-imagem roboto color-dark-gray size-1-2 normal">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="glyphicon glyphicon-search"></i>
                                                    </div>
                                                    <div class="col-xs-9">
                                                        Passe o mouse
                                                        para ampliar
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                        <img src="<?= $fotoPrincipal ?>" data-zoom-image="<?= $fotoPrincipal ?>" width="100%" <?= ($tamanhoImagem[0] > 370 && $tamanhoImagem[1] > 330) ? "class=\"elevatezoom\"" : "" ?> id="foto-grande"/>
                                    </div>
                                </div>
                            <? } else { ?>
                                <div class="col-xs-12">
                                    <img src="<?= IMG_URL ?>/padrao/semFoto.jpg" width="100%"/>
                                </div>
                            <? } ?>
                        </div>
                        <div class="row visible-xs">
                            <? if (chkArray($produto, "fotos")) { ?>
                                <div <?= (count($produto["fotos"]) > 1) ? "class=\"carousel-interna\"" : "" ?>>
                                    <?
                                    foreach ($produto["fotos"] as $foto) {
                                        $srcFoto = UPLOAD_URL . "/produto/" . $produto["produto"]["id"] . "/" . $foto["foto"];
                                        ?>
                                        <a href="<?= $srcFoto ?>" class="fancyboxSingle" rel="galeria">
                                            <div class="<?= (count($produto["fotos"]) > 1) ? "item" : "" ?> foto-produto-principal">
                                                <img src="<?= $srcFoto ?>" onload="resizeToRatioUncut(this, '', '', true, true)" class="img-thumbnail" id="foto-grande"/>
                                            </div>
                                        </a>
                                    <? } ?>
                                </div>
                            <? } else { ?>
                                <div class="col-md-12">
                                    <div class="foto-produto-principal">
                                        <img src="<?= IMG_URL ?>/semFoto.jpg" onload="resizeToRatioUncut(this, '', '', true, true)" id="foto-grande"/>
                                    </div>
                                </div>
                            <? } ?>
                        </div>
                        <br/><br/>
                        <div class="row" id="redes-sociais">
                            <div class="col-xs-1 padding-0">
                                <a data-toggle="modal" data-target="#indicar"class="btn btn-block btn-xs btn-primary padding-0">
                                    <span class="glyphicon glyphicon-envelope"></span>  
                                </a>
                                <div class="modal fade bs-example-modal-sm" id="indicar" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Indicar produto.</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form class="indicarProduto">
                                                    <div class="form-group">
                                                        <input type="hidden" name="produtoFK" value="<?= $produto["produto"]["id"] ?>"/>
                                                        <div class="row">
                                                            <div class="col-md-10 col-md-offset-1 input-group">
                                                                <input type="text" name="nomeAmigo" class="form-control" id="nomeAmigo" placeholder="Nome do seu amigo">
                                                            </div>
                                                        </div><br/>
                                                        <div class="row">
                                                            <div class="col-md-10 col-md-offset-1 input-group">
                                                                <input type="text" name="emailAmigo" class="form-control" id="emailAmigo" placeholder="E-mail do seu amigo">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-10 col-md-offset-1 input-group">
                                                                <input type="text" name="nome" class="form-control" id="nomeCliente" placeholder="Seu nome">
                                                            </div>
                                                        </div><br/>
                                                        <div class="row">
                                                            <div class="col-md-10 col-md-offset-1 input-group">
                                                                <input type="text" name="email" class="form-control" id="emailCliente" placeholder="Seu e-mail">
                                                            </div>
                                                        </div><br/>
                                                        <div id="resultado-modal-sucesso" class="alert alert-success display-none size-1-2 text-center" role="alert">
                                                            Um e-mail com o produto foi enviado para o seu amigo!
                                                        </div>
                                                        <div id="resultado-modal-erro" class="alert alert-danger display-none size-1-2 text-center" role="alert"></div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
                                                <button class="btn btn-success" id="submitIndicarProduto">ENVIAR</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-5">
                                <div class="fb-like" data-href="<?= HOME_URL . $_SERVER["REQUEST_URI"] ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
                            </div>
                            <div class="col-md-3  col-xs-5">
                                <div id="fb-root"></div>
                                <script>
                                    (function (d, s, id) {
                                        var js, fjs = d.getElementsByTagName(s)[0];
                                        if (d.getElementById(id))
                                            return;
                                        js = d.createElement(s);
                                        js.id = id;
                                        js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.4";
                                        fjs.parentNode.insertBefore(js, fjs);
                                    }(document, 'script', 'facebook-jssdk'));
                                </script>
                                <div class="fb-share-button" data-href="<?= HOME_URL . $_SERVER["REQUEST_URI"] ?>" data-layout="button"></div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                                <script>
                                    !function (d, s, id) {
                                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                        if (!d.getElementById(id)) {
                                            js = d.createElement(s);
                                            js.id = id;
                                            js.src = p + '://platform.twitter.com/widgets.js';
                                            fjs.parentNode.insertBefore(js, fjs);
                                        }
                                    }(document, 'script', 'twitter-wjs');
                                </script>
                            </div>
                            <div class="col-md-2 col-xs-6 padding-0">
                                <!-- Posicione esta tag no cabeï¿½alho ou imediatamente antes da tag de fechamento do corpo. -->
                                <script src="https://apis.google.com/js/platform.js" async defer>
                                    {
                                        lang: 'pt-BR'
                                    }
                                </script>

                                <!-- Posicione esta tag onde vocï¿½ deseja que o botï¿½o +1 apareï¿½a. -->
                                <div class="g-plusone" data-size="medium"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <? if (chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                            <div class="quadro-valor-produto">
                                <div class="row">
                                    <? if ($produto["produto"]["freteGratis"] == "S") { ?>
                                        <div class="col-xs-12">
                                            <div class="quadro-frete-gratis color-white size-1-4">
                                                <i class="fa fa-truck"></i> Frete Grátis
                                            </div>
                                        </div>
                                    <? } ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="variacoes-produto">
                                        <? if (isset($produto["variacao"])) { ?>
                                            <? foreach ($produto["variacao"] as $ind => $variacao) { ?>
                                                <ul>
                                                    <li class="titulo-variacao source size-1-6 light color-dark-gray"><?= $variacao["titulo"] ?></li>
                                                    <? foreach ($variacao["valor"] as $variacaoValor) { ?>
                                                        <li id="<?= $variacaoValor["id"] ?>" onclick="selecionarVariacao('<?= arrumaString($variacao["titulo"]) ?>', $(this).attr('id'), '<?= $produto["produto"]["id"] ?>')" class="variacao-opcao text-center source bold size-1-8 color-dark-gray <?= arrumaString($variacao["titulo"]) ?>" <?= (substr($variacaoValor["icone"], 0, 1) == "#") ? "style=\"background: " . $variacaoValor["icone"] . "\"" : "" ?>>
                                                            <? if ($variacaoValor["icone"] == "") { ?>
                                                                <img src="<?= UPLOAD_URL ?>/variacao/<?= $variacaoValor["nomeFoto"] ?>" height="40"/>
                                                            <? } else { ?>
                                                                <?= (substr($variacaoValor["icone"], 0, 1) != "#") ? $variacaoValor["icone"] : "" ?>
                                                            <? } ?>
                                                        </li>
                                                    <? } ?>
                                                </ul>
                                            <? } ?>
                                        <? } ?>
                                    </div>
                                </div>
                                <br/>
                                <div class="row display-none" id="alerta-erro">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger size-1-4" role="alert">
                                            Erro ao buscar informações. Tente novamente.
                                        </div>
                                    </div>
                                </div>
                                <div class="row produto-indisponivel display-none">
                                    <div class="col-md-12">
                                        <form action="" method="POST">
                                            <div class="alert alert-danger size-1-2" role="alert">
                                                <strong>Atenção!</strong><br/>
                                                O Produto selecionado encontra-se sem estoque, 
                                                informe abaixo seu e-mail e sera avisado assim que o produto estiver novamente disponível em nossos estoques.<br/><br/>
                                                <div class="form-group row">
                                                    <div class="col-md-8 col-xs-12">
                                                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="E-mail">
                                                    </div>
                                                    <div class="col-md-4 col-xs-12">
                                                        <button type="submit" name="aviseme" class="btn btn-primary botao-enviar">
                                                            <i class="glyphicon glyphicon-envelope"></i>&nbsp;&nbsp;&nbsp;ENVIAR
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row produto-disponivel">
                                    <div id="quadro-valores-produto" class="col-md-5 col-xs-12 roboto color-dark-gray">
                                        <span id="valorDe" class="light size-1-2 linha-sobre"><?= (chkArray($produto["produto"], "valorDe")) ? "De " . chkArray($produto["produto"], "valorDe") : "" ?></span><br/>
                                        <span class="size-1">Por Apenas</span><br/>
                                        <strong id="valorPor" class="size-3 color-red"><?= $produto["produto"]["valorPor"] ?></strong><br/>
                                        <span class="size-1-2 textoParcelamento color-dark-gray"><?= chkArray($produto["produto"], "textoParcelamento") ?></span>
                                    </div>
                                    <div class="col-md-7 col-xs-12 text-right">
                                        <form action="<?= HOME_URL ?>/carrinho/meus-produtos/detalhes.html#conteudo" method="POST">
                                            <input type="hidden" name="variacao" value="<?= (chkArray($produto["produto"], "variacaoID")) ?>" id="idVariacao"/>
                                            <div class="row">
                                                <div class="col-xs-6 col-xs-offset-3 padding-0 aguarda-liberacao text-center">
                                                    <div class="input-group quadro-quantidade-produto">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="quantidade">
                                                                <span class="glyphicon glyphicon-minus"></span>
                                                            </button>
                                                        </span>
                                                        <input type="text" name="quantidade" class="form-control input-number" value="1" min="1" max="99999">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="quantidade">
                                                                <span class="glyphicon glyphicon-plus"></span>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <br/>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 padding-0 text-center" id="quadro-botao-compra" <?= (!chkArray($produto["produto"], "variacaoUnica")) ? "data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"\" data-original-title=\"Selecione uma variação para efetuar a compra!\"" : "" ?>>
                                                <button type="submit" name="comprar" class="btn btn-sm btn-success size-1-6" id="botao-comprar" <?= (!chkArray($produto["produto"], "variacaoUnica")) ? "disabled=\"disabled\"" : "" ?>>
                                                    <i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;&nbsp;COMPRAR
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <a href="#redes-sociais">
                                    <div id="compra-flutuante" class="produto-disponivel">
                                        <div class="row visible-xs hidden-sm hidden-md hidden-lg quadro-compra-produto-flutuante background-padrao roboto size-1-2 bold color-contraste">
                                            <div class="icone-seta-cima-produto-flutuante background-padrao color-contraste">
                                                <i class="glyphicon glyphicon-arrow-up"></i>
                                            </div>
                                            <div class="col-xs-12">
                                                <div class="row vcenter">
                                                    <div class="col-xs-6 text-center vcenter">
                                                        <strong id="valorPor-2" class="text-center" style="display: block;">
                                                            <?= $produto["produto"]["valorPor"] ?>
                                                        </strong>
                                                        <? if (chkArray($produto["produto"], "textoParcelamento")) { ?>
                                                            <span class="color-white size-0-5 normal roboto" style="display: block;">
                                                                <?= chkArray($produto["produto"], "textoParcelamento") ?>
                                                            </span>
                                                        <? } ?>
                                                    </div>
                                                    <div class="col-xs-6 size-1-5 text-center vcenter quadro-comprar">
                                                        COMPRAR
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <br/>
                                <div class="row aguarda-liberacao produto-disponivel">
                                    <div class="col-md-12">
                                        <span for="buscaCep">Calcular o Frete e Prazo</span>
                                    </div>
                                </div>
                                <div class="row aguarda-liberacao produto-disponivel">
                                    <div class="col-md-5 text-center">
                                        <div class="input-group">
                                            <input type="text" class="form-control cep" id="buscaCep" placeholder="CEP">
                                            <div class="input-group-btn">
                                                <button class="btn btn-default" type="button" onclick="calcularFrete($('#buscaCep').val(), '<?= $produto["produto"]["id"] ?>', $('#idVariacao').val(), 'PRODUTO')">
                                                    <i class="glyphicon glyphicon-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="icone-carregando" class="display-none">
                                            <i class="fa fa-spinner fa-pulse size-1-8"></i>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <div class="row aguarda-liberacao produto-disponivel">
                                    <div class="col-md-12 size-1-4" id="resultadoFrete"></div>
                                </div>
                                <br/>
                                <div id="area-parcelamento" class="aguarda-liberacao"></div>
                                <? if (isset($produto["formapagamento"])) { ?>
                                    <div class="row produto-disponivel">
                                        <div class="col-md-12">
                                            <span for="formaPagamento">Formas de Pagamento</span>
                                        </div>
                                    </div>
                                    <div class="row produto-disponivel">
                                        <div class="col-md-12">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <?
                                                foreach ($produto["formapagamento"] as $ind => $formaPagamento) {
                                                    if ($formaPagamento["classe"] != "BOLETO") {
                                                        ?>
                                                        <li role="presentation" >
                                                            <a href="#<?= $formaPagamento["classe"] ?>" aria-controls="<?= $formaPagamento["classe"] ?>" role="tab" data-toggle="tab">
                                                                <img src="<?= IMG_URL ?>/forma_pagamento/<?= strtolower($formaPagamento["classe"]) ?>-icon.png" height="15" title="<?= $formaPagamento["titulo"] ?>" alt="<?= $formaPagamento["titulo"] ?>"/>
                                                            </a>
                                                        </li>
                                                    <? } ?>
                                                <? } ?>
                                            </ul>
                                            <div class="tab-content">
                                                <?
                                                foreach ($produto["formapagamento"] as $ind => $formaPagamento) {
                                                    if ($formaPagamento["classe"] != "BOLETO") {
                                                        ?>
                                                        <div role="tabpanel" class="tab-pane" id="<?= $formaPagamento["classe"] ?>">
                                                            <table class="table table-bordered parcelamento size-1-4">
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="2" class="text-center">
                                                                            Estes valores são aproximados e podem sofrer alterações.
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <div class="col-md-6 text-center">
                                                                                <b>1x</b> <strong><?= $produto["produto"]["valorPor"] ?></strong> <b>sem juros</b>
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
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        <? } else { ?>
                            <div class="alert alert-danger text-center size-1-4" role="alert">
                                Favor efetue o login para poder visualizar nossos preços e dar sequencia a sua compra.
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>