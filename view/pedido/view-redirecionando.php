<section id="conteudo">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 quadro-produto-interno">
                <h1><?= $this->title ?></h1>
                <div class="alert alert-success text-center" role="alert">
                    <strong>Aguarde!</strong> Em breve você será redirecionado para o site de pagamento onde poderá finalizar a sua compra.<br/>
                    Caso isso não aconteça <a href="<?= $linkRedirecionamento ?>" target="_blank"><strong class="color-dark-green-2">clique aqui</strong></a><br/>
                    <i class="fa fa-spinner fa-pulse size-1-8"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Code for Vendas Conversion Page -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 1000888937;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "7LqpCLfBnwcQ6bSh3QM";
<? if (isset($valorAquisicao)) { ?>
        var google_conversion_value = <?= $valorAquisicao ?>;
        var google_conversion_currency = "BRL";
<? } ?>
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1000888937/?value=<?= $valorAquisicao ?>&amp;currency_code=BRL&amp;label=7LqpCLfBnwcQ6bSh3QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>