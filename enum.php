<?

// Tras os dados da loja
$this->db->tabela = "config";
$empresa = $this->db->consultaId(1);

// Tras os dados da loja
$this->db->tabela = "config_valores";
$configValores = $this->db->consultaId(1);

// Configura��es de e-mail e SMTP
$this->db->tabela = "config_email";
$configEmail = $this->db->consultaId(1);

// Monta array de estados
$this->db->tabela = "estado";
$consulta = $this->db->consulta();
$estado = $this->db->fetchAll($consulta);

// Monta array de redes sociais
$this->db->tabela = "rede_social";
$consulta = $this->db->consulta("WHERE link IS NOT NULL AND link != ''");
$redeSocial = array();
while ($linha = mysql_fetch_assoc($consulta)) {
    $redeSocial[$linha["classe"]]["link"] = $linha["link"];
    $redeSocial[$linha["classe"]]["titulo"] = $linha["titulo"];
}

// Monta array de telefones
$this->db->tabela = "telefone";
$consulta = $this->db->consulta("","ORDER BY principal ASC");
$telefone = $this->db->fetchAll($consulta);

// Monta array de email
$this->db->tabela = "email_contato";
$consulta = $this->db->consulta();
$emailContato = $this->db->fetchAll($consulta);

// Monta array de configura��es de frete
$this->db->tabela = "config_frete";
$consulta = $this->db->consulta();
$configFrete = $this->db->fetch($consulta);

// Monta array com dados de SEO
$this->db->tabela = "seo";
$consulta = $this->db->consulta();
$seo = $this->db->fetch($consulta);

// Enuns gerais do site
$tipoCliente = array(
    "FISICA" => "Pessoa Fisica",
    "JURIDICA" => "Pessoa Jur�dica"
);

$tipoCliente2 = array(
    "FISICA" => "Pessoa Fisica",
    "JURIDICA" => "Pessoa Jur�dica",
    "TESTE_FISICA" => "Pessoa F�sica - Teste",
    "TESTE_JUR�DICA" => "Pessoa Jur�dica - Teste"
);

$status = array(
    "A" => "Ativo",
    "I" => "Inativo"
);

$statusCupom = array(
    "AGUARDANDO" => "Aguardando",
    "RESGATADO" => "Resgatado",
    "CANCELADO" => "Cancelado"
);

$statusPesquisa = array(
    "AGUARDANDO" => "Aguardando",
    "ENVIADO" => "Enviado",
    "VISUALIZADO" => "Visualizado",
    "RESPONDIDO" => "Respondido"
);

$statusBoleto = array(
    "AGUARDANDO" => "Aguardando",
    "PAGO" => "Pago",
    "VENCIDO" => "Vencido"
);

$statusVisita = array(
    "ABERTO" => "Aberta",
    "FECHADO" => "Fechada"
);

$statusPedido = array(
    "AGUARDANDO" => "Aguardando",
    "CALCULANDO FRETE" => "Calculando frete",
    "PAGO" => "Pago",
    "EMBALANDO" => "Preparando para envio",
    "DESPACHADO" => "Despachado",
    "ENTREGUE" => "Entregue",
    "DISPUTA" => "Em disputa",
    "CANCELADO" => "Cancelado",
    "CREDITADO" => "Creditado",
    "DEVOLVIDO" => "Devolvido"
);

$tipoDesconto = array(
    "VALOR" => "Por Valor",
    "PORCENTAGEM" => "Por Porcentagem"
);

$tipoFrete = array(
    "PAC" => "PAC",
    "SEDEX" => "Sedex",
    "ESEDEX" => "e-SEDEX",
    "CARTA" => "Carta Registrada"
);

$destino = array(
    "INTERIOR" => "Interior",
    "CAPITAL" => "Capital"
);

$sexo = array(
    "M" => "Masculino",
    "F" => "Feminino"
);

$formaPagamento = array(
    "PAGSEGURO" => "PagSeguro",
    "BCASH" => "BCash",
    "MERCADO_PAGO" => "Mercado Pago",
    "CIELO" => "Cielo",
    "BOLETO" => "Boleto"
);

$simNao = array(
    "N" => "N�o",
    "S" => "Sim"
);

$servidorEmail = array(
    "GMAIL" => "Gmail",
    "ZOHO" => "Zoho"
);

$localBanner = array(
    "TOPO" => "Topo",
    "LATERAL" => "Lateral",
    "RODAPE" => "Rodape",
    "CATEGORIAS" => "Categorias"
);

$acoes = array(
    "INSERT" => "Cadastro",
    "UPDATE" => "Altera��o",
    "DELETE" => "Exclus�o"
);

$assuntoEmail = array(
    "contato" => "Contato on-line",
    "esqueci_senha" => "Altera��o de senha",
    "indicar_amigo" => "Indica��o de produto",
    "indicar_site" => "Indica��o de site",
    "cadastro" => "Cadastro realizado",
    "cupom" => "Cupom de desconto",
    "pedido" => "Pedido realizado com sucesso",
    "produto_disponivel" => "Produto dispon�vel na loja",
    "atualizacao_status" => "Altera��o de status de pedido",
    "pesquisa_satisfacao" => "Pesquisa de satisfa��o"
);
