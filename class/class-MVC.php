<?

/**
 * Gerencia modelos, controladores e visualizações.
 * 
 * 
 */
class MVC {

    /**
     * Receberá o valor do controlador vindo da URL
     * http://www.exemplo.com/controlador/
     * 
     */
    private $controlador;

    /**
     * Receberá a açao a ser executada vindo da URL
     * http://www.exemplo.com/controlador/acao
     * 
     */
    private $acao;

    /**
     * Receberá um array de parâmetros vindo da URL
     * http://www.exemplo.com/controlador/acao/parametro1/parametro2/parametro3
     * 
     */
    private $parametros;

    /** 
     * Caminho da página não encontrada
     * 
     */
    private $notFound = "/include/404.php";

    /**
     * Obtém os valores do controlador, ação e parâmetros
     * acionando o controlador e a ação respectivas
     * 
     */
    function __construct() {

        // Obtém os valores do controlador, ação e parâmetro da URL
        $this->getURLData();

        /**
         * Verifica se o controlador existe. Caso contrário adiciona
         * o controlador padrão (controller/controller-home.php)
         * e chama o método index
         * 
         */
        if (!$this->controlador) {

            // Adiciona o controlador padrão
            require_once ABSPATH . '/controller/controller-home.php';

            // Cria o objeto do controlador "controller-home.php"
            // Este controlador deverá ter uma classe HomeController
            $this->controlador = new ControllerHome();

            // Executa o método index()
            $this->controlador->index();

            return;
        }

        // Se o arquivo do controlador não existir, não faremos nada
        if (!file_exists(ABSPATH . "/controller/" . $this->controlador . ".php")) {
            require_once ABSPATH . $this->notFound;
            return;
        }

        // Inclui o arquivo do controlador
        require_once ABSPATH . "/controller/" . $this->controlador . ".php";

        /**
         * Remove caracteres inválidos do nome do controlador para gerar 
         * o nome da classe. Ex: Se o arquivo chamar "controller-home.php"
         * a classe deverá se chamar ControllerHome
         * 
         */
        $this->controlador = preg_replace('/[^a-zA-Z]/i', '', $this->controlador);

        // Se a classe do controlador não existir não faremos nada
        if (!class_exists($this->controlador)) {
            require_once ABSPATH . $this->notFound;
            return;
        }

        // Cria o objeto da classe e envia os parâmetros
        $this->controlador = new $this->controlador($this->parametros);

        // Remoce os caracteres inválidos do nome da ação (método)
        $this->acao = preg_replace('/[^a-zA-Z]/i', '', $this->acao);

        // Se o método indicado existir execuda o método e envia os parâmetros
        if (method_exists($this->controlador, $this->acao)) {
            $this->controlador->{$this->acao}($this->parametros);
            return;
        }

        // Se não existir o método chamamos o método index
        if (method_exists($this->controlador, 'index')) {
            $this->controlador->index($this->parametros);
            return;
        }

        // Página não encontrada
        require_once ABSPATH . $this->notFound;
        return;
    }

    /**
     * Obtém parâmetros do $_GET["path"] e configura as propriedades
     * $this->controlador, $this->acao e $this->parametros
     * 
     * A URL deverá ter o seguinte formato:
     * http://www.exemplo.com/controlador/acao/parametro1/parametro2/parametro3
     * 
     */
    public function getURLData() {

        // Verifica se o parâmetro path
        if (isset($_GET["path"])) {

            $path = $_GET["path"];

            // Limpa os dados
            $path = rtrim($path, "/");
            $path = filter_var($path, FILTER_SANITIZE_URL);

            // Cria um array de parâmetros
            $path = explode("/", $path);

            // Configura as propriedades
            $this->controlador = chkArray($path, 0);
            $this->controlador = "controller-" . $this->controlador;
            $this->acao = chkArray($path, 1);

            // Configura os parâmetros
            if (chkArray($path, 2)) {
                unset($path[0]);
                unset($path[1]);

                $this->parametros = array_values($path);
            }

            // DEBUG
            //
            // echo $this->controlador . '<br>';
            // echo $this->acao        . '<br>';
            // echo '<pre>';
            // print_r($this->parametros);
            // echo '</pre>';
        }
    }
}