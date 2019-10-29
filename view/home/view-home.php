<section id="home">
    <div class="container">
        <?
        if ($mostraBanner) {
            require_once ABSPATH . '/view/_include/banner.php';
            $this->db->tabela = "banner_secundario";
            $this->db->limit = 4;
            $consulta = $this->db->consulta();
            if (mysql_num_rows($consulta)) {
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <? while ($linha = mysql_fetch_assoc($consulta)) { ?>
                            <div class="quadro-banner-secundario">
                                <img src="<?= UPLOAD_URL ?>/bannerSecundario/<?= $linha['nomeFoto'] ?>" onload="resizeToRatioUncut(this, $('#home .quadro-banner-secundario').width(), 70, true, true)"/>
                            </div>
                        <? } ?>
                    </div>
                </div>
            <? } ?>
        <? } ?>
        <br/><br/>
        <div class="row">
            <div class="col-xs-3">
                <? require_once ABSPATH . '/view/home/view-menu.php'; ?>
            </div>
            <div class="col-xs-9">
                <? if (mysql_num_rows($consultaCatTop)) { ?>
                    <div class="row margin-0">
                        <div class="col-xs-12 padding-0 source bold size-1-4 color-gray">Sugestões</div>
                    </div>
                    <div class="row margin-0">
                        <? $cont = 0; ?>
                        <? while ($catTop = mysql_fetch_assoc($consultaCatTop)) { ?>
                            <?
                            $cont++;
                            if ($cont > 4) {
                                break;
                            }
                            $this->db->tabela = "produto";
                            $this->db->limit = 999999;
                            $totalProd = mysql_num_rows($this->db->consulta("WHERE id IN (SELECT produtoFK FROM produto_categoria WHERE categoriaFK = '" . $catTop['id'] . "')"));
                            ?>
                            <div class="col-xs-3 padding-0">
                                <a href="<?= HOME_URL ?>/produto/categoria/<?= $catTop['id'] ?>/<?= arrumaString($catTop['titulo']) ?>.html" class="source color-padrao">
                                    <strong class="size-2-2"><?= $catTop['titulo'] ?></strong> <span class="size-1-4">(<?= $totalProd ?>)</span>
                                </a>
                            </div>
                        <? } ?>
                    </div>
                    <hr/>
                <? } ?>
                <div class="row margin-0">
                    <div class="col-xs-12 padding-0">
                        <span class="source normal size-1-4 color-light-gray">(<?= count($resultadoProd) - 1 ?> produto<?= (count($resultadoProd) - 1 > 1) ? "s" : "" ?>)</span>
                        <form class="form-inline pull-right">
                            <div class="form-group">
                                <label for="mostrar" class="source normal size-1-4 color-light-gray">Mostrar:&nbsp;&nbsp;</label>
                                <select class="form-control" id="mostrar" onchange="mudarFiltro('limite', $(this).val())">
                                    <option value="15" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "LIMITE")) ? ($_SESSION["FILTRAR"]["LIMITE"] == "15") ? "selected" : "" : "" : "" ?>>15 por página</option>
                                    <option value="21" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "LIMITE")) ? ($_SESSION["FILTRAR"]["LIMITE"] == "21") ? "selected" : "" : "" : "" ?>>21 por página</option>
                                    <option value="27" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "LIMITE")) ? ($_SESSION["FILTRAR"]["LIMITE"] == "27") ? "selected" : "" : "" : "" ?>>27 por página</option>
                                    <option value="36" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "LIMITE")) ? ($_SESSION["FILTRAR"]["LIMITE"] == "36") ? "selected" : "" : "" : "" ?>>36 por página</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ordenacao" class="source normal size-1-4 color-light-gray">&nbsp;&nbsp;&nbsp;&nbsp;Ordenar:&nbsp;&nbsp;</label>
                                <select class="form-control" id="ordenacao" onchange="mudarFiltro('ordem', $(this).val())">
                                    <option value="novos" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "ORDEM")) ? ($_SESSION["FILTRAR"]["ORDEM"] == "novos") ? "selected" : "" : "" : "" ?>>Novos Produtos</option>
                                    <option value="maisVendidos" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "ORDEM")) ? ($_SESSION["FILTRAR"]["ORDEM"] == "maisVendidos") ? "selected" : "" : "" : "" ?>>Mais Vendidos</option>
                                    <option value="menorPreco" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "ORDEM")) ? ($_SESSION["FILTRAR"]["ORDEM"] == "menorPreco") ? "selected" : "" : "" : "" ?>>Menor Preço</option>
                                    <option value="maiorPreco" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "ORDEM")) ? ($_SESSION["FILTRAR"]["ORDEM"] == "maiorPreco") ? "selected" : "" : "" : "" ?>>Maior Preço</option>
                                    <option value="AZ" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "ORDEM")) ? ($_SESSION["FILTRAR"]["ORDEM"] == "AZ") ? "selected" : "" : "" : "" ?>>Alfabética A-Z</option>
                                    <option value="ZA" <?= (chkArray($_SESSION, "FILTRAR")) ? (chkArray($_SESSION["FILTRAR"], "ORDEM")) ? ($_SESSION["FILTRAR"]["ORDEM"] == "ZA") ? "selected" : "" : "" : "" ?>>Alfabética Z-A</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <br/><br/>
                <div class="row">
                    <? if (isset($resultadoProd)) { ?>
                        <? foreach ($resultadoProd as $ind => $prod) { ?>
                            <? if (is_numeric($ind)) { ?>
                                <a href="<?= HOME_URL ?>/produto/detalhes/<?= $prod["id"] ?>/<?= arrumaString($prod["titulo"]) ?>.html">
                                    <div class="col-xs-4">
                                        <div class="quadro-produto">
                                            <div class="quadro-imagem">
                                                <img src="<?= $prod['imagem'] ?>" title="<?= $prod['titulo'] ?>" alt="<?= $prod['titulo'] ?>" onload="resizeToRatioUncut(this, $('#home .quadro-produto .quadro-imagem').width(), 230, true, true)"/>
                                            </div><br/>
                                            <div class="quadro-valores-produto">
                                                <div class="titulo-prod source semi-bold size-1-8 color-dark-gray">
                                                    <?= quebrarTexto($prod["titulo"], 60) ?>
                                                </div><br/><br/>
                                                <? if (chkArray($prod, "textoDE") && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="source normal size-1-4 color-gray linha-sobre">
                                                        <?= $prod["textoDE"] ?>
                                                    </span><br/>
                                                <? } ?>
                                                <? if (chkArray($prod, "textoPOR") && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="source normal size-1-4 color-gray">
                                                        a partir de: 
                                                    </span>
                                                    <span class="source bold size-2-2 color-padrao">
                                                        <?= $prod["textoPOR"] ?>
                                                    </span><br/>
                                                <? } ?>
                                                <? if (chkArray($prod, "textoParcelamento") && chkArray($configValores, "mostrarValoresLogado") != "S") { ?>
                                                    <span class="source normal size-1-4 color-gray">
                                                        <?= $prod["textoParcelamento"] ?>
                                                    </span><br/>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <? if (($ind + 1) >= 9) { ?>
                                    <? if ((($ind + 1) % 3 == 0) && ($bannerSec = @mysql_fetch_assoc($consultaBannerSec))) { ?>
                                        <div class="col-xs-12">
                                            <a href="<?= $bannerSec['link'] ?>" target="_blank">
                                                <img src="<?= UPLOAD_URL ?>/bannerSecundario/<?= $bannerSec['nomeFoto'] ?>" width="100%"/>
                                            </a>
                                            <br/><br/>
                                        </div>
                                    <? } ?>
                                <? } elseif (($ind + 1) % 6 == 0) { ?>
                                    <div class="col-xs-12">
                                        <div class="row quadro-newsletter">
                                            <div class="col-xs-7 vcenter hidden-xs">
                                                <div class="col-xs-4">
                                                    <i class="fa fa-envelope-o color-white size-8"></i>
                                                </div>
                                                <div class="col-xs-8 padding-0 oswald normal size-2-6 color-white">
                                                    RECEBA NOVIDADES E PROMOÇÕES EXCLUSIVAS
                                                </div>
                                            </div>
                                            <div class="col-xs-5">
                                                <form action="<?= HOME_URL ?>/home/newsletter/assinar-newsletter.html#newsletter" method="POST">
                                                    <div class="input-group">
                                                        <input type="text" name="email" class="form-control" placeholder="Digite seu e-mail">
                                                        <span class="input-group-btn size-1-6">
                                                            <button class="btn btn-default source bold color-white" type="submit">
                                                                RECEBER
                                                            </button>
                                                        </span>
                                                    </div>
                                                </form>
                                            </div>
                                            <? if (isset($retorno)) { ?>
                                                <? if (chkArray($retorno, "newsletter")) { ?>
                                                    <div class="col-xs-12">
                                                        <div class="alert size-1-4 <?= (chkArray($retorno["newsletter"], "erro")) ? "alert-danger" : "alert-success" ?> text-center margin-0" role="alert">
                                                            <?= $retorno["newsletter"]["msg"] ?>
                                                        </div>
                                                    </div>
                                                <? } ?>
                                            <? } ?>
                                        </div>
                                    </div>
                                <? } ?>
                            <? } ?>
                        <? } ?>
                        <? if (chkArray($resultadoProd, "paginacao")) { ?>
                            <div class="row margin-0">
                                <div class="col-xs-12 padding-0 text-center">
                                    <nav>
                                        <ul class="pagination">
                                            <li <?= ($resultadoProd["paginacao"]["atual"] == 1) ? "class=\"disabled\"" : "" ?>>
                                                <? if (($resultadoProd["paginacao"]["atual"] != 1)) { ?>
                                                    <a href="<?= $caminho . ($resultadoProd["paginacao"]["atual"] - 1) . "/" . (chkArray($resultadoProd, "termo") ? ($resultadoProd["termo"] . "/") : "") . "produtos.html" ?>" aria-label="Previous">
                                                    <? } ?>
                                                    <span aria-hidden="true">
                                                        <i class="glyphicon glyphicon-step-backward"></i>
                                                    </span>
                                                    <? if (($resultadoProd["paginacao"]["atual"] != 1)) { ?>
                                                    </a>
                                                <? } ?>
                                            </li>
                                            <? if ($resultadoProd["paginacao"]["atual"] > 4) { ?>
                                                <li>
                                                    <a href="<?= $caminho . 1 . "/" . (chkArray($resultadoProd, "termo") ? ($resultadoProd["termo"] . "/") : "") . "produtos.html" ?>" aria-label="Primeira">
                                                        <span aria-hidden="true">
                                                            <i class="glyphicon glyphicon-backward"></i>
                                                        </span>
                                                    </a>
                                                </li>
                                                <?
                                            }
                                            for ($i = ($resultadoProd["paginacao"]["atual"] - $resultadoProd["paginacao"]["offset1"]); $i < $resultadoProd["paginacao"]["atual"]; $i++) {
                                                if ($i > 0) {
                                                    ?>
                                                    <li <?= ($resultadoProd["paginacao"]["atual"] == $i) ? "class=\"active\"" : "" ?>>
                                                        <a href="<?= $caminho . $i . "/" . (chkArray($resultadoProd, "termo") ? ($resultadoProd["termo"] . "/") : "") . "produtos.html" ?>" aria-label="Pagina<?= $i ?>">
                                                            <span aria-hidden="true"><?= $i ?></span>
                                                        </a>
                                                    </li>
                                                    <?
                                                    $resultadoProd["paginacao"]["offset2"] --;
                                                }
                                            }
                                            for (; $i < $resultadoProd["paginacao"]["atual"] + $resultadoProd["paginacao"]["offset2"]; $i++) {
                                                if ($i <= $resultadoProd["paginacao"]["ultima"]) {
                                                    ?>
                                                    <li <?= ($resultadoProd["paginacao"]["atual"] == $i) ? "class=\"active\"" : "" ?>>
                                                        <a href="<?= $caminho . $i . "/" . (chkArray($resultadoProd, "termo") ? ($resultadoProd["termo"] . "/") : "") . "produtos.html" ?>" aria-label="Pagina<?= $i ?>">
                                                            <span aria-hidden="true"><?= $i ?></span>
                                                        </a>
                                                    </li>
                                                    <?
                                                }
                                            }
                                            if ($resultadoProd["paginacao"]["atual"] <= ($resultadoProd["paginacao"]["ultima"] - $resultadoProd["paginacao"]["offset1"])) {
                                                ?>
                                                <li>
                                                    <a href="<?= $caminho . $resultadoProd["paginacao"]["ultima"] . "/" . (chkArray($resultadoProd, "termo") ? ($resultadoProd["termo"] . "/") : "") . "produtos.html" ?>" aria-label="Ultima">
                                                        <span aria-hidden="true">
                                                            <i class="glyphicon glyphicon-forward"></i>
                                                        </span>
                                                    </a>
                                                </li>
                                            <? } ?>
                                            <li <?= ($resultadoProd["paginacao"]["atual"] == $resultadoProd["paginacao"]["ultima"]) ? "class=\"disabled\"" : "" ?>>
                                                <? if (($resultadoProd["paginacao"]["atual"] != $resultadoProd["paginacao"]["ultima"])) { ?>
                                                    <a href="<?= $caminho . ($resultadoProd["paginacao"]["atual"] + 1) . "/" . (chkArray($resultadoProd, "termo") ? ($resultadoProd["termo"] . "/") : "") . "produtos.html" ?>" aria-label="Next">
                                                    <? } ?>
                                                    <span aria-hidden="true">
                                                        <i class="glyphicon glyphicon-step-forward"></i>
                                                    </span>
                                                    <? if (($resultadoProd["paginacao"]["atual"] != $resultadoProd["paginacao"]["ultima"])) { ?>
                                                    </a>
                                                <? } ?>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        <? } ?>
                    <? } else { ?>
                        <div class="alert alert-danger text-center" role="alert">
                            Nenhum produto encontrado !
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</section>