<?

/**
 * Classe para gerenciamento do banco de dados
 * 
 */
class DB extends MainModel {

    // Propriedades do Banco
    public $host = "", // Host do banco de dados
            $dbName = "", // Nome do banco de dados
            $password = "", // Senha do usu�rio do banco de dados
            $user = "", // Usu�rio do banco de dados
            $charset = "utf8", // Charset do banco de dados
            $erros = null, // Configura o erro
            $debug = false, // Mostra todos os erros
            $db = null, // Identificador da conex�o com o banco de dados
            $lastID = null, // �ltimo ID inserido
            $tabela = null, // Tabela que esta sendo tratada na fun��o
            $limit = null, // Quantidade de registros qu vir�o na consulta
            $required = array(), // Campos obrigat�rios para o persist
            $valores = array();     // Array de valores que ser�o passados para a fun��o de persistencia

    function __construct($host = null, $dbName = null, $password = null, $user = null, $charset = null, $debug = null) {
        // Configura as propriedades novamente
        $this->host = defined("HOSTNAME") ? HOSTNAME : $this->host;
        $this->dbName = defined("DB_NAME") ? DB_NAME : $this->dbName;
        $this->password = defined("DB_PASSWORD") ? DB_PASSWORD : $this->password;
        $this->user = defined("DB_USER") ? DB_USER : $this->user;
        $this->charset = defined("DB_CHARSET") ? DB_CHARSET : $this->charset;
        $this->debug = defined("DEBUG") ? DEBUG : $this->debug;

        // Conecta
        $this->connect();
    }

    // Cria a conex�o com o banco de dados
    final protected function connect() {
        // Estabelece a conex�o com o banco de dados
        $this->db = @mysql_pconnect($this->host, $this->user, $this->password);
        
        // Seleciona o banco
        @mysql_select_db($this->dbName) or die("Houve um problema ao conectar: " . mysql_error());
    }

    // Importa array para ser utilizado na fun��o de persistencia
    public function importArray($array) {

        $valores = array();
        
        // Percorre o array passado na fun��o atribuindo 
        // seus valores e indices para a constante da classe
        foreach ((array) $array as $indice => $valor) {
            $valores[$indice] = $valor;
        }

        $this->valores = $valores;
    }

    // Fun��o que realiza a consulta no banco de dados
    public function consulta($filtro = "", $order = "", $limit = "", $join = "", $group = "", $campo = "", $prefixoTabela = "", $semCampoData = FALSE, $semCampoExcluir = FALSE) {

        // Caso n�o tenha sido informado os campos ser� trazido todos
        if ($campo == "") {
            $campo = "*";
        }
        
        if ($limit == "" && $this->limit) {
            $limit = "LIMIT " . $this->limit;
        }

        // Verifica se ter� a necessidade de apresentar os campos excluidos
        if (!$semCampoExcluir) {
            if ($filtro) {
                $filtro .= " AND ";
                if ($prefixoTabela) {
                    $filtro .= $prefixoTabela . ".";
                }
                $filtro .= "excluido = '0'";
            } else {
                $filtro = "WHERE ";
                if ($prefixoTabela) {
                    $filtro .= $prefixoTabela . ".";
                }
                $filtro .= "excluido = '0'";
            }
        }

        // Efetuar consulta utilizando os campos da fun��o
        $sql = "SELECT $campo FROM $this->tabela $prefixoTabela $join $filtro $group $order $limit";
        $query = mysql_query($sql) or die(mysql_error() . " - " . $sql);
        return $query;
    }

    // Fun��o para transformar o resultado da query em array
    public function fetchAll($query) {
        $retorno = array();
        while ($linha = mysql_fetch_assoc($query)) {
            $retorno[] = $linha;
        }
        return $retorno;
    }

    // Retorna apenas uma linha do resultado
    public function fetch($query) {
        $retorno = array();
        if (mysql_num_rows($query)) {
            $linha = mysql_fetch_assoc($query);
            $retorno[] = $linha;
        }
        return $retorno;
    }

    // Fun��o �til para trazer apenas uma linha da consulta da tabela
    public function consultaId($id, $and = "") {
        $consulta = $this->consulta("WHERE id = '$id' $and");
        $campo = mysql_fetch_assoc($consulta);
        return $campo;
    }

    // Fun��o para apagar um registro especifico sendo esse l�gico ou n�o
    public function apagaId($id = "", $where = "", $logico = FALSE) {
        // Caso n�o tenha sido definido uma logica para a exclus�o ser� apagado o id
        if ($where == "") {
            $where = "WHERE id = '$id'";
        } else {
            $where = "WHERE " . $where;
        }

        // Define se a exclus�o sera fisica ou l�gica
        switch ($logico) {
            case TRUE:
                $sql = "UPDATE $this->tabela SET excluido = 1 $where";
                break;
            default:
                $sql = "DELETE FROM $this->tabela $where";
                break;
        }

        // Executa a query
        $query = mysql_query($sql) or die(mysql_error() . " - " . $sql);
    }

