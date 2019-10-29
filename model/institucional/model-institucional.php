<?

/**
 * Modelo para gerenciar os institucionais
 * 
 */
class ModelInstitucional extends MainModel {

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
     * Instancia o construtor da classe pai
     * 
     * @param type $db
     * @param type $controller
     */
    public function __construct($db = false, $controller = null) {
        parent::__construct($db, $controller);
    }

    /**
     * Lista os institucionais
     * 
     * @access public
     * @return array Resultado da query
     */
    public function listarInstitucionais() {

        // Configura as varia��es
        $id = $where = $order = $limit = null;

        // Verifica se um par�metro foi enviado para carregar um institucional
        if (is_numeric(chkArray($this->parametros, 0))) {

            // Configura o ID para enviar para a consulta
            $id = chkArray($this->parametros, 0);

            // Configura o where da consulta
            $where = "WHERE id = '$id'";
        }

        // Configura a p�gina a ser exibida
        $pagina = !empty($this->parametros[1]) ? $this->parametros[1] : 1;

        // Como a p�gina��o se inicia do 0 diminui uma p�gina da vari�vel
        $pagina--;

        // Configura o n�mero de resultados por p�gina
        $resultadoPorPagina = $this->resultadoPorPagina;

        // O offset dos resultados da consulta
        $offset = $pagina * $resultadoPorPagina;

        // Configura o limite da consulta
        $limit = "LIMIT $offset, $resultadoPorPagina";

        // Verifica se foi informado uma ordem, caso contrario ordena por id descrecente
        if (!isset($this->ordenacao)) {
            $order = "ORDER BY id DESC";
        } else {
            $order = $this->ordenacao;
        }

        // Faz a consulta
        $this->db->tabela = "texto";
        $query = $this->db->consulta($where, $order, $limit);

        return $this->db->fetchAll($query);
    }

    /**
     * Pagina��o
     * 
     * @access public
     */
    public function paginacao() {

        // Verifica se o primeiro par�metro da url n�o � um num�rico
        // caso seja n�o � necess�ria pagina��o
        if (is_numeric(chkArray($this->parametros, 0))) {
            return;
        }

        // Obt�m o total de resultados da base de dados
        $this->db->tabela = "texto";
        $query = $this->db->consulta("", "", "", "", "", "COUNT(*) as total");
        $total = $this->db->fetch($query);
        $total = $total["total"];

        // Configura o caminho para a pagina��o
        $caminho = HOME_URL . "/institucional/index/pagina/";

        // Resultados por p�gina
        $resultadoPorPagina = $this->resultadoPorPagina;

        // Obt�m a �ltima p�gina poss�vel
        $ultima = ceil($total / $resultadoPorPagina);

        // Configura a primeira p�gina
        $primeira = 1;

        // Offisets utilizados como limites para exibir os n�meros das
        // p�ginas deixando a p�gina atual no centro
        $offset1 = 3;
        $offset2 = 6;

        // P�gina atual
        $atual = ($this->parametros[1]) ? $this->parametros[1] : 1;

        // Exibe o link para a primeira p�gina 
        if ($atual > 4) {
            ?>
            <li>
                <a href="<?= $caminho . $primeira ?>" aria-label="Primeira">
                    <span aria-hidden="true">
                        <i class="fa fa-angle-double-left"></i>
                    </span>
                </a>
            </li
            <?
        }

        // Primeiro loop toma conta da parte esquerda dos n�meros
        for ($i = ($atual - $offset1); $i < $atual; $i++) {
            if ($i > 0) {
                ?>
                <li>
                    <a href="<?= $caminho . $i ?>" aria-label="Pagina<?= $i ?>">
                        <span aria-hidden="true"><?= $i ?></span>
                    </a>
                </li
                <?
                $offset2--;
            }
        }
        
        // Segundo loop toma conta da parte direita dos n�meros
        for (; $i < $atual + $offset2; $i++) {
            if ($i <= $ultima) {
                ?>
                <li>
                    <a href="<?= $caminho . $i ?>" aria-label="Pagina<?= $i ?>">
                        <span aria-hidden="true"><?= $i ?></span>
                    </a>
                </li
                <?
            }
        }
        
        // Exibe o link para a �ltima p�gina 
        if ($atual <= ($ultima - $offset1)) {
            ?>
            <li>
                <a href="<?= $caminho . $ultima ?>" aria-label="Ultima">
                    <span aria-hidden="true">
                        <i class="fa fa-angle-double-right"></i>
                    </span>
                </a>
            </li
            <?
        }
    }

}
