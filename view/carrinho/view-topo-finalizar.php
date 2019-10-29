<?php if (!defined('ABSPATH')) exit; ?>
<?
//if (!strstr($_SERVER['SCRIPT_URI'], "www")) {
//    $newLocation = str_replace("://", "://www.", $_SERVER['SCRIPT_URI']);
//    header("Location:$newLocation");
//}

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
        <header>
            <section id="topo-finalizacao">
                <div class="container">
                    <div class="row margin-0 background-padrao">
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <a href="<?= HOME_URL ?>">
                                <img src="<?= IMG_URL ?>/logo-finalizacao.png" title="<?= $empresa['titulo'] ?>" alt="<?= $empresa['titulo'] ?>" class="logo" width="100%"/>
                            </a>
                        </div>
                        <div class="col-md-7 col-md-offset-1 col-sm-6 col-sm-offset-1 col-xs-10 col-xs-offset-1 passos-compra">
                            <ul class="text-center">
                                <li>
                                    <div class="icon text-center">
                                        <i class="fa fa-shopping-cart color-contraste size-1-5"></i>
                                    </div>
                                    <span class="size-1-2 normal color-light-gray hidden-xs">Meu Carrinho</span>
                                </li>
                                <li>
                                    <div class="icon text-center">
                                        <i class="glyphicon glyphicon-user color-contraste size-1-5"></i>
                                    </div>
                                    <span class="size-1-2 normal color-light-gray hidden-xs">Identificação</span>
                                </li>
                                <li class="ativo">
                                    <div class="icon text-center">
                                        <i class="fa fa-credit-card-alt color-red size-1-5"></i><br/>
                                    </div>
                                    <span class="size-1-2 normal color-red hidden-xs">Pagamento</span>
                                </li>
                                <li>
                                    <div class="icon text-center">
                                        <i class="fa fa-tags color-contraste size-1-5"></i>
                                    </div>
                                    <span class="size-1-2 normal color-light-gray hidden-xs">Obrigado</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </header>
        <article>