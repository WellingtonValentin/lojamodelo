<table style="width: 580px; background-color: #dff0d8; border: solid 1px #d6e9c6; margin: 10px; margin-bottom: 20px; border-radius: 4px;">
    <tr>
        <td style="color: #3c763d; font-family: 'Roboto'; padding: 15px; font-size: 14px">
            <?= chkArray($resultado, "texto") ?>
        </td>
    </tr>
</table>
<?php if (chkArray($this->parametros, 0) == "atualizacao_status") { ?>
    <table style="width: 580px; border: solid 1px #ddd; margin: 10px; margin-bottom: 20px;">
        <tr>
            <td style="color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd;">
                <h4 style="text-align: center; font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';"><strong>Alteração Realizada</strong></h4>
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; color: #333; font-family: 'Roboto'; font-weight: normal; font-size: 14px;">
                <strong>Status atual: </strong><?= $statusPedido[$resultado["status"]["titulo"]] ?><br/>
                <strong>Data da alteração: </strong><?= $resultado["status"]["data"] ?> 
            </td>
        </tr>
    </table>
<?php } ?>
<?php if (isset($resultado["listaProduto"])) { ?>
    <table style="width: 580px; border: solid 1px #ddd; margin: 10px; margin-bottom: 20px;">
        <tr>
            <td colspan="5" style="color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd;">
                <h4 style="text-align: center; font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';"><strong>Dados do Pedido</strong></h4>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding: 8px; color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                <span style="font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';">
                    CÓDIGO
                </span>
            </td>
            <td style="text-align: center; padding: 8px; color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                <span style="font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';">
                    PRODUTO
                </span>
            </td>
            <td style="text-align: center; padding: 8px; color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                <span style="font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';">
                    QUANTIDADE
                </span>
            </td>
            <td style="text-align: center; padding: 8px; color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                <span style="font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';">
                    VALOR
                </span>
            </td>
            <td style="text-align: center; padding: 8px; color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                <span style="font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';">
                    TOTAL
                </span>
            </td>
        </tr>
        <?php
        $totalCarrinho = 0;
        foreach ($resultado["listaProduto"] as $ind => $produtoPedido) {
            ?>
            <tr>
                <td style="text-align: center; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;"><?= $produtoPedido["id"] ?></td>
                <td style="text-align: center; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                    <?= $produtoPedido["titulo"] ?>
                </td>
                <td style="text-align: center; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;"><?= $produtoPedido["quantidade"] ?></td>
                <td style="text-align: center; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">
                    
                    R$ <?= $produtoPedido["valor"] ?>
                </td>
                <td style="text-align: center; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                    R$ <?= number_format($produtoPedido["total"], 2, ",", ".") ?>
                </td>
            </tr>
            <?php
            $totalCarrinho += $produtoPedido["total"];
            $totalFinal = $totalCarrinho;
        }
        ?>
        <tr>
            <td colspan="5" style="padding: 8px;"></td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: right; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                <strong>Total do Carrinho:</strong> R$ <?= number_format($totalCarrinho, 2, ",", ".") ?>
            </td>
        </tr>
        <?php
        if (isset($resultado["fretePedido"])) {
            $totalFinal += $resultado["fretePedido"];
            ?>
            <tr>
                <td colspan="5" style="text-align: right; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                    <strong>Frete:</strong> R$ <?= number_format($resultado["fretePedido"], 2, ",", ".") ?>
                </td>
            </tr>
        <?php } ?>
        <?php
        if (isset($resultado["descontoPedido"])) {
            $totalFinal -= $resultado["descontoPedido"];
            ?>
            <tr>
                <td colspan="5" style="text-align: right; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                    <strong>Desconto:</strong> R$ <?= number_format($resultado["descontoPedido"], 2, ",", ".") ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="5" style="text-align: right; padding: 8px; font-family: 'Roboto'; font-size: 18px; color: #bf0303; border-bottom: 1px solid #ddd;">
                <strong>Total:</strong> R$ <?= number_format($totalFinal, 2, ",", ".") ?>
            </td>
        </tr>
    </table>
<?php } ?>
<?php if (isset($resultado["endereco"])) { ?>
    <table style="width: 580px; border: solid 1px #ddd; margin: 10px; margin-bottom: 20px;">
        <tr>
            <td style="color: #333; background-color: #f5f5f5; border-bottom: 1px solid #ddd;">
                <h4 style="text-align: center; font-size: 15px; font-weight: bold; color: #333; font-family: 'Roboto';"><strong>Endereço de Entrega</strong></h4>
            </td>
        </tr>
        <tr>
            <td style="padding: 15px; color: #333; font-family: 'Roboto'; font-weight: normal; font-size: 14px;">
                <strong>Destinatário: </strong><?= chkArray($resultado["endereco"], "destinatario") ?><br/>
                <strong>Endereço: </strong><?= chkArray($resultado["endereco"], "endereco") ?>, <?= chkArray($resultado["endereco"], "numero") ?>, <?= chkArray($resultado["endereco"], "complemento") ?>, <?= chkArray($resultado["endereco"], "bairro") ?><br/>
                <strong>Cidade: </strong><?= chkArray($resultado["endereco"], "cidade") ?> - <?= chkArray($resultado["endereco"], "estado") ?>, <strong>CEP: </strong><?= chkArray($resultado["endereco"], "cep") ?><br/><br/>
                <strong>Entrega: </strong><?= $resultado["formaEntrega"] ?>, <strong>Prazo: </strong><?= ($resultado["prazoEstimado"]) ? $resultado["prazoEstimado"] . " Dias Úteis" : "a calcular" ?>
            </td>
        </tr>
    </table>
<?php } ?>
<?php if (isset($resultado["produtos"])) { ?>
    <table style="width: 580px; border: solid 1px #ddd; margin: 10px; margin-bottom: 20px;">
        <tr>
            <td colspan="2" style="padding: 8px;">
                <h1 style="font-family: 'Roboto'; font-weight: 400; padding-bottom: 10px; font-size: 20px; line-height: 20px; letter-spacing: 1px; color: #666; border-bottom: solid 1px #c9c9c9;"><?= chkArray($resultado["produtos"], "titulo") ?></h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px;">
                <img src="<?= chkArray($resultado["produtos"], "foto") ?>" width="280" style="border: none"/>
            </td>
            <td style="padding: 8px; text-align: center;">
                <span style="font-size: 12px; text-decoration: line-through; font-weight: 300; font-family: 'Roboto'; color: #666;">de: <?= chkArray($resultado["produtos"], "valorDe") ?></span><br/>
                <span style="font-size: 18px; font-weight: bold; font-family: 'Roboto'; color: #bf0303;">Por: <?= chkArray($resultado["produtos"], "valorPor") ?></span><br/><br/><br/>
                <a href="<?= HOME_URL . "/produto/detalhes/" . $this->parametros[1] . "/eletronico-em-promocao.html" ?>" target="_blank">
                    <div style="color: #fff; font-size: 18px; padding: 8px; text-align: center; background-color: #c9302c; border-color: #ac2925;">
                        VEJA O PRODUTO NO SITE
                    </div>
                </a>
            </td>
        </tr>
    </table>
<?php } ?>
<?php if (chkArray($this->parametros, 0) == "contato" || $this->assunto == "Contato") { ?>
    <table style="width: 580px; border: solid 1px #ddd; margin: 10px; margin-bottom: 20px;">
        <tr>
            <td style="text-align: center; font-size: 14px; font-family: 'Roboto'; padding: 8px; color: #fff; background-color: #C01C1D;  border-color: #C01C1D;">
                Contato
            </td>
        </tr>
        <tr>
            <td style="text-align: left; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                <strong>Nome:</strong> <?= chkArray($_POST, "nome") ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                <strong>E-Mail:</strong> <?= chkArray($_POST, "email") ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                <strong>Telefone:</strong> <?= chkArray($_POST, "telefone1") ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; padding: 8px; font-family: 'Roboto'; font-size: 14px; color: #333; border-bottom: 1px solid #ddd;">
                <strong>Mensagem:</strong> <?= chkArray($_POST, "texto") ?>
            </td>
        </tr>
    </table>
<?php } ?>