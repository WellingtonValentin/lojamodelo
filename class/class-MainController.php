<?

/**
 *  Todos os controladores deverão estender a essa classe
 * 
 */
class MainController extends Login {

    // Conexão com o banco de dados
    public $db;
    // Classe phpass
    public $phpass;
    // Título da página
    public $title;
    // Se a página precisa de login
    public $login_required = FALSE;
    // Parametros
    public $parametros = array();

    function __construct($parametros = array()) {
        // Instancia o Banco de Dados
        $this->db = new DB();

        // PHPass
        $this->phpass = new PasswordHash(8, FALSE);

        // Parâmetros
        $this->parametros = $parametros;

        // Verifica o login (class-Login.php)
        $this->VerificaLogin();
    }

    // Carrega os modelos presentes na pasta /models/
    public function loadModel($modelName = FALSE) {
        // Verifica se foi enviado arquivo
        if (!$modelName) {
            return;
        }

        // Garante que o nome do modelo tenha letras minÃºsculas
        $modelName = strtolower($modelName);

        // Define o caminho do arquivo
        $modelPath = ABSPATH . "/model/" . $modelName . ".php";

        // Verifica se existe o arquivo
        if (file_exists($modelPath)) {
            
            // Inclui o arquivo
            require_once $modelPath;

            // Remove os caminhos do arquivo (se tiver algum)
            $modelName = explode('/', $modelName);

            // Pega sÃ³ o nome final do caminho
            $modelName = end($modelName);

            // Remove caracteres invÃ¡lidos do nome do arquivo
            $modelName = preg_replace('/[^a-zA-Z0-9]/is', '', $modelName);

            // Verifica se a classe existe
            if (class_exists($modelName)) {

                // Retorna um objeto da classe
                return new $modelName($this->db, $this);
            }
        }

        return;
    }

}
