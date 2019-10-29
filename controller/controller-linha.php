<?

/**
 * Controlador da p�gina principal e controlador
 * padr�o para quando n�o encontrar algum m�todo
 * 
 */
class ControllerLinha extends MainController {

    /**
     * Carrega a p�gina "/view/home/index.php"
     */
    public function index() {

        $urlRedirect = str_replace("/site", "", $_SERVER["REQUEST_URI"]);
        $urlRedirect = str_replace("/linha/", "/produto/categoria/", $urlRedirect);
        $urlRedirect = HOME_URL . $urlRedirect;

        header("Location: " . $urlRedirect);
    }

}
