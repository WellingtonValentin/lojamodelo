<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <div class="list-group">
                    <?
                    while ($grupo = mysql_fetch_assoc($consultaGrupo)) {
                        $consultaTexto = $modelo->db->consulta("WHERE grupoFK = '" . $grupo["id"] . "'", "ORDER BY titulo ASC");
                        ?>
                        <a class="list-group-item active background-padrao borda-padrao roboto size-1-2 semi-bold color-contraste">
                            <?= $grupo["titulo"] ?>
                        </a>
                        <?
                        while ($textoGrupo = mysql_fetch_assoc($consultaTexto)) {
                            ?>
                            <a href="<?= HOME_URL ?>/institucional/index/<?= $textoGrupo["id"] ?>/<?= arrumaString($textoGrupo["titulo"]) ?>.html" class="list-group-item roboto size-1-2 normal color-dark-gray">
                                <?= $textoGrupo["titulo"] ?>
                            </a>
                            <?
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-9 col-sm-8 col-xs-12 padding-smal corpo-texto">
                <div class="panel panel-default">
                    <div class="panel-heading size-1-8"><?= $texto["0"]["titulo"] ?></div>
                    <div class="panel-body size-1-4">
                    <?= $texto["0"]["texto"] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>