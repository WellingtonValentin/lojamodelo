<tr>
    <td data-title="Cupom" colspan="5" class="alert alert-success">
        <div class="row">
            <div class="col-xs-12 size-1-4">
                <div class="col-md-9 hidden-xs text-left">
                    <strong><i class="fa fa-ticket"></i></strong> Se você possui um cupom de desconto informe o código.
                </div>
                <div class="col-md-3 col-xs-12 padding-0">
                    <div id="area-cupom" class="input-group grupo-cupom">
                        <input type="text" id="codigo-cupom" class="form-control codigo-cupom" placeholder="Cupom">
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="button" onclick="validarCupom($('#codigo-cupom').val())">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="area-resposta-cupom display-none">
            <br/>
            <div class="alert alert-danger alert-dismissible size-1-4" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                Cupom de desconto não encontrado ou vencido.
            </div>
        </div>
    </td>
    <td id="valor-cupom-carrinho" colspan="2" valign="middle" class="hidden-xs faixa-valores-carrinho size-1-4 text-right"></td>
</tr>