<?

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerLinha extends MainController {

    /**
     * Carrega a página "/view/home/index.php"
     */
    public function index() {

        $urlRedirect = str_replace("/site", "", $_SERVER["REQUEST_URI"]);
        $urlRedirect = str_replace("/linha/", "/produto/categoria/", $urlRedirect);
        $urlRedirect = HOME_URL . $urlRedirect;

        header("Location: " . $urlRedirect);
    }

}
