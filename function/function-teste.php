<?php

/**
 * Fun��o de auxilio para exibir alerta javascript
 * 
 * @param type $msg Mensagem para o alerta
 */
function alerta($msg) {
    ?>
    <script type="text/javascript">
        alert("<?= $msg ?>")
    </script>
    <?
}

/**
 * Fun��o para redirecionamento javascript
 * 
 * @param type $url URL para redirecionar
 * @param type $target Define se ser� aberto na mesma p�gina ou na pr�xima
 */
function redireciona($url, $target = "_top") {
    ?>
    <script language='javascript'>
        window.open('<?= $url ?>', '<?= $target ?>');
    </script>
    <?
}

/**
 * Fun��o que retorna o print r de um array apenas se o usu�rio logado for @byteabyte.com.br
 */
function printr($array) {

    if (stripos($_SESSION['CLIENTE']['email'], "@byteabyte.com.br")) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
}
