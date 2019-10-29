<?

/**
 * Funзгo para carregar automaticamente todas as classes
 * As classes devem estar na pasta class/ e comeзarem com class-
 */
function __autoload($class) {
    $file = ABSPATH . "/class/class-" . $class . ".php";

    if (!file_exists($file)) {
        require_once ABSPATH . "/include/404.php";
        return;
    }

    require_once $file;
}