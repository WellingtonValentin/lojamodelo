<?

// Tras os dados da loja
$this->db->tabela = "config";
$empresa = $this->db->consultaId(1);

// Tras os dados da loja
$this->db->tabela = "config_valores";
$configValores = $this->db->consultaId(1);

// Configurações de e-mail e SMTP
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

// Monta array de configurações de frete
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
    "JURIDICA" => "Pessoa Jurídica"
);

$tipoCliente2 = array(
    "FISICA" => "Pessoa Fisica",
    "JURIDICA" => "Pessoa Jurídica",
    "TESTE_FISICA" => "Pessoa Física - Teste",
    "TESTE_JURÍDICA" => "Pessoa Jurídica - Teste"
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
    "N" => "Não",
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
    "UPDATE" => "Alteração",
    "DELETE" => "Exclusão"
);

$assuntoEmail = array(
    "contato" => "Contato on-line",
    "esqueci_senha" => "Alteração de senha",
    "indicar_amigo" => "Indicação de produto",
    "indicar_site" => "Indicação de site",
    "cadastro" => "Cadastro realizado",
    "cupom" => "Cupom de desconto",
    "pedido" => "Pedido realizado com sucesso",
    "produto_disponivel" => "Produto disponível na loja",
    "atualizacao_status" => "Alteração de status de pedido",
    "pesquisa_satisfacao" => "Pesquisa de satisfação"
);
