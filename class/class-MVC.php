<?

/**
 * Gerencia modelos, controladores e visualiza��es.
 * 
 * 
 */
class MVC {

    /**
     * Receber� o valor do controlador vindo da URL
     * http://www.exemplo.com/controlador/
     * 
     */
    private $controlador;

    /**
     * Receber� a a�ao a ser executada vindo da URL
     * http://www.exemplo.com/controlador/acao
     * 
     */
    private $acao;

    /**
     * Receber� um array de par�metros vindo da URL
     * http://www.exemplo.com/controlador/acao/parametro1/parametro2/parametro3
     * 
     */
    private $parametros;

    /** 
     * Caminho da p�gina n�o encontrada
     * 
     */
    private $notFound = "/include/404.php";

    /**
     * Obt�m os valores do controlador, a��o e par�metros
     * acionando o controlador e a a��o respectivas
     * 
     */
    function __construct() {

        // Obt�m os valores do controlador, a��o e par�metro da URL
        $this->getURLData();

        /**
         * Verifica se o controlador existe. Caso contr�rio adiciona
         * o controlador padr�o (controller/controller-home.php)
         * e chama o m�todo index
         * 
         */
        if (!$this->controlador) {

            // Adiciona o controlador padr�o
            require_once ABSPATH . '/controller/controller-home.php';

            // Cria o objeto do controlador "controller-home.php"
            // Este controlador dever� ter uma classe HomeController
            $this->controlador = new ControllerHome();

            // Executa o m�todo index()
            $this->controlador->index();

            return;
        }

        // Se o arquivo do controlador n�o existir, n�o faremos nada
        if (!file_exists(ABSPATH . "/controller/" . $this->controlador . ".php")) {
            require_once ABSPATH . $this->notFound;
            return;
        }

        // Inclui o arquivo do controlador
        require_once ABSPATH . "/controller/" . $this->controlador . ".php";

        /**
         * Remove caracteres inv�lidos do nome do controlador para gerar 
         * o nome da classe. Ex: Se o arquivo chamar "controller-home.php"
         * a classe dever� se chamar ControllerHome
         * 
         */
        $this->controlador = preg_replace('/[^a-zA-Z]/i', '', $this->controlador);

        // Se a classe do controlador n�o existir n�o faremos nada
        if (!class_exists($this->controlador)) {
            require_once ABSPATH . $this->notFound;
            return;
        }

        // Cria o objeto da classe e envia os par�metros
        $this->controlador = new $this->controlador($this->parametros);

        // Remoce os caracteres inv�lidos do nome da a��o (m�todo)
        $this->acao = preg_replace('/[^a-zA-Z]/i', '', $this->acao);

        // Se o m�todo indicado existir execuda o m�todo e envia os par�metros
        if (method_exists($this->controlador, $this->acao)) {
            $this->controlador->{$this->acao}($this->parametros);
            return;
        }

        // Se n�o existir o m�todo chamamos o m�todo index
        if (method_exists($this->controlador, 'index')) {
            $this->controlador->index($this->parametros);
            return;
        }

        // P�gina n�o encontrada
        require_once ABSPATH . $this->notFound;
        return;
    }

    /**
     * Obt�m par�metros do $_GET["path"] e configura as propriedades
     * $this->controlador, $this->acao e $this->parametros
     * 
     * A URL dever� ter o seguinte formato:
     * http://www.exemplo.com/controlador/acao/parametro1/parametro2/parametro3
     * 
     */
    public function getURLData() {

        // Verifica se o par�metro path
        if (isset($_GET["path"])) {

            $path = $_GET["path"];

            // Limpa os dados
            $path = rtrim($path, "/");
            $path = filter_var($path, FILTER_SANITIZE_URL);

            // Cria um array de par�metros
            $path = explode("/", $path);

            // Configura as propriedades
            $this->controlador = chkArray($path, 0);
            $this->controlador = "controller-" . $this->controlador;
            $this->acao = chkArray($path, 1);

            // Configura os par�metros
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