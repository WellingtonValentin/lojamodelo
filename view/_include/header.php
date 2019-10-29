<?php if (!defined('ABSPATH')) exit; ?>
<?
if (!strstr($_SERVER['SCRIPT_URI'], "www")) {
    $newLocation = str_replace("://", "://www.", $_SERVER['SCRIPT_URI']);
    header("Location:$newLocation");
}

$login = new Login();

$login->VerificaLogin();
$cliente = $login->userdata;

$modelCliente = $this->loadModel("cliente/model-cliente");
$modelCliente->paginaVisitada($this->title);
?>
<? require ABSPATH . "/enum.php"; ?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="pt-BR">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="pt-BR">
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html lang="pt-BR">
    <!--<![endif]-->
    <head>
        <? require ABSPATH . "/view/_include/include.php"; ?>
        <? require ABSPATH . "/view/_include/script.php"; ?>
    </head>
    <body id="wrapper" class="roboto">
        <div id="preloader">
            <div id="preloader-status">&nbsp;</div>
        </div>
        <div class="barra-lateral">
            <ul id="categoria-lateral">
                <li>
                    <div class="titulo-barra-lateral source size-1-6 color-dark-gray semi-bold">
                        <i class="glyphicon glyphicon-th"></i>   Navegue por Nossos Departamentos
                    </div>
                </li>
                <?
                $this->db->tabela = "categoria";
                $consultaCat = $this->db->consulta("WHERE categoriaFK IS NULL", "ORDER BY titulo ASC");
                while ($linhaCat = mysql_fetch_assoc($consultaCat)) {
                    $idCat = $linhaCat['id'];
                    $consultaSub = $this->db->consulta("WHERE categoriaFK = '$linhaCat[id]'", "ORDER BY titulo ASC");
                    ?>
                    <li  <?= (mysql_num_rows($consultaSub)) ? "onclick=\"carregaCategoriaLateral('$idCat', 'main')\"" : "" ?>>
                        <span class="color-padrao source normal size-1-6">
                            <? if (!mysql_num_rows($consultaSub)) { ?> 
                                <a href="<?= HOME_URL ?>/produto/categoria/<?= $linhaCat["id"] ?>/<?= arrumaString($linhaCat["titulo"]) ?>.html#conteudo" class="color-padrao">
                                <? } ?>
                                <?= $linhaCat["titulo"] ?>
                                <? if (!mysql_num_rows($consultaSub)) { ?> 
                                </a>
                            <? } ?>
                        </span> <? if (mysql_num_rows($consultaSub)) { ?> <i class="glyphicon glyphicon-menu-right size-2-2 light color-padrao"></i><? } ?>
                    </li>
                <? } ?>
            </ul>
            <ul id="sub-categoria-lateral"></ul>
        </div>
        <div class="background-overlay cursor-pointer text-center" onclick="funcaoChamaCatLateral('fecha')">
            <i class="glyphicon glyphicon-chevron-left color-white size-2-5"></i>
        </div>

        <div id="preloader">
            <div id="preloader-status"></div>
        </div>

        <div  class="move">
            <header>
                <section id="cabecalho">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-5">
                                <a href="<?= HOME_URL ?>">
                                    <img src="<?= IMG_URL ?>/pontodasbicicletas-logo.png" alt="<?= $empresa['titulo'] ?>" title="<?= $empresa['titulo'] ?>" class="logo"/>
                                </a>
                            </div>
                            <div class="col-xs-<?= ($login->logado) ? "7" : "5" ?>">
                                <nav>
                                    <ul>
                                        <?
                                        $this->db->tabela = "texto";
                                        $this->db->limit = 3;
                                        $consulta = $this->db->consulta();
                                        while ($campo = mysql_fetch_assoc($consulta)) {
                                            ?>
                                            <li class="pull-left">
                                                <a href="<?= HOME_URL ?>/institucional/index/<?= $campo['id'] ?>/<?= arrumaString($campo['titulo']) ?>.html#conteudo" class="source normal size-1-4 color-gray">
                                                    <?= $campo['titulo'] ?>
                                                </a>
                                            </li>
                                            <?
                                        }
                                        ?>
                                        <? if ($login->logado) { ?>
                                            <li class="pull-left">
                                                <a href="<?= HOME_URL ?>/cliente/dados/meus-dados.html#conteudo" class="source normal size-1-4 color-gray">Meus Dados</a>
                                            </li>
                                            <li class="pull-left">
                                                <a href="<?= HOME_URL ?>/cliente/pedido/meus-pedidos.html#conteudo" class="source normal size-1-4 color-gray">Meus Pedidos</a>
                                            </li>
                                            <li class="pull-left">
                                                <a href="<?= HOME_URL ?>/cliente/sair/logout.html#conteudo" class="source normal size-1-4 color-gray">Sair</a>
                                            </li>
                                        <? } ?> 
                                    </ul>
                                </nav>
                            </div>
                            <? if (!$login->logado) { ?>
                                <div class="col-xs-2 text-center position-relative">
                                    <br/><br/>
                                    <i class="fa fa-user color-light-blue size-1-4" onclick="$('.quadro-login').slideToggle()"></i> 
                                    <span class="source normal size-1-4 color-gray cursor-pointer underline" onclick="$('.quadro-login').slideToggle()">
                                        Faça seu Login
                                    </span>
                                    <form action="<?= HOME_URL ?>/cliente/login/login.html" method="POST">
                                        <div class="quadro-login">
                                            <div class="input-group">
                                                <span class="input-group-addon" id="login-topo"><i class="glyphicon glyphicon-user"></i></span>
                                                <input type="email" name="email" class="form-control" placeholder="Login" aria-describedby="login-topo">
                                            </div>
                                            <br/>
                                            <div class="input-group">
                                                <span class="input-group-addon" id="senha-topo"><i class="glyphicon glyphicon-lock"></i></span>
                                                <input type="password" name="senha" class="form-control" placeholder="Senha" aria-describedby="senha-topo">
                                            </div>
                                            <br/>
                                            <button type="submit" name="loginCliente" class="btn btn-sm btn-primary pull-right">
                                                <span class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp;&nbsp;Entrar
                                            </button>
                                            <label for="senhaCli" class="control-label pull-left source normal size-1-2 color-gray cursor-pointer" data-toggle="modal" data-target="#quadroEsqueciSenha">
                                                Esqueci minha senha.
                                            </label>
                                        </div>
                                    </form>
                                    <div class="modal fade bs-example-modal-sm" id="quadroEsqueciSenha" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Esqueci minha senha.</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="lembrarSenha">
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div class="col-md-10 col-md-offset-1 size-1-2 text-center">
                                                                    Informe o seu e-mail que enviaremos um link para que você possa alterar a sua senha.
                                                                </div>
                                                            </div><br/>
                                                            <div class="row">
                                                                <div class="col-md-10 col-md-offset-1 input-group">
                                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                                                    <input type="text" name="email" class="form-control" id="emailEsqueciSenha" placeholder="E-mail">
                                                                </div>
                                                            </div><br/>
                                                            <div id="resultado-modal-sucesso" class="display-none alert alert-success size-1-2 text-center" role="alert">
                                                                Um link para a mudança de senha foi enviado para o seu e-mail!
                                                            </div>
                                                            <div id="resultado-modal-erro" class="display-none alert alert-danger size-1-2 text-center" role="alert"></div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>
                                                    <button class="btn btn-success" id="submitEsqueciSenha">ENVIAR</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                            <div class="col-xs-5">
                                <? require_once ABSPATH . '/view/_include/formBusca.php'; ?>
                            </div>
                            <div class="col-xs-2">
                                <a href="<?= HOME_URL ?>/carrinho/meus-produtos/detalhes.html#conteudo">
                                    <div class="quadro-carrinho text-center source bold size-1-6 color-white">
                                        <i class="glyphicon glyphicon-shopping-cart"></i>    CARRINHO
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <?
                            $this->db->tabela = "categoria";
                            $this->db->limit = 9999;
                            $consultaCatTop = $this->db->consulta("WHERE topo = 'S'", "ORDER BY titulo ASC");
                            $totalRegistros = mysql_num_rows($consultaCatTop);
                            $cont = 0;
                            while ($linhaCat = mysql_fetch_assoc($consultaCatTop)) {
                                $consultaSubCat = $this->db->consulta("WHERE categoriaFK = '" . $linhaCat['id'] . "'");
                                $cont++;
                                if ($cont > 6) {
                                    break;
                                }
                                ?>
                                <div class="col-xs-2 text-center position-relative" onmouseover="$(this).children('.quadro-sub-cat').show()" onmouseout="$(this).children('.quadro-sub-cat').hide()">
                                    <a href="<?= HOME_URL ?>/produto/categoria/<?= $linhaCat['id'] ?>/<?= arrumaString($linhaCat['titulo']) ?>.html" class="oswald normal size-1-6 color-white text-uppercase">
                                        <?= $linhaCat['titulo'] ?>
                                    </a>
                                    <? if (mysql_num_rows($consultaSubCat)) { ?>
                                        <div class="quadro-sub-cat">
                                            <? while ($subCat = mysql_fetch_assoc($consultaSubCat)) { ?>
                                                <a href="<?= HOME_URL ?>/produto/categoria/<?= $subCat['id'] ?>/<?= arrumaString($subCat['titulo']) ?>.html" class="oswald normal size-1-6 color-white text-uppercase">
                                                    <?= $subCat['titulo'] ?>
                                                </a>
                                            <? } ?>
                                        </div>
                                    <? } ?>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                </section>
            </header>
            <article id="conteudo">