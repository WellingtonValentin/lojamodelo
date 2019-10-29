<?

/**
 * MainModel - Modelo geral
 *
 * 
 *
 * @package TutsupMVC
 */
class MainModel {

    /**
     * $form_data
     *
     * Os dados de formulário de envio.
     *
     * @access public
     */
    public $formData;

    /**
     * $formMsg
     *
     * Mensagens de feedback para o formulário.
     *
     * @access public
     */
    public $formMsg;

    /**
     * $formConfirma
     *
     * Mensagem de confirmação para apagar dados do formulário.
     *
     * @access public
     */
    public $formConfirma;

    /**
     * $db
     *
     * O objeto da nossa conexão.
     *
     * @access public
     */
    public $db;

    /**
     * $controller
     *
     * O controller que gerou esse modelo.
     *
     * @access public
     */
    public $controller;

    /**
     * $parametros
     *
     * Parâmetros da URL.
     *
     * @access public
     */
    public $parametros;

    /**
     * $form_data
     *
     * Os dados de formulário de envio.
     *
     * @access public
     */
    public $userdata;

    /**
     * $resultadoPorPagina
     * 
     * Receberá o número de resultados por página para configurar
     * a listagem e também para ser utilizada na paginação
     * 
     * @access public
     */
    public $resultadoPorPagina = 12;

    /**
     * $ordenacao
     * 
     * Receberá a ordenação dos resultados da consulta
     * 
     * @access public
     */
    public $ordenacao = "";

    /**
     * Construtor da classe
     * 
     * Configura o banco, controlador, os parâmetros e o dados do usuário
     * 
     * @access public
     * @param Objeto $db Objeto da nossa conexão
     * @param Objeto $controller Objeto do controlador
     */
    function __construct($db = false, $controller = null) {
        
        // Configura o banco e seleciona a tabela
        $this->db = new DB();

        // Configura o controlador
        $this->controller = $controller;

        // Configura os parâmetros
        $this->parametros = $this->controller->parametros;

        // Configura os dados do usuário
        $this->userdata = $this->controller->userdata;
    }


}