    // Fun��o para apagar v�rios registros de uma vez
    public function apagar($campo) {
        // Percorre o array apagando os registros selecionados no formul�rio
        foreach ((array) $campo as $ind => $valor) {
            $this->apagaId($valor);
        }
    }

    // Fun��o para trazer todas a colunas da tabela
    public function colunas($string = "") {
        $buscaColunas = mysql_query("SELECT IS_NULLABLE as nullable, "
                . "DATA_TYPE as tipo, "
                . "COLUMN_NAME as campo "
                . "FROM information_schema.COLUMNS "
                . "WHERE TABLE_NAME = '$this->tabela' AND "
                . "TABLE_SCHEMA = '$this->dbName' AND "
                . "COLUMN_NAME != 'id' $string") or die(mysql_error());
        while ($coluna = mysql_fetch_assoc($buscaColunas)) {
            $colunas[] = $coluna;
        }
        return $colunas;
    }

    // Fun��o de persistencia que grava os registros no banco
    public function persist($id = "") {
        
        // Traz todas as colunas da tabela
        $colunas = $this->colunas();
        $valores = $this->valores;
        $campos = array();
        $query = "";
        
        foreach ($colunas as $coluna) {
            
            // Verifica se o nome da coluna foi 
            // passado pela fun��o arrayImport
            if (array_key_exists($coluna["campo"], $valores)) {
                
                // Atribui para a variavel o valor do 
                // campo com nome da coluna do arrayImport
                $valor = addslashes($valores[$coluna["campo"]]);

                // Adapta o valor passado no array para o campo no banco
                switch ($coluna["tipo"]) {
                    case "decimal":
                    case "float":
                    case "double":
                        // Caso decimal, float, double receber�
                        // o formato 9999.99
                        $valor = vtop($valor);
                        $validado = ($valor > 0);
                        break;
                    case "int":
                        // Caso int receber� o formato 9999
                        $valor = (int) $valor;
                        $validado = ($valor > 0);
                        break;
                    case "date":
                        // Caso int receber� o formato yyyy-mm-dd
                        $validado = ($valor != "");
                        $valor = inverteData($valor);
                        break;
                    case "datetime":
                        // Caso int receber� o formato yyyy-mm-dd hh:mm:ss
                        $validado = ($valor != "");
                        $valor = inverteData($valor);
                        break;
                    default:
                        $validado = ($valor != "");
                        break;
                }

                // Caso algum campo da tabela n�o tenha sido passado
                // no importArray ser� atribuido o valor null para ele
                if ($coluna["campo"] != "ultimaAlteracao") {
                    if (!$validado && $coluna["nullable"] == "YES") {
                        $campos[] = "{$coluna['campo']} = NULL";
                    } else {
                        $campos[] = "{$coluna['campo']} = '" . stripslashes($valor) . "'";
                    }
                }
            }

            // Caso algum campo requerido tenha sido deixado null
            // ser� emitida a mensagem de erro
            if (array_key_exists($coluna["campo"], (array) $this->required) && !$validado) {
                $this->erros[] = "Preencha o campo: {$this->required[$coluna["campo"]]}";
            }
        }

        // Caso n�o tenha erros e existam campos passados no 
        // importArray ir� preparar o banco para inser�ao ou update
        if (is_array($campos) && !is_array($this->erros)) {
            // Atribui ao campo ultimaAlteracao a data atual
            $campos[] = "ultimaAlteracao = NOW()";

            // Come�a a preparar os campos 
            // que ser�o inseridos na tabela
            $values = implode(", ", $campos);
            $condicao = "";

            // Definindo se ser� feita uma inser��o ou altera��o
            if ($id) {
                $comando = "UPDATE";
                $condicao = "WHERE id = '$id'";
            } else {
                $comando = "INSERT";
            }

            // Executa a persistencia
            $sql = "$comando $this->tabela SET $values $condicao";
            mysql_query("BEGIN");
            $query = mysql_query($sql) or die(mysql_error() . " - " . $sql);
        }

        /**
         * Caso tenha sido executada a query sem erros
         * Executa o commit e exibe a mensagem de confirma��o
         * Caso contr�rio exibe o erro
         */
        if (!is_array($this->erros) && $query) {
            $id = ($id) ? $id : mysql_insert_id();
            mysql_query("COMMIT");
            $this->status = "OK";
            $this->mensagem = "Opera��o realizada com sucesso.";
            $retorno = $id;
        } else {
            mysql_query("ROLLBACK");
            $this->status = "ERRO";
            $this->mensagem = $this->erros;
            $retorno = FALSE;
        }
        return $retorno;
    }

    // Monta a mensagem do resultado da persistencia
    function mensagem() {
        switch ($this->status) {
            case "OK":
                $mensagem = $this->mensagem;
                break;
            case "ERRO":
                $mensagem = "<b>Ocorreram os seguintes erros:</b><br/>";
                foreach ($this->mensagem as $ind => $valor) {
                    $mensagem .= " - $valor<br/>";
                }
                break;
        }
        return $mensagem;
    }

}
