<?
$this->db->tabela = "banner_principal";
$consulta = $this->db->consulta();
if (mysql_num_rows($consulta)) {
    ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="owl-banner">
                <? while ($banner = mysql_fetch_assoc($consulta)) { ?>
                    <a <?= ($banner['link'] != "") ? "href='" . $banner['link'] . "' target='_blank'" : "" ?>>
                        <div class="item">
                            <img src="<?= UPLOAD_URL ?>/bannerPrincipal/<?= $banner['nomeFoto'] ?>" width="100%"/>
                        </div>
                    </a>
                <? } ?>
            </div>
        </div>
    </div>
<? } ?>