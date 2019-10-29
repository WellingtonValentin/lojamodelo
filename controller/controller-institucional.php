<?

/**
 * Controlador da p�gina principal e controlador
 * padr�o para quando n�o encontrar algum m�todo
 * 
 */
class ControllerInstitucional extends MainController {

    /**
     * Carrega a p�gina "/view/home/index.php"
     */
    public function index($retorno = "") {
        
        $modelo = $this->loadModel("institucional/model-institucional");
        $texto = $modelo->listarInstitucionais();
        $modelo->db->tabela = "texto_grupo";
        $consultaGrupo = $modelo->db->consulta("","ORDER BY titulo ASC");
        $modelo->db->tabela = "texto";
        
        
        // Titulo da p�gina
        $this->title = $texto["0"]["titulo"];

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        require ABSPATH . "/view/institucional/view-institucional.php";
        require ABSPATH . "/view/_include/footer.php";
    }

}
