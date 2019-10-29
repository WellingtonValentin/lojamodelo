<?

/**
 * Controlador da página principal e controlador
 * padrão para quando não encontrar algum método
 * 
 */
class ControllerHome extends MainController {

    /**
     * Carrega a página "/view/home/index.php"
     */
    public function index($retorno = "") {
        // Titulo da página
        $this->title = "Home";

        $modelo = $this->loadModel("produto/model-produto");
        $modelo->parametros = array(0 => 1);
        if (isset($_SESSION["FILTRAR"]["LIMITE"])) {
            $modelo->resultadoPorPagina = $_SESSION["FILTRAR"]["LIMITE"];
        } else {
            $modelo->resultadoPorPagina = 15;
        }
        $whereComplemento = "";
        if (isset($_SESSION['FILTRO'])) {
            if (isset($_SESSION['FILTRO']['CATEGORIA'])) {
                $whereComplemento .= $_SESSION['FILTRO']['CATEGORIA']['WHERE'];
            }
            if (isset($_SESSION['FILTRO']['PRECO'])) {
                $whereComplemento .= $_SESSION['FILTRO']['PRECO']['WHERE'];
            }
        }
        $resultadoProd = $modelo->listarProdutos("WHERE destaque = 'S' AND id NOT IN (SELECT produtoFK FROM produto_combinacao WHERE estoque <= 0)" . $whereComplemento);
        $resultadoProdPromo = $modelo->listarProdutos("WHERE promocao = 'S'");
        $caminho = HOME_URL . "/produto/home/";

        $this->db->tabela = "categoria";
        $this->db->limit = 9999;
        $consulta = $this->db->consulta("WHERE topo = 'S'");
        while ($cat = mysql_fetch_assoc($consulta)) {
            $resultadoProdCat[] = array(
                "titulo" => $cat["titulo"],
                "produtos" => $modelo->listarProdutos("WHERE destaque = 'S' AND id IN (SELECT produtoFK FROM produto_categoria WHERE categoriaFK = '" . $cat["id"] . "')")
            );
        }
        
        $this->db->tabela = "banner_secundario";
        $consultaBannerSec = $this->db->consulta("WHERE local = 'TOPO-2' AND status = 'A'");

        // Carrega os arquivos da view
        require ABSPATH . "/view/_include/header.php";
        $mostraBanner = TRUE;
        require ABSPATH . "/view/home/view-home.php";
        require ABSPATH . "/view/_include/footer.php";
    }

    public function newsletter() {

        $modelo = $this->loadModel("atendimento/model-atendimento");
        $modelo->db->tabela = "newsletter";
        $modelo->parametros = $_POST;
        $retorno = $modelo->cadastrarNewsletter();

        $this->index($retorno);
    }

    public function indicarSite() {

        require_once ABSPATH . '/controller/controller-email.php';

        $controladoEmail = new ControllerEmail();
        $controladoEmail->parametros[0] = "indicar_site";
        $controladoEmail->parametros[1] = $produtoFK;
        $controladoEmail->parametros["POST"]["nome"] = strip_tags($_POST['nome']);
        $controladoEmail->parametros["POST"]["email"] = strip_tags($_POST['email']);
        $controladoEmail->parametros["POST"]["nomeAmigo"] = strip_tags($_POST['nomeAmigo']);
        $controladoEmail->parametros["POST"]["emailAmigo"] = strip_tags($_POST['emailAmigo']);
        $controladoEmail->index();

        echo "ENVIADO";
    }

}
