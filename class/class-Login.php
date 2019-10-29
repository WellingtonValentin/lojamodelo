<?

/**
 * Manipula os dados de usu�rios.
 * 
 * Faz login e logout e redireciona p�gina para usu�rios logados
 * 
 */
class Login {

    // Informa se o usu�rio esta logado, verdadeiro se estiver.
    public $logado;
    // Dados de Usu�rio
    public $userdata;
    // Mensagem de erro para formul�rio de login
    public $login_error;
    

    function __construct() {
        // Configura o banco e seleciona a tabela
        $this->db = new DB();
    }

    /**
     * Verifica o login
     * Configura as propriedades $logado e $login_error.
     * Configura tamb�m o array do usu�rio em $userdata
     * 
     */
    public function VerificaLogin() {

        // Verifica se existe uma sess�o de cliente logado
        if (isset($_SESSION["CLIENTE"]) && !empty($_SESSION["CLIENTE"]) && is_array($_SESSION["CLIENTE"]) && !isset($_POST["CLIENTE"])) {

            // Configura os dados do usu�rio
            $userdata = $_SESSION["CLIENTE"];

            // Garante que n�o � HTTP POST
            $userdata["post"] = FALSE;
        }

        // Verifica se existe um $_POST com a chave userdata
        // Tem que ser um array
        if (isset($_POST['CLIENTE']) && !empty($_POST['CLIENTE']) && is_array($_POST['CLIENTE'])) {
            // Configura os dados do usuário
            $userdata = $_POST['CLIENTE'];

            // Garante que é HTTP POST
            $userdata['post'] = true;
        }

        // Verifica se existe algum dado de usu�rio para conferir
        if (!isset($userdata) || !is_array($userdata)) {
            
            // Desconfigura qualquer sess�o que possa existir
            $this->logout();
            return;
        }

        // Passa os dados do post para uma vari�vel
        if ($userdata["post"] === true) {
            $post = true;
        } else {
            $post = false;
        }

        // Remove a chave post do array userdata
        unset($userdata["post"]);

        // Verifica se existe algo para conferir
        if (empty($userdata)) {
            $this->logado = false;
            $this->login_error = null;

            // Desconfigura qualquer sess�o que possa existir
            $this->logout();

            return;
        }

        // Extrai vari�veis dos dados do usu�rio
        extract($userdata);

        if (!isset($email) || !isset($senha)) {
            $this->logado = false;
            $this->login_error = "Ambos os campos s�o obrigat�rios!";

            // Desconfigura qualquer sess�o que possa existir
            $this->logout();

            return;
        }

        // Verificia se o usu�rio existe na base de dados
        $this->db->tabela = "cliente";
        $consulta = $this->db->consulta("WHERE email = '$email' AND senha = '$senha'");

        if (!mysql_num_rows($consulta)) {

            $this->logado = false;
            $this->login_error = "Login ou Senha Incorreto!";

            // Desconfigura qualquer sess�o que possa existir
            $this->logout();

            return;
        }

        // Obt�m os dados da base de usu�rio
        $campo = mysql_fetch_assoc($consulta);

        // Obt�m o ID do Cliente
        $clienteId = (int) $campo["id"];

        // Verifica se o ID existe
        if (empty($clienteId)) {
            $this->logado = false;
            $this->login_error = "Cliente n�o existe!";

            // Desconfigura qualquer sess�o que possa existir
            $this->logout();

            return;
        }

        // Se for uma sess�o verifica se a sess�o bate sess�o do banco
        if (session_id() != $campo["session_id"] && !$post) {
            $this->logado = false;
            $this->login_error = "ID da sess�o errado!";

            // Desconfigura qualquer sess�o que possa existir
            $this->logout();

            return;
        }

        // Se for um post
        if ($post) {
            // Recria o ID da sess�o
            ob_start();
            session_regenerate_id();
            $sessionId = session_id();

            // Envia os dados de usu�rio para a sess�o
            $_SESSION["CLIENTE"] = $campo;

            // Atualiza o ID da sess�o
            $_SESSION["CLIENTE"]["session_id"] = $sessionId;

            // Atualiza o ID da sess�o na base de dados
            $arrayUpdate["session_id"] = $sessionId;
            $this->db->importArray($arrayUpdate);
            $this->db->persist($clienteId);
        }

        // Configura a propriedade dizendo que o usu�rio esta logado
        $this->logado = TRUE;

        // Configura os dados do usu�rio para $this->userdata
        $this->userdata = $_SESSION["CLIENTE"];

        // Verifica se existe uma URL para redirecionar o usu�rio
        if (isset($_SESSION["REDIRECT_URL"])) {
            // Passa a URL para uma vari�vel
            $redirectURL = urldecode($_SESSION["REDIRECT_URL"]);

            // Remove a sess�o com a URL
            unset($_SESSION["REDIRECT_URL"]);

            // Redireciona a p�gina
            ?>
            <meta http-equiv="Refresh" content="0; url=<?= $redirectURL ?>">
            <script>window.location.href = "<?= $redirectURL ?>";</script>
            <?
        } 
    }

    /**
     * Desconfigura tudo do usu�rio
     * 
     */
    protected function logout($redirecionamento = "") {
        
        // Removendo todas as informa��es de $_SESSION['CLIENTE']
        $_SESSION["CLIENTE"] = array();

        // Removendo a sess�o somente para ter certeza
        unset($_SESSION["CLIENTE"]);
        
        $_SESSION["PEDIDO"]["FRETE"] = array();
        $_SESSION["PEDIDO"]["CUPOM"] = array();
        $_SESSION["PEDIDO"]["ENDERECO"] = array();
        unset($_SESSION["PEDIDO"]["FRETE"]);
        unset($_SESSION["PEDIDO"]["CUPOM"]);
        unset($_SESSION["PEDIDO"]["ENDERECO"]);

        // Gera novamente a ID da sess�o
        session_regenerate_id();

        if ($redirecionamento) {
            // Envia o usu�rio para a p�gina de login
            $this->gotoPage($redirecionamento);
        }
    }

    /**
     * Redireciona para a p�gina de login
     * 
     */
    protected function gotoLogin() {
        // Verifica se a URL da Home est� configurada
        if (defined("HOME_URL")) {
            // Configura a URL de Login
            $loginURL = HOME_URL . "/cliente/login/login.html";

            // A p�gina em que o usu�rio estava
            $_SESSION["REDIRECT_URL"] = urlencode($_SERVER["REQUEST_URI"]);

            // Redirecionamento
            ?>
            <meta http-equiv="Refresh" content="0; url=<?= $loginURL ?>">
            <script>window.location.href = "<?= $loginURL ?>";</script>
            <?
        }

        return;
    }

    /**
     * Envia para uma p�gina qualquer
     * 
     */
    final protected function gotoPage($pageURL = null) {
        if (isset($_GET["url"]) && !empty($_GET["url"]) && $pageURL) {
            // Configura a URL
            $pageURL = urlencode($_GET["url"]);
        }

        if ($pageURL) {
            // Redireciona
            ?>
            <meta http-equiv="Refresh" content="0; url=<?= $pageURL ?>">
            <script>window.location.href = "<?= $pageURL ?>";</script>
            <?
            return;
        }
    }

}
