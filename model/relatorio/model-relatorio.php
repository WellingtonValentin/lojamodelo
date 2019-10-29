<?

/**
 * Modelo para gerenciar os relatorios
 * 
 */
class ModelRelatorio extends MainModel {

    /**
     * Tabela principal que ser� utilizada no relat�rio
     * 
     * @access public
     */
    public $tabela = "";

    /**
     * $ordenacao
     * 
     * Receber� a ordena��o dos resultados da consulta
     * 
     * @access public
     */
    public $ordenacao = "";

    /**
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    /**
     * Insere linha ao relat�rio
     * 
     * @return array Array com resultado do cadastro e mensagem para retorno
     */
    public function inserir() {
        
    }
        
    public function lista() {
        
    }
    
    public function busca() {
        
    }
    
    public function imprimir() {
        
    }

}
