<?php

/**
 * Função de auxilio para exibir alerta javascript
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
 * Função para redirecionamento javascript
 * 
 * @param type $url URL para redirecionar
 * @param type $target Define se será aberto na mesma página ou na próxima
 */
function redireciona($url, $target = "_top") {
    ?>
    <script language='javascript'>
        window.open('<?= $url ?>', '<?= $target ?>');
    </script>
    <?
}

/**
 * Função que retorna o print r de um array apenas se o usuário logado for @byteabyte.com.br
 */
function printr($array) {

    if (stripos($_SESSION['CLIENTE']['email'], "@byteabyte.com.br")) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
}
