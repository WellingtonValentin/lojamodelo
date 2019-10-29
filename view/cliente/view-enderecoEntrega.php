<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3">
                <? require_once ABSPATH . '/view/cliente/view-menu.php'; ?>
            </div>
            <div class="col-md-9 borda-padrao size-1-2">
                <h2>Cadastrar Endereço</h2>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cep" class="control-label">CEP: </label>
                                <input type="text" name="cep" value="<?= chkArray($enderecoCliente, "cep") ?>" class="form-control cep" onblur="preencheEndereco($(this).val())" id="cep" placeholder="CEP">
                            </div>
                        </div>
<!--                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="titulo" class="control-label">Título: </label>
                                <input type="text" name="titulo" value="<?= chkArray($enderecoCliente, "titulo") ?>" class="form-control" id="titulo" placeholder="Título">
                            </div>
                        </div>-->
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="destinatario" class="control-label">Nome do destinatário completo: </label>
                                <input type="text" name="destinatario" value="<?= chkArray($enderecoCliente, "destinatario") ?>" class="form-control" id="destinatario" placeholder="Destinatário">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="endereco" class="control-label">Endereço: </label>
                                <input type="text" name="endereco" value="<?= chkArray($enderecoCliente, "endereco") ?>" class="form-control" id="endereco" placeholder="Endereço">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="numero" class="control-label">Número: </label>
                                <input type="text" name="numero" value="<?= chkArray($enderecoCliente, "numero") ?>" class="form-control num6" id="numero" placeholder="Número">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="complemento" class="control-label">Complemento: </label>
                                <input type="text" name="complemento" value="<?= chkArray($enderecoCliente, "complemento") ?>" class="form-control" id="complemento" placeholder="Complemento">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="bairro" class="control-label">Bairro: </label>
                                <input type="text" name="bairro" value="<?= chkArray($enderecoCliente, "bairro") ?>" class="form-control" id="bairro" placeholder="Bairro">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="cidade" class="control-label">Cidade: </label>
                                <input type="text" name="cidade" value="<?= chkArray($enderecoCliente, "cidade") ?>" class="form-control" id="cidade" placeholder="Cidade">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estado" class="control-label">Estado: </label>
                                <select class="form-control" id="estado" name="estado">
                                    <? foreach ($estado as $ind => $estados) { ?>
                                        <option <?= (chkArray($enderecoCliente, "estado") == $estados["uf"]) ? "selected" : "" ?> value="<?= $estados["uf"] ?>"><?= $estados["uf"] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group text-right">
                                <button type="submit" name="cadastrarCliente" class="btn btn-success">
                                    <i class="glyphicon glyphicon-floppy-disk"></i>
                                    SALVAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <br/><br/><br/>
                <h2>Endereços de Entrega</h2>
                <div id="no-more-tables">
                    <table class="table col-md-12 padding-0  table-striped table-hover cf">
                        <thead>
                            <tr>
<!--                                <th class="text-left size-1-4">
                                    <strong>Título</strong>
                                </th>-->
                                <th class="text-left size-1-4">
                                    <strong>Endereço</strong>
                                </th>
                                <th class="text-lef size-1-4t">
                                    <strong>Cidade</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Estado</strong>
                                </th>
                                <th class="text-center">

                                </th>
                                <th class="text-center">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ((array) $listagemEndereco as $endereco) { ?>
                                <tr>
<!--                                    <td data-title="Título" class="text-left size-1-4">
                                        <?= $endereco["titulo"] ?>
                                    </td>-->
                                    <td data-title="Endereço" class="text-left size-1-4">
                                        <?= $endereco["endereco"] ?>, <?= $endereco["numero"] ?>, <?= $endereco["complemento"] ?>, <?= $endereco["bairro"] ?>
                                    </td>
                                    <td data-title="Cidade" class="text-left size-1-4">
                                        <?= $endereco["cidade"] ?>
                                    </td>
                                    <td data-title="Estado" class="text-center size-1-4">
                                        <?= $endereco["estado"] ?>
                                    </td>
                                    <td data-title="Editar" class="text-center size-1-4">
                                        <a href="<?= HOME_URL ?>/cliente/endereco-entrega/<?= cr($endereco["id"]) ?>/meus-enderecos.htm#conteudo" title="Alterar">
                                            <i class="glyphicon glyphicon-pencil color-padrao"></i>
                                        </a>
                                    </td>
                                    <td data-title="Excluir" class="text-center size-1-4">
                                        <a href="<?= HOME_URL ?>/cliente/endereco-entrega/apagar/<?= cr($endereco["id"]) ?>/meus-enderecos.htm#conteudo" title="Alterar">
                                            <i class="glyphicon glyphicon-trash color-padrao"></i>
                                        </a>
                                    </td>
                                </tr>
                            <? } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                        <? if (isset($_SESSION['PEDIDO']['CARRINHO'])) { ?>
                            <div class="col-md-4 col-xs-12 text-left botao-carrinho">
                                <a href="<?= HOME_URL ?>/carrinho/finalizar/finalizar-compra.html" class="btn btn-block btn-primary btn-success">FINALIZAR COMPRA <span class="glyphicon glyphicon-chevron-right"></span></a>
                            </div>
                        <? } ?>
                </div>
            </div>
        </div>
    </div>
</section>