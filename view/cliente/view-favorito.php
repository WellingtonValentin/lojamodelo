<section id="conteudo">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <? require_once ABSPATH . '/view/_include/navegacao.php'; ?>
            </div>
            <div class="col-md-3">
                <? require_once ABSPATH . '/view/cliente/view-menu.php'; ?>
            </div>
            <div class="col-md-9 borda-padrao">
                <h2>Meus Favoritos</h2>
                <div id="no-more-tables">
                    <table class="table col-md-12 padding-0  table-striped table-hover cf">
                        <thead>
                            <tr>
                                <th class="text-center size-1-4">
                                    <strong>Foto</strong>
                                </th>
                                <th class="text-left size-1-4">
                                    <strong>Título</strong>
                                </th>
                                <th class="text-center size-1-4">
                                    <strong>Valor</strong>
                                </th> 
                                <th class="text-left" colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            foreach ((array) $listagemFavorito as $favorito) {
                                $modeloProduto = $this->loadModel("produto/model-produto");
                                $imagem = $modeloProduto->imagemPrincipal($favorito["produtoFK"]);
                                $valor = $modeloProduto->valoresProduto($favorito["produtoFK"]);
                                if (isset($valor["0"]["maxValor"])) {
                                    $valorFinal = "R$ " . number_format($valor["0"]["maxValor"], 2, ",", ".");
                                } else {
                                    $valorFinal = "R$ " . number_format($valor["0"]["valorPor"], 2, ",", ".");
                                }

                                $this->db->tabela = "produto";
                                $produto = $this->db->consultaId($favorito["produtoFK"]);
                                ?>
                                <tr>
                                    <td data-title="Foto" class="text-center">
                                        <div class="thumb-produto">
                                            <img src="<?= $imagem ?>" alt="<?= $produto["titulo"] ?>" title="<?= $produto["titulo"] ?>" onload="resizeToRatioUncut(this, 135, 90, true, true)" class="img-rounded">
                                        </div>
                                        <small>Adicionado em: <?= dataSite($favorito["data"]) ?></small>
                                    </td>
                                    <td data-title="Título" class="text-left size-1-4">
                                        <?= $produto["titulo"] ?>
                                    </td>
                                    <td data-title="Valor" class="text-center size-1-4">
                                        <?= $valorFinal ?>
                                    </td>
                                    <td data-title="Ver" class="text-center size-1-4">
                                        <a href="<?= HOME_URL ?>/produto/detalhes/<?= $favorito["produtoFK"] ?>/<?= arrumaString($produto["titulo"]) ?>.html">
                                            <i class="glyphicon glyphicon-shopping-cart" title="Adicionar ao Carrinho"></i>
                                        </a>
                                    </td>
                                    <td data-title="Excluir" class="text-center size-1-4">
                                        <a href="<?= HOME_URL ?>/cliente/favoritos/apagar/<?= cr($favorito["id"]) ?>/favoritos.htm#conteudo">
                                            <i class="glyphicon glyphicon-trash" title="Apagar o Produto"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>