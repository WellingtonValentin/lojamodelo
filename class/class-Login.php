<?

/**
 * Manipula os dados de usuários.
 * 
 * Faz login e logout e redireciona página para usuários logados
 * 
 */
class Login {

    // Informa se o usuário esta logado, verdadeiro se estiver.
    public $logado;
    // Dados de Usuário
    public $userdata;
    // Mensagem de erro para formulário de login
    public $login_error;
    

    function __construct() {
        // Configura o banco e seleciona a tabela
        $this->db = new DB();
    }

    /**
     * Verifica o login
     * Configura as propriedades $logado e $login_error.
     * Configura também o array do usuário em $userdata
     * 
     */
    public function VerificaLogin() {

        // Verifica se existe uma sessão de cliente logado
        if (isset($_SESSION["CLIENTE"]) && !empty($_SESSION["CLIENTE"]) && is_array($_SESSION["CLIENTE"]) && !isset($_POST["CLIENTE"])) {

            // Configura os dados do usuário
            $userdata = $_SESSION["CLIENTE"];

            // Garante que não é HTTP POST
            $userdata["post"] = FALSE;
        }

        // Verifica se existe um $_POST com a chave userdata
        // Tem que ser um array
        if (isset($_POST['CLIENTE']) && !empty($_POST['CLIENTE']) && is_array($_POST['CLIENTE'])) {
            // Configura os dados do usuÃ¡rio
            $userdata = $_POST['CLIENTE'];

            // Garante que Ã© HTTP POST
            $userdata['post'] = true;
        }

        // Verifica se existe algum dado de usuário para conferir
        if (!isset($userdata) || !is_array($userdata)) {
            
            // Desconfigura qualquer sessão que possa existir
            $this->logout();
            return;
        }

        // Passa os dados do post para uma variável
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

            // Desconfigura qualquer sessão que possa existir
            $this->logout();

            return;
        }

        // Extrai variáveis dos dados do usuário
        extract($userdata);

        if (!isset($email) || !isset($senha)) {
            $this->logado = false;
            $this->login_error = "Ambos os campos são obrigatórios!";

            // Desconfigura qualquer sessão que possa existir
            $this->logout();

            return;
        }

        // Verificia se o usuário existe na base de dados
        $this->db->tabela = "cliente";
        $consulta = $this->db->consulta("WHERE email = '$email' AND senha = '$senha'");

        if (!mysql_num_rows($consulta)) {

            $this->logado = false;
            $this->login_error = "Login ou Senha Incorreto!";

            // Desconfigura qualquer sessão que possa existir
            $this->logout();

            return;
        }

        // Obtém os dados da base de usuário
        $campo = mysql_fetch_assoc($consulta);

        // Obtém o ID do Cliente
        $clienteId = (int) $campo["id"];

        // Verifica se o ID existe
        if (empty($clienteId)) {
            $this->logado = false;
            $this->login_error = "Cliente não existe!";

            // Desconfigura qualquer sessão que possa existir
            $this->logout();

            return;
        }

        // Se for uma sessão verifica se a sessão bate sessão do banco
        if (session_id() != $campo["session_id"] && !$post) {
            $this->logado = false;
            $this->login_error = "ID da sessão errado!";

            // Desconfigura qualquer sessão que possa existir
            $this->logout();

            return;
        }

        // Se for um post
        if ($post) {
            // Recria o ID da sessão
            ob_start();
            session_regenerate_id();
            $sessionId = session_id();

            // Envia os dados de usuário para a sessão
            $_SESSION["CLIENTE"] = $campo;

            // Atualiza o ID da sessão
            $_SESSION["CLIENTE"]["session_id"] = $sessionId;

            // Atualiza o ID da sessão na base de dados
            $arrayUpdate["session_id"] = $sessionId;
            $this->db->importArray($arrayUpdate);
            $this->db->persist($clienteId);
        }

        // Configura a propriedade dizendo que o usuário esta logado
        $this->logado = TRUE;

        // Configura os dados do usuário para $this->userdata
        $this->userdata = $_SESSION["CLIENTE"];

        // Verifica se existe uma URL para redirecionar o usuário
        if (isset($_SESSION["REDIRECT_URL"])) {
            // Passa a URL para uma variável
            $redirectURL = urldecode($_SESSION["REDIRECT_URL"]);

            // Remove a sessão com a URL
            unset($_SESSION["REDIRECT_URL"]);

            // Redireciona a página
            ?>
            <meta http-equiv="Refresh" content="0; url=<?= $redirectURL ?>">
            <script>window.location.href = "<?= $redirectURL ?>";</script>
            <?
        } 
    }

    /**
     * Desconfigura tudo do usuário
     * 
     */
    protected function logout($redirecionamento = "") {
        
        // Removendo todas as informações de $_SESSION['CLIENTE']
        $_SESSION["CLIENTE"] = array();

        // Removendo a sessão somente para ter certeza
        unset($_SESSION["CLIENTE"]);
        
        $_SESSION["PEDIDO"]["FRETE"] = array();
        $_SESSION["PEDIDO"]["CUPOM"] = array();
        $_SESSION["PEDIDO"]["ENDERECO"] = array();
        unset($_SESSION["PEDIDO"]["FRETE"]);
        unset($_SESSION["PEDIDO"]["CUPOM"]);
        unset($_SESSION["PEDIDO"]["ENDERECO"]);

        // Gera novamente a ID da sessão
        session_regenerate_id();

        if ($redirecionamento) {
            // Envia o usuário para a página de login
            $this->gotoPage($redirecionamento);
        }
    }

    /**
     * Redireciona para a página de login
     * 
     */
    protected function gotoLogin() {
        // Verifica se a URL da Home está configurada
        if (defined("HOME_URL")) {
            // Configura a URL de Login
            $loginURL = HOME_URL . "/cliente/login/login.html";

            // A página em que o usuário estava
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
     * Envia para uma página qualquer
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
