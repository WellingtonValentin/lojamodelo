<section id="menu-lateral">
    <span class="source bold size-2-2 color-dark-blue">Filtrar esta Lista</span><br/>
    <? if (isset($_SESSION['FILTRO'])) { ?>
        <i class="glyphicon glyphicon-remove-sign color-padrao size-1-4 cursor-pointer" onclick="$.get('<?= HOME_URL ?>/produto/ajaxLimpaFiltro', function () {
                    location.href = '<?= HOME_URL ?>';
                })"></i> <span class="source normal size-1-4 color-black cursor-pointer" onclick="$.get('<?= HOME_URL ?>/produto/ajaxLimpaFiltro', function () {
                            location.href = '<?= HOME_URL ?>';
                        })">Limpar tudo</span>
       <? } ?>
    <hr/>
    <? if (isset($_SESSION['FILTRO']['CATEGORIA']['OPT'])) { ?>
        <?
        $this->db->tabela = "categoria";
        $this->db->limit = 999999;
        $cat = $this->db->consultaId($_SESSION['FILTRO']['CATEGORIA']['OPT']);
        $consultaSub = $this->db->consulta("WHERE categoriaFK = '" . $_SESSION['FILTRO']['CATEGORIA']['OPT'] . "'");
        ?>
        <span class="source bold size-1-6 color-padrao text-uppercase"><?= $cat['titulo'] ?></span>
        <br/>
        <nav>
            <ul>
                <? while ($linhaSub = mysql_fetch_assoc($consultaSub)) { ?>
                    <li onclick="marcaFiltro('cat', '<?= $linhaSub['id'] ?>')">
                        <a class="source semi-bold size-1-5 color-light-gray">
                            <div id="check-cat-<?= $linhaSub['id'] ?>" class="quadro-check text-center">
                                <? if (isset($_SESSION['FILTRO']['CATEGORIA']['OPT'])) { ?>
                                    <? if ($_SESSION['FILTRO']['CATEGORIA']['OPT'] == $linhaSub['id']) { ?>
                                        <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                                    <? } ?>
                                <? } ?>
                            </div>
                            <?= $linhaSub['titulo'] ?>
                        </a>
                    </li>
                <? } ?>
            </ul>
        </nav>
        <hr/>
        <br/>
    <? } ?>
    <span class="source bold size-1-6 color-padrao">CATEGORIA/USO</span>
    <br/>
    <?
    $this->db->tabela = "categoria";
    $this->db->limit = 999999;
    $consulta = $this->db->consulta("WHERE categoriaFK IS NULL AND nivel = '1'");
    ?>
    <nav>
        <ul>
            <? while ($linha = mysql_fetch_assoc($consulta)) { ?>
                <li onclick="marcaFiltro('cat', '<?= $linha['id'] ?>')">
                    <a class="source semi-bold size-1-5 color-light-gray">
                        <div id="check-cat-<?= $linha['id'] ?>" class="quadro-check text-center">
                            <? if (isset($_SESSION['FILTRO']['CATEGORIA']['OPT'])) { ?>
                                <? if ($_SESSION['FILTRO']['CATEGORIA']['OPT'] == $linha['id']) { ?>
                                    <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                                <? } ?>
                            <? } ?>
                        </div>
                        <?= $linha['titulo'] ?>
                    </a>
                </li>
            <? } ?>
        </ul>
    </nav>
    <hr/>
    <span class="source bold size-1-6 color-padrao">PREÇO</span>
    <br/>
    <nav>
        <ul>
            <li onclick="marcaFiltro('prec', '1')">
                <a class="source semi-bold size-1-5 color-light-gray">
                    <div id="check-prec-1" class="quadro-check text-center">
                        <? if (isset($_SESSION['FILTRO']['PRECO']['OPT'])) { ?>
                            <? if ($_SESSION['FILTRO']['PRECO']['OPT'] == 1) { ?>
                                <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                            <? } ?>
                        <? } ?>
                    </div>
                    Até R$ 99,99
                </a>
            </li>
            <li onclick="marcaFiltro('prec', '2')">
                <a class="source semi-bold size-1-5 color-light-gray">
                    <div id="check-prec-2" class="quadro-check text-center">
                        <? if (isset($_SESSION['FILTRO']['PRECO']['OPT'])) { ?>
                            <? if ($_SESSION['FILTRO']['PRECO']['OPT'] == 2) { ?>
                                <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                            <? } ?>
                        <? } ?>
                    </div>
                    R$ 100,00 - 299,99
                </a>
            </li>
            <li onclick="marcaFiltro('prec', '3')">
                <a class="source semi-bold size-1-5 color-light-gray">
                    <div id="check-prec-3" class="quadro-check text-center">
                        <? if (isset($_SESSION['FILTRO']['PRECO']['OPT'])) { ?>
                            <? if ($_SESSION['FILTRO']['PRECO']['OPT'] == 3) { ?>
                                <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                            <? } ?>
                        <? } ?>
                    </div>
                    R$ 300,00 - 499,99
                </a>
            </li>
            <li onclick="marcaFiltro('prec', '4')">
                <a class="source semi-bold size-1-5 color-light-gray">
                    <div id="check-prec-4" class="quadro-check text-center">
                        <? if (isset($_SESSION['FILTRO']['PRECO']['OPT'])) { ?>
                            <? if ($_SESSION['FILTRO']['PRECO']['OPT'] == 4) { ?>
                                <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                            <? } ?>
                        <? } ?>
                    </div>
                    R$ 500,00 - 999,99
                </a>
            </li>
            <li onclick="marcaFiltro('prec', '5')">
                <a class="source semi-bold size-1-5 color-light-gray">
                    <div id="check-prec-5" class="quadro-check text-center">
                        <? if (isset($_SESSION['FILTRO']['PRECO']['OPT'])) { ?>
                            <? if ($_SESSION['FILTRO']['PRECO']['OPT'] == 5) { ?>
                                <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                            <? } ?>
                        <? } ?>
                    </div>
                    R$ 1.000,00 - 1.499,99
                </a>
            </li>
            <li onclick="marcaFiltro('prec', '6')">
                <a class="source semi-bold size-1-5 color-light-gray">
                    <div id="check-prec-6" class="quadro-check text-center">
                        <? if (isset($_SESSION['FILTRO']['PRECO']['OPT'])) { ?>
                            <? if ($_SESSION['FILTRO']['PRECO']['OPT'] == 6) { ?>
                                <i class="glyphicon glyphicon-ok size-0-8 color-light-blue"></i>
                            <? } ?>
                        <? } ?>
                    </div>
                    Acima de R$ 1.500,00
                </a>
            </li>
        </ul>
    </nav>
</section>