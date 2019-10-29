<?

/**
 * Modelo para gerenciar os institucionais
 * 
 */
class ModelInstitucional extends MainModel {

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

        // Configura as variações
        $id = $where = $order = $limit = null;

        // Verifica se um parâmetro foi enviado para carregar um institucional
        if (is_numeric(chkArray($this->parametros, 0))) {

            // Configura o ID para enviar para a consulta
            $id = chkArray($this->parametros, 0);

            // Configura o where da consulta
            $where = "WHERE id = '$id'";
        }

        // Configura a página a ser exibida
        $pagina = !empty($this->parametros[1]) ? $this->parametros[1] : 1;

        // Como a páginação se inicia do 0 diminui uma página da variável
        $pagina--;

        // Configura o número de resultados por página
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
     * Paginação
     * 
     * @access public
     */
    public function paginacao() {

        // Verifica se o primeiro parâmetro da url não é um numérico
        // caso seja não é necessária paginação
        if (is_numeric(chkArray($this->parametros, 0))) {
            return;
        }

        // Obtém o total de resultados da base de dados
        $this->db->tabela = "texto";
        $query = $this->db->consulta("", "", "", "", "", "COUNT(*) as total");
        $total = $this->db->fetch($query);
        $total = $total["total"];

        // Configura o caminho para a paginação
        $caminho = HOME_URL . "/institucional/index/pagina/";

        // Resultados por página
        $resultadoPorPagina = $this->resultadoPorPagina;

        // Obtém a última página possível
        $ultima = ceil($total / $resultadoPorPagina);

        // Configura a primeira página
        $primeira = 1;

        // Offisets utilizados como limites para exibir os números das
        // páginas deixando a página atual no centro
        $offset1 = 3;
        $offset2 = 6;

        // Página atual
        $atual = ($this->parametros[1]) ? $this->parametros[1] : 1;

        // Exibe o link para a primeira página 
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

        // Primeiro loop toma conta da parte esquerda dos números
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
        
        // Segundo loop toma conta da parte direita dos números
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
        
        // Exibe o link para a última página 
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
