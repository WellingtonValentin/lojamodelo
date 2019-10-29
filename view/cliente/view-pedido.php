<section id="pagina-interna">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3">
                <? require_once ABSPATH . '/view/cliente/view-menu.php'; ?>
            </div>
            <div class="col-md-9 borda-padrao">
                <h2>Meus Pedidos</h2>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="status" class="col-md-3 control-label size-1-4">Status: </label>
                                <div class="col-md-7 input-group">
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Selecione...</option>
                                        <?
                                        unset($statusPedido["CREDITADO"]);
                                        foreach ($statusPedido as $ind => $status) {
                                            ?>
                                            <option value="<?= $ind ?>" <?= (chkArray($_POST, "status") == $ind) ? "selected" : "" ?>><?= $status ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-left">
                            <div class="form-group">
                                <button type="submit" name="cadastrarCliente" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-search"></i>&nbsp;&nbsp;&nbsp;FILTRAR
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <br/>
                <div id="no-more-tables">
                    <table class="table col-md-12 padding-0 table-striped table-hover cf">
                        <thead>
                            <tr>
                                <th class="text-center size-1-4">
                                    <strong>ID</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Data</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Status</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Frete</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Valor</strong>
                                </th>
                                <th class="text-center size-1-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $total = 0;
                            foreach ((array) $listagemPedido as $pedido) {
                                $totalProduto = 0;
                                $andStatus = "";
                                $total += $totalProduto = ($pedido["valorTotal"] + $pedido["valorFrete"]) - $pedido["valorDesconto"];

                                $this->db->tabela = "pedido_status";
                                if (chkArray($_POST, "status")) {
                                    $andStatus = " AND status = '" . $_POST["status"] . "'";
                                }
                                $consulta = $this->db->consulta("WHERE pedidoFK = '" . $pedido["id"] . "'" . $andStatus, "ORDER BY data DESC");
                                $pedido_status = $this->db->fetch($consulta);
                                ?>
                                <tr>
                                    <td data-title="Código" class="text-center size-1-4">
                                        <?= $pedido["id"] ?>
                                    </td>
                                    <td data-title="Data" class="text-center size-1-4">
                                        <?= dataHoraSite($pedido["data"]) ?>
                                    </td>
                                    <td data-title="Status" class="text-center size-1-4">
                                        <?= $statusPedido[$pedido_status["0"]["status"]] ?>
                                    </td>
                                    <td data-title="Frete" class="text-center size-1-4 capitalize">
                                        <?= ($pedido["tipoFrete"] == "MOTOBOY") ? "Entrega Própria" : ucfirst($pedido["tipoFrete"]) ?>
                                    </td>
                                    <td data-title="Valor" class="text-center size-1-4">
                                        R$ <?= number_format($totalProduto, 2, ",", ".") ?>
                                    </td>
                                    <td data-title="Ver" class="text-center size-1-4">
                                        <a href="<?= HOME_URL ?>/cliente/detalhe-pedido/<?= cr($pedido["id"]) ?>/detalhes-do-pedido.htm#conteudo">
                                            <i class="glyphicon glyphicon-search color-padrao"></i>
                                        </a>
                                    </td>
                                </tr>
                            <? } ?>
                            <tr style="text-align: center; background-color: #ffffff;">
                                <td data-title="Total" colspan="6" class="text-right size-1-4">
                                    <strong class="hidden-xs">Total:</strong> R$ <?= number_format($total, 2, ",", ".") ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>