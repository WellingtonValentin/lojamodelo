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
     * Os dados de formul�rio de envio.
     *
     * @access public
     */
    public $formData;

    /**
     * $formMsg
     *
     * Mensagens de feedback para o formul�rio.
     *
     * @access public
     */
    public $formMsg;

    /**
     * $formConfirma
     *
     * Mensagem de confirma��o para apagar dados do formul�rio.
     *
     * @access public
     */
    public $formConfirma;

    /**
     * $db
     *
     * O objeto da nossa conex�o.
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
     * Par�metros da URL.
     *
     * @access public
     */
    public $parametros;

    /**
     * $form_data
     *
     * Os dados de formul�rio de envio.
     *
     * @access public
     */
    public $userdata;

    /**
     * $resultadoPorPagina
     * 
     * Receber� o n�mero de resultados por p�gina para configurar
     * a listagem e tamb�m para ser utilizada na pagina��o
     * 
     * @access public
     */
    public $resultadoPorPagina = 12;

    /**
     * $ordenacao
     * 
     * Receber� a ordena��o dos resultados da consulta
     * 
     * @access public
     */
    public $ordenacao = "";

    /**
     * Construtor da classe
     * 
     * Configura o banco, controlador, os par�metros e o dados do usu�rio
     * 
     * @access public
     * @param Objeto $db Objeto da nossa conex�o
     * @param Objeto $controller Objeto do controlador
     */
    function __construct($db = false, $controller = null) {
        
        // Configura o banco e seleciona a tabela
        $this->db = new DB();

        // Configura o controlador
        $this->controller = $controller;

        // Configura os par�metros
        $this->parametros = $this->controller->parametros;

        // Configura os dados do usu�rio
        $this->userdata = $this->controller->userdata;
    }


}
