<table style="width: 600px; background: #DBDCDD;">
    <tr>
        <td valign="top" style="padding: 10px; font-family: 'Roboto'; font-size: 14px; color: #333;">
            <strong><?= $empresa["titulo"] ?>.</strong><br>
            <?= $empresa["endereco"] ?>, <?= $empresa["numero"] ?><br/>
            <?= $empresa["complemento"] ?>, <?= $empresa["bairro"] ?><br>
            <?= $empresa["cidade"] ?> - <?= $empresa["uf"] ?><br>
            CEP: <?= $empresa["cep"] ?><br/><br/>
            <strong>E-mail</strong><br>
            <? foreach ($emailContato as $ind => $email) { ?>
                <strong><?= $email["titulo"] ?>: </strong><a href="mailto:<?= $email["email"] ?>"><?= $email["email"] ?></a><br/>
            <? } ?>
        </td>
        <td valign="top" style="padding: 10px; font-family: 'Roboto'; font-size: 14px; color: #333;">
            <strong>Telefone</strong><br>
            <? foreach ($telefone as $ind => $fone) { ?>
                <strong><?= $fone["titulo"] ?>: </strong><?= $fone["telefone"] ?><br/>
            <? } ?>
            <br/>
            <strong>Redes Sociais</strong><br>
            <?
            $col = round(12 / count($redeSocial));
            foreach ($redeSocial as $ind => $rede) {
                ?>
                <a href="<?= $rede["link"] ?>" target="_blank" style="text-decoration: none; border: none">
                    <img style="margin: 5px;" src="<?= IMG_URL ?>/rede_social/<?= strtolower($ind) ?>.png" alt="<?= $rede["titulo"] ?>" title="<?= $rede["titulo"] ?>" style="border: none"/>
                </a>
            <? } ?>
        </td>
    </tr>
</table>
</td>
</tr>
</table>