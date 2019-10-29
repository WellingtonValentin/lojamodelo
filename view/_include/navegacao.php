<?php if (!defined('ABSPATH')) exit; ?>
<ol class="breadcrumb hidden-xs">
    <li>
        <a href="<?= HOME_URL ?>" class="raleway color-padrao size-1-4 light">
            Home
        </a>
    </li>
    <? if (isset($paginas)) { ?>
        <? foreach ((array) $paginas as $ind => $local) { ?>
            <li>
                <a href="<?= HOME_URL ?><?= $local["link"] ?>" class="raleway color-padrao size-1-4 light"><?= $local["titulo"] ?></a>
            </li>
        <? } ?>
    <? } ?>
    <li class="active raleway color-gray size-1-4 light">
        <?= $this->title ?>
    </li>
</ol>