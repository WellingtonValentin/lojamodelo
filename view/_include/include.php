<?php if (!defined('ABSPATH')) exit; ?>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width">
<title><?= $empresa["titulo"] ?> - <?= $this->title ?></title>
<meta name="description" content="<?= (!isset($this->description)) ? (isset($seo[0])) ? $seo[0]["description"] : "" : $this->description ?>" />
<meta name="keywords" content="<?= (!isset($this->keyword)) ? (isset($seo[0])) ? $seo[0]["keyword"] : "" : $this->keyword ?>">
<meta name="robots" content="index, follow"> 
<link rel="shortcut icon" href="<?= IMG_URL ?>/favicon.png" type="image/icon"/>

<!-- Folhas de estilos --->
<link href="<?= HOME_URL ?>/view/_css/reset.css" rel="stylesheet" type="text/css">
<link href="<?= HOME_URL ?>/view/_css/geral.css" rel="stylesheet" type="text/css">
<link href="<?= HOME_URL ?>/view/_css/main.css" rel="stylesheet" type="text/css">
<link href="<?= HOME_URL ?>/view/_css/mediascreen.css" rel="stylesheet" type="text/css">
<link href="<?= HOME_URL ?>/view/_css/bootstrap-theme.css" rel="stylesheet" type="text/css">

<!-- JS Padrï¿½es --->
<script src="<?= HOME_URL ?>/view/_js/jquery.min.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/gerais.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/localizacao.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/maskedinput.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/maskmoney.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/modernizr.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/resizeToRatio.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/maskedinput.js" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/view/_js/maskmoney.js" type="text/javascript"></script>

<!-- BootStrap --->
<link  href="<?= HOME_URL ?>/_utilitarios/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
<script src="<?= HOME_URL ?>/_utilitarios/bootstrap/js/bootstrap.js" type="text/javascript"></script>

<!-- Font Awesome --->
<link  href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- Elevate Zoom --->
<script src="<?= HOME_URL ?>/_utilitarios/elevatezoom/elevateZoom-min.js" type="text/javascript"></script>

<!-- Owl Carousel --->
<link  href="<?= HOME_URL ?>/_utilitarios/owlcarousel/carousel.css" rel="stylesheet" type="text/css">
<script src="<?= HOME_URL ?>/_utilitarios/owlcarousel/carousel.min.js" type="text/javascript"></script>

<!-- Fancybox --->
<link  href="<?= HOME_URL ?>/_utilitarios/fancybox/fancybox.css?v=2.1.5" rel="stylesheet" type="text/css">
<script src="<?= HOME_URL ?>/_utilitarios/fancybox/fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
<link  href="<?= HOME_URL ?>/_utilitarios/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" rel="stylesheet" type="text/css">
<script src="<?= HOME_URL ?>/_utilitarios/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5" type="text/javascript"></script>
<script src="<?= HOME_URL ?>/_utilitarios/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6" type="text/javascript"></script>
<link  href="<?= HOME_URL ?>/_utilitarios/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" rel="stylesheet" type="text/css">
<script src="<?= HOME_URL ?>/_utilitarios/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7" type="text/javascript"></script>

<!-- Spinner JQuery UI --->
<script src="<?= HOME_URL ?>/_utilitarios/jqueryui-spinner/jquery-ui.min.js" type="text/javascript"></script>
<link href="<?= HOME_URL ?>/_utilitarios/jqueryui-spinner/jquery-ui.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Sidebar --->
<script src="<?= HOME_URL ?>/_utilitarios/bootstrap-sidebar/script.js" type="text/javascript"></script>
<link href="<?= HOME_URL ?>/_utilitarios/bootstrap-sidebar/estilos.css" rel="stylesheet" type="text/css">

<!-- Google Icons --->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<!-- Bootstrap Minus e Plus --->
<script src="<?= HOME_URL ?>/_utilitarios/bootstrap-minus-plus/script.js" type="text/javascript"></script>

<!-- Plugin que remove espaços vazios entre as divs com float left --->
<script src="<?= HOME_URL ?>/_utilitarios/masonry/masonry.js" type="text/javascript"></script>

<!-- Lightbox do Pag Seguro --->
<script src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js" type="text/javascript"></script>

<!-- Facebook -->
<meta property="og:url"           content="<?= HOME_URL . $_SERVER ["REQUEST_URI"] ?>" />
<meta property="og:type"          content="product" />
<meta property="og:title"         content="<?= (isset($this->title)) ? $this->title : HOME_URL . "/view/_image/logo.png" ?>" />
<meta property="og:description"   content="<?= (isset($this->description)) ? $this->description : "" ?>" />
<meta property="og:image"         content="<?= (isset($this->image)) ? $this->image : "" ?>" />


<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/5728a370223d6cfa50b857d7/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
<!--End of Tawk.to Script-->