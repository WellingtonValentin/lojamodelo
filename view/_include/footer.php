<?php if (!defined('ABSPATH')) exit; ?>
</article><!-- header -->
<footer>
    <section id="rodape">
        <div class="container">
            <div class="row">
                <div class="col-xs-5">
                    <div class="input-group">
                        <input type="text" class="form-control raleway semi-bold size-1-4 color-padrao" placeholder="O que você procura?">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <i class="glyphicon glyphicon-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-xs-3">
                    <i class="material-icons icone color-padrao size-2-2">&#xE8AF;</i> <span class="source bold size-2-2 color-dark-gray">Atendimento</span>
                </div>
                <? if (isset($telefone[0])) { ?>
                    <div class="col-xs-4">
                        <i class="glyphicon icone glyphicon-earphone color-padrao size-2-2"></i> <span class="source bold size-2 color-dark-gray"><?= str_replace(array("(", ")"), "", $telefone[0]['telefone']) ?></span>
                    </div>
                <? } ?>
            </div>
            <br/><br/>
            <div class="row">
                <?
                $this->db->tabela = "texto_grupo";
                $this->db->limit = 2;
                $consulta = $this->db->consulta();
                while ($linha = mysql_fetch_assoc($consulta)) {
                    $this->db->tabela = "texto";
                    $this->db->limit = 9999;
                    $consultaTxt = $this->db->consulta("WHERE grupoFK = '" . $linha['id'] . "'");
                    ?>
                    <div class="col-xs-3">
                        <nav>
                            <ul>
                                <li class="source bold size-1-4 color-padrao"><?= $linha['titulo'] ?></li><br/>
                                <? while ($txt = mysql_fetch_assoc($consultaTxt)) { ?>
                                    <li>
                                        <a href="<?= HOME_URL ?>/institucional/index/<?= $txt['id'] ?>/<?= arrumaString($txt['titulo']) ?>.html#conteudo" class="source normal size-1-2 color-gray">
                                            <?= $txt['titulo'] ?>
                                        </a>
                                    </li>
                                <? } ?>
                            </ul>
                        </nav>
                    </div>
                    <?
                }
                ?>
                <div class="col-xs-2">
                    <nav>
                        <ul>
                            <li class="source bold size-1-4 color-padrao">Atendimento</li><br/>
                            <li>
                                <a href="<?= HOME_URL ?>/atendimento/contato/fale-conosco.html#conteudo" class="source normal size-1-2 color-gray">
                                    Fale Conosco
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="col-xs-4">
                    <nav>
                        <ul>
                            <li class="source bold size-1-4 color-padrao">Formas de Pagamento</li><br/>
                        </ul>
                    </nav>
                    <img src="<?= IMG_URL ?>/forma_pagamento/formas-de-pagamento.png" width="100%"/>
                </div>
            </div>
            <br/><br/> 
            <div class="row">
                <div class="col-xs-12 source normal size-1-2 color-gray">
                    CNPJ: <?= $empresa['cnpj'] ?>. *Em caso de divergência de preços no site, o valor válido é o do Carrinho de Compras. Preços e condições válidos apenas para compras no site.  Os preços apresentados no site prevalecem sobre outros anunciados em qualquer outro meio de comunicação ou sites de buscas. Código de Defesa do Consumidor: Lei nº 8.078
                </div>
            </div>
        </div>
    </section>
</footer>
</div><!-- header -->
<div class="modal fade bs-example-modal-sm" id="indicarSite" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Indicar o site.</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="indicarSite">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0 input-group">
                                    <input type="text" name="nomeAmigo" class="form-control" id="nomeAmigo2" placeholder="Nome do seu amigo">
                                </div>
                            </div><br/>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0 input-group">
                                    <input type="text" name="emailAmigo" class="form-control" id="emailAmigo2" placeholder="E-mail do seu amigo">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0 input-group">
                                    <input type="text" name="nome" class="form-control" id="nomeCliente2" placeholder="Seu nome">
                                </div>
                            </div><br/>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1 col-xs-12 col-xs-offset-0 input-group">
                                    <input type="text" name="email" class="form-control" id="emailCliente2" placeholder="Seu e-mail">
                                </div>
                            </div><br/>
                            <div id="resultado-modal-sucesso2" class="alert alert-success size-1-2 text-center" role="alert">
                                Um e-mail de indicação foi enviado para o seu amigo, agradecemos a preferência!
                            </div>
                            <div id="resultado-modal-erro2" class="alert alert-danger size-1-2 text-center" role="alert"></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
                <button class="btn btn-success" id="submitIndicarSite">ENVIAR</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>