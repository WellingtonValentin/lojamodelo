<footer>
    <section id="rodape-finalizacao">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <p class="color-gray normal size-1-2">
                        <strong>Importante!</strong><br/>
                        &bull; Para finalizar a sua compra primeiro selecione a forma de entregra desejada.<br/>
                        &bull; Uma vez selecionado irá surgir para você as formas de pagamento, revise bem seu pedido primeiro e depois selecione a forma de pagamento desejada.<br/>
                        &bull; Caso por algum motivo o frete de seu pedido não pode ser cálculado no ato da compra pode finalizar o pedido normalmente, pois ele será salvo e em breve calcularemos por você o frete e adicionaremos o valor ao pedido e então será liberado o pagamento de seu pedido.<br/>
                        <? if (isset($emailContato[0])) { ?>
                            &bull; Qualquer dúvida basta entrar em contato: <?= $emailContato[0]['email'] ?>
                        <? } ?>
                    </p>
                </div>
            </div>
        </div>
    </section>
</footer>