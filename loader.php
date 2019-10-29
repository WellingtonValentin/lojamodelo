<?

// Evita que usuсrio acesse este arquivo diretamente
if (!defined("ABSPATH")) {
    exit;
}

// Inicia a sessуo
session_start();

// Verifica o modo para debugar
if (!defined("DEBUG") || DEBUG === false) {
    // Esconde todos os erros
    error_reporting(0);
    ini_set("display_errors", 0);
} else {
    // Mostra todos os erros
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

// Funчѕes globais
require_once ABSPATH . '/function/function-autoLoad.php';
require_once ABSPATH . '/function/function-data.php';
require_once ABSPATH . "/function/function-global.php";
require_once ABSPATH . "/function/function-criptografia.php";
require_once ABSPATH . "/function/function-manipulaString.php";
require_once ABSPATH . '/function/function-teste.php';

// Carrega a aplicaчуo
$mvc = new MVC();
