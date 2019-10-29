<script>
    $(document).ready(function ($) {

        /** MASCARAS */
        $(".money").maskMoney({
            thousands: ".",
            decimal: ","
        });
        $(".peso").maskMoney({
            thousands: ".",
            precision: 3,
            decimal: ","
        });
        $(".num2").mask("?99");
        $(".num3").mask("?999");
        $(".num4").mask("?9999");
        $('.num6').mask('?999999');
        $(".telefone").change(function () {
            var phone, element;
            element = $(this);
            phone = element.val().replace(/\D/g, '');
            if (phone.length > 10) {
                element.mask("(99) 99999-999?9");
            } else {
                element.mask("(99) 9999-9999?9");
            }
        }).change();
        $(".cpf").mask("999.999.999-99");
        $(".cnpj").mask("99.999.999/9999-99");
        $(".rg").mask("99.999.999-*");
        $(".cep").mask("99999-999");
        $(".date").mask("99/99/9999");
        $(".datahora").mask("99/99/9999 99:99:99");
        trocaTipo("FISICA");
        $('[data-toggle="tooltip"]').tooltip();
        /** INICIALIZAÇÃO ELEVATE ZOOM */
        $(".elevatezoom").elevateZoom({
            gallery: 'galleria-zoom',
            galleryActiveClass: 'active',
            responsive: true,
            lensColour: 'white',
            borderSize: 2,
            cursor: 'crosshair',
            zoomType: "lens",
            lensShape: "square",
            lensSize: 200
        });
        $(".elevatezoom").bind("click", function (e) {
            var ez = $('#galleria-zoom').data('elevateZoom');
            $.fancybox(ez.getGalleryList());
            $("#foto-grande").load();
            return false;
        });
        /** INICIALIZAÇÃO DA FANCYBOX */
        $(".fancyboxGaleria").fancybox({
            openEffect: 'none',
            closeEffect: 'none'
        });
        $(".fancyboxSingle").fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic',
            helpers: {
                title: {
                    type: 'inside'
                }
            }
        });
        $(".fancyboxVarious").fancybox({
            maxWidth: 800,
            maxHeight: 600,
            fitToView: false,
            width: '70%',
            height: '70%',
            autoSize: false,
            closeClick: false,
            openEffect: 'none',
            closeEffect: 'none'
        });
        /** INICIALIZAÇÃO OWL CAROUSEL */
        $(".owl-banner").owlCarousel({
            nav: false,
            items: 1,
            loop: true,
            autoplay: true,
            navigation: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true
        });
        /** CAROUSEL DE PRODUTOS DA HOME */
        $('.carousel-1').owlCarousel({
            navText: [
                "<img src='<?= IMG_URL ?>/seta-carousel-1-left.png'/>",
                "<img src='<?= IMG_URL ?>/seta-carousel-1-right.png'/>"
            ],
            loop: true,
            margin: 10,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 4
                }
            }
        });
        /** CAROUSEL DA PAGINA INTERNA DE PRODUTOS */
        $('.carousel-interna').owlCarousel({
            navText: [
                "<i class=\"glyphicon glyphicon-chevron-left size-3 color-gray\"></i>",
                "<i class=\"glyphicon glyphicon-chevron-right size-3 color-gray\"></i>"
            ],
            loop: true,
            margin: 10,
            nav: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 4
                }
            }
        });
        /** BANNER HOME **/
        $('.carousel-bootstrap').carousel();
        /** SPINNER JQUERY UI **/
        $(".spinner").spinner({
            min: 1,
            max: 9999
        });
//        setTimeout(function(){
//        alert("passso");
//        }, 5000);

        $(".quadro-compra-produto-flutuante").attr('style', 'display:none !important');
        /** VARIÁVEIS UTILIZADAS NAS TELAS DO SITE **/
        liberado = false; /** TELA DE PRODUTO INDICANDO SE ESTÁ LIBERADO O PRODUTO **/
        indisponivel = false; /** TELA DE PRODUTO INDICADOR SE O PRODUTO ESTA SEM ESTOQUE **/
        valorDe = ""; /** TELA DE PRODUTO VARIÁVEL QUE ARMAZENA O "VALOR DE" DO PRODUTO **/
        valorPor = ""; /** TELA DE PRODUTO VARIÁVEL QUE ARMAZENA O "VALOR POR" DO PRODUTO **/
        textoParcelamento = ""; /** TELA DE PRODUTO VARIÁVEL QUE ARMAZENA O "TEXTO DO PARCELAMENTO" DO PRODUTO **/
        slideAtual = 1; /** SLIDE DA GALERIA DE PRDUTOS  **/
    });
    $(document).scroll(function () {
        if ($(window).width() < 765) {
            if ($(window).scrollTop() > 800) {
                $(".quadro-compra-produto-flutuante").attr('style', 'display:block !important');
            } else {
                $(".quadro-compra-produto-flutuante").attr('style', 'display:none !important');
            }
        }

        if ($(window).scrollTop() > 200) {
            $("#quadro-topo-1 .col-md-10").slideUp("fast", function () {
                $("#quadro-topo-1 img").animate({height: 55}, 100);
                $("#quadro-topo-1 .quadro-todos-produtos").animate({margin: "0px"}, 100);
                $("#quadro-topo-1 .form-control").animate({margin: "0px"}, 100);
                $("#quadro-topo-1 .btn").animate({margin: "0px"}, 100);
                $("#quadro-topo-1 .quadro-carrinho").animate({margin: "0px"}, 100);
            });
        } else {
            $("#quadro-topo-1 .col-md-10").slideDown("fast", function () {
                $("#quadro-topo-1 img").animate({height: 115}, 100);
                $("#quadro-topo-1 .quadro-todos-produtos").animate({marginTop: "40px"}, 100);
                $("#quadro-topo-1 .form-control").animate({marginTop: "40px"}, 100);
                $("#quadro-topo-1 .btn").animate({marginTop: "40px"}, 100);
                $("#quadro-topo-1 .quadro-carrinho").animate({marginTop: "40px"}, 100);
            });
        }
    });
    /** FUNÇÃO PARA FILTRAR UTILIZANDO O MENU LATERAL **/
    function marcaFiltro(opt, id) {
        $.ajax({
            url: "<?= HOME_URL ?>/produto/ajaxFiltrar/" + opt + "/" + id,
            type: "GET",
            success: function (retorno) {
                $('#check-' + opt + "-" + id + ' i').toggle();

                location.href = "<?= HOME_URL ?>";
            },
            error: function () {
            }
        });
    }

    /** FUNÇÃO PARA MUDAR AS VARIAÇÕES NA TELA DO PRODUTO **/
    function selecionarVariacao(variacao, idOpcao, idProduto) {
        $('#preloader-status').fadeIn();
        $('#preloader').fadeIn();
        $("#alerta-erro").hide();
        $('#botao-comprar').attr('disabled', 'disabled');
        $("#quadro-botao-compra").attr('data-toggle', 'tooltip');
        $("#quadro-botao-compra").attr('data-placement', 'bottom');
        $("#quadro-botao-compra").attr('data-original-title', 'Selecione uma variação para efetuar a compra!');
        $(".aguarda-liberacao").each(function () {
            $(this).hide();
        });
        $("#idVariacao").val("");
        $("." + variacao).removeClass("disponivel");
        $("#" + idOpcao).addClass("disponivel");
        outrasVariacoes = "";
        $('.variacao-opcao').each(function () {
            if (!$(this).hasClass(variacao)) {
                outrasVariacoes = outrasVariacoes + ", " + $(this).attr("id");
            }
        });
        $.ajax({
            url: "<?= HOME_URL ?>/produto/ajaxVerificaVariacao",
            type: "POST",
            data: {
                arrayVariacoes: outrasVariacoes,
                opcao: idOpcao,
                produto: idProduto
            },
            success: function (retorno) {
                dados = jQuery.parseJSON(retorno);
                $(".variacao-opcao").removeClass("indisponivel");
                for (var i = 0; i < dados.length; i++) {
                    $("#" + dados[i]).removeClass("disponivel");
                    $("#" + dados[i]).addClass("indisponivel");
                }

                if ($("#variacoes-produto ul").length == $(".disponivel").length) {
                    atribuiVariacao(idProduto);
                }
                $('#preloader-status').fadeOut(); // will first fade out the loading animation
                $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
                $('body').delay(350).css({'overflow': 'visible'});
            },
            error: function () {
                $("#alerta-erro").show();
                $('#preloader-status').fadeOut(); // will first fade out the loading animation
                $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
                $('body').delay(350).css({'overflow': 'visible'});
            }
        });
    }

    /**  **/
    function atribuiVariacao(idProduto) {

        outrasVariacoes = "";
        $(".disponivel").each(function () {
            outrasVariacoes = outrasVariacoes + ", " + $(this).attr("id");
        });
        $.ajax({
            url: "<?= HOME_URL ?>/produto/atribuiVariacao",
            type: "POST",
            data: {
                arrayVariacoes: outrasVariacoes,
                produto: idProduto
            },
            success: function (retorno) {
                if (retorno == "INDISPONIVEL") {
                    $(".produto-indisponivel").show();
                    $(".produto-disponivel").hide();
                } else {
                    $(".produto-disponivel").show();
                    $(".produto-indisponivel").hide();
                    $(".aguarda-liberacao").each(function () {
                        $(this).show();
                    });
                    var dados = jQuery.parseJSON(retorno);
                    $(".spinner").spinner({
                        min: 1,
                        max: dados.estoque
                    });
                    $("#idVariacao").val(dados.variacao);
                    $("#quadro-valores-produto").html(dados.valores);
                    $("#area-parcelamento").html(dados.parcelamento);
                    $("#botao-comprar").removeAttr("disabled");
                    $("#quadro-botao-compra").removeAttr("data-toggle");
                    $("#quadro-botao-compra").removeAttr('data-placement');
                    $("#quadro-botao-compra").removeAttr('data-original-title');
                }
            },
            error: function () {
                $("#alerta-erro").show();
            }
        });
    }

    /** FAZ A TROCA DOS CAMPOS DO CLIENTE */
    function trocaTipo(tipo) {
        if (tipo == "FISICA") {
            $(".pf").show();
            $(".pj").hide();
        } else {
            $(".pj").show();
            $(".pf").hide();
        }
    }

    function funcaoChamaCatLateral(acao) {
        if (acao == "abre") {
            $('.background-overlay').show();
            $('.background-overlay').animate({
                width: '15%',
                left: "85%"
            });
            $('.move').css({
                position: 'fixed',
                overflow: 'hidden'
            });
            $('.move').animate({
                left: "85%"
            });
            $('.barra-lateral').animate({
                left: "0%"
            });
        } else {
            $('.barra-lateral').animate({
                left: "-100%"
            });
            $('.move').animate({
                left: "0%"
            });
            $('.move').css({
                position: 'relative',
                overflow: 'visible'
            });
            $('.background-overlay').animate({
                width: '100%',
                left: "0%"
            }, function () {
                $('.background-overlay').hide();
            });
        }
    }

    /** FUNÇÃO PARA CARREGAR CATEGORIAS NO MENU LATERAL **/
    function carregaCategoriaLateral(idCategoria, acao) {
        if (acao == "main") {
            $.ajax({
                url: "<?= HOME_URL ?>/produto/ajaxCategoriaLateral",
                type: "POST",
                data: {
                    idCat: idCategoria
                },
                success: function (retorno) {
                    $('#sub-categoria-lateral').html(retorno);
                    $('#categoria-lateral').animate({
                        left: '-100%'
                    });
                    $('#sub-categoria-lateral').animate({
                        left: '0%'
                    });
                },
                error: function () {
                }
            });
        } else {
            $('#categoria-lateral').animate({
                left: '0%'
            });
            $('#sub-categoria-lateral').animate({
                left: '100%'
            });
        }
    }

    /** PREENCHIMENTO AUTOMATICO DO ENDEREÇO */
    function preencheEndereco(cep) {
        tamanho = cep.length;
        for (i = 0; i < tamanho; i++) {
            cep = cep.replace("_", "");
        }
        if (cep.length == 9) {
            $('#modalCarregando').modal({
                backdrop: false,
                show: true
            });
            $.post('<?= HOME_URL ?>/frete/buscarendereco/' + cep, function (retorno) {
                retorno = eval("(" + retorno + ")");
                status = retorno.status;
                if (status == "sucesso") {
                    logradouro = retorno.logradouro;
                    bairro = retorno.bairro;
                    cidade = retorno.cidade;
                    uf = retorno.uf;
                    $("#endereco").val(logradouro);
                    $("#bairro").val(bairro);
                    $("#cidade").val(cidade);
                    $("#estado").val(uf);
                    $("#numero").focus();
                }
                $('#modalCarregando').modal('hide');
            })
        }
    }

    /** TROCA A IMAGEM DA GALERIA DO PRODUTO */
    function trocaImagem(src) {
        $("#foto-grande").attr({"src": src});
        $("#foto-grande").attr({"data-zoom-image": src});
    }

    /** MANIPULA OS FILTROS DOS PRODUTOS DA HOME */
    function  mudarFiltro(indice, valor) {
        switch (indice) {
            case "limite":
                $.post("<?= HOME_URL ?>/view/_ajax/filtroHome.php", {limite: valor}, function () {
                    location.reload();
                })
                break;
            case "ordem":
                $.post("<?= HOME_URL ?>/view/_ajax/filtroHome.php", {ordem: valor}, function () {
                    location.reload();
                })
                break;
            case "faixa":
                $.post("<?= HOME_URL ?>/view/_ajax/filtroHome.php", {faixa: valor}, function () {
                    location.reload();
                })
                break;
            case "prontaEntrega":
                $.post("<?= HOME_URL ?>/view/_ajax/filtroHome.php", {prontaEntrega: valor}, function () {
                    location.reload();
                })
                break;
        }
        ;
    }

    /** FUNÇÕES DE TROCA DE TABS */
    function trocaTabIdentificacao(tab) {
        if (!$(tab).hasClass("tab-ativa")) {
            switch ($(tab).attr('id')) {
                case "tab-login":
                    $("#tab-cadastro").removeClass("tab-ativa");
                    $("#tab-login").addClass("tab-ativa");
                    $("#cadastro").removeClass("tab-ativa");
                    $("#login").addClass("tab-ativa");
                    break;
                case "tab-cadastro":
                    $("#tab-cadastro").addClass("tab-ativa");
                    $("#tab-login").removeClass("tab-ativa");
                    $("#login").removeClass("tab-ativa");
                    $("#cadastro").addClass("tab-ativa");
                    break;
            }
        }
    }

    /** FUNÇÃO PARA CHECKAR TODAS AS CHECKBOX **/
    function checkedOrUnCheckedAll(field) {
        if (field.checked) {
            $("input[type=checkbox]").attr('checked', true);
        } else {
            $("input[type=checkbox]").attr('checked', false);
        }
    }

    /** FUNÇÃO PARA CHAMAR O CÁLCULO DE FRETE **/
    function calcularFrete(cep, idProduto, idCombinacao, local) {
        switch (local) {
            case "PRODUTO":
//                $("#icone-carregando").show();
                $('#preloader-status').fadeIn();
                $('#preloader').fadeIn();
                $.post("<?= HOME_URL ?>/frete/calcularFrete/calculo-de-frete.html", {cep: cep, idProduto: idProduto, idCombinacao: idCombinacao}, function (retorno) {
                    $("#resultadoFrete").html(retorno);
//                    $("#icone-carregando").hide();
                    $('#preloader-status').fadeOut(); // will first fade out the loading animation
                    $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
                    $('body').delay(350).css({'overflow': 'visible'});
                });
                break;
            case "CARRINHO":
//                $("#icone-carregando").show();
                $('#preloader-status').fadeIn();
                $('#preloader').fadeIn();
                var loading = setTimeout(function () {
                    $.ajax({
                        url: "<?= HOME_URL ?>/frete/calcularFreteCarrinho/calculo-de-frete-carrinho.html",
                        type: "POST",
                        data: {
                            cep: cep
                        },
                        success: function (retorno) {
                            $("#resultado-frete").slideDown(500, function () {
                                $("#resultado-frete").html(retorno);
                                $("#mensagem-retorno-frete").show();
//                                $("#icone-carregando").hide();
                                $('#preloader-status').fadeOut(); // will first fade out the loading animation
                                $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
                                $('body').delay(350).css({'overflow': 'visible'});
                            });
                        },
                        error: function () {
                        }
                    });
                }, 500);
                break;
        }
    }

    /** FUNÇÃO PARA TELA DO CARRINHO QUANDO CLICAR NO FRETE SELECIONADO IRA MUDAR O VALOR TOTAL DO PEDIDO **/
    function trocaValorFrete(frete, valor, valorFormatado, radioButton) {
        $.ajax({
            url: "<?= HOME_URL ?>/carrinho/selecionarFrete",
            type: "POST",
            data: {
                valor: valor,
                frete: frete
            },
            success: function (retorno) {
                var dados = jQuery.parseJSON(retorno);
                $(radioButton).prop("checked", true);
                $("#area-valor-frete").html("<strong>Frete: </strong> " + valorFormatado);
                $("#valor-total-pedido").html("<span class='roboto size-2-2 color-padrao'><strong>Total: </strong>R$ " + dados.total + "</span>");
                if (dados.numeroParcela) {
                    $("#valor-total-pedido").append("<br/><span class='roboto normal size-1 color-dark-gray'>Em até em " + dados.numeroParcela + "x sem juros de R$ " + dados.valorParcela + "</span>");
                }
                if (dados.descontoBoleto) {
                    $("#valor-total-pedido").append("<br/><span class='roboto normal size-1 color-dark-gray'>Ou no boleto com " + dados.descontoBoleto + "% de desconto R$ " + dados.valorBoleto + "</span>");
                }
            },
            error: function () {
            }
        });
    }

    /** FUNÇÃO PARA A TELA DE CARRINHO ONDE VALIDA O CUPOM DO DESCONTO E ADICIONA A SESSÃO DO CLIENTE **/
    function validarCupom(codigo) {
        $('#preloader-status').fadeIn();
        $('#preloader').fadeIn();
        $.ajax({
            url: "<?= HOME_URL ?>/carrinho/validarCupom",
            type: "POST",
            data: {
                codigo: codigo
            },
            success: function (retorno) {

                var dados = jQuery.parseJSON(retorno);
                if (dados.resposta == "VALIDO") {
                    $("#valor-cupom").html(dados.desconto);
                    $("#valor-total-pedido").html("<span><strong>Total: </strong>R$ " + dados.total);
                    $("#area-cupom").removeClass("has-error");
                    $("#area-cupom").addClass("has-success");
                    $(".area-resposta-cupom").hide();
                    location.reload();
                } else if (dados.resposta == "INVALIDO") {
                    $("#area-cupom").removeClass("has-success");
                    $("#area-cupom").addClass("has-error");
                    $(".area-resposta-cupom").show();
                    $("#valor-cupom").html("");
                    alert("Código do cupom inválido!");
                    location.reload();
                } else if (dados.resposta == "DESLOGADO") {
                    alert("Efetue o login para validar seu cupom!");
                    $('#preloader-status').fadeOut();
                    $('#preloader').fadeOut();
                }
            },
            error: function () {
            }
        });
    }

    /** FUNÇÃO PARA ATRIBUIR OS DADOS DE PRESENTE NO PRODUTO DO CARRINHO **/
    function adicionarPresente(indice) {

        mensagem = $('#modal-presente-' + indice + ' .mensagem-presente').val();
        $.ajax({
            url: "<?= HOME_URL ?>/carrinho/atribuirPresente",
            type: "POST",
            data: {
                indice: indice,
                mensagem: mensagem
            },
            success: function (retorno) {
                window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;
            },
            error: function () {
            }
        });
    }

    /** FUNÇÃO PARA TROCA DE VARIAÇÃO DO PRODUTO **/
    function mudarVariacao(idProduto, idVariacao, idVariacaoPai) {
        var liberado;
        var indisponivel;
        if ($("#variacao-valor-" + idVariacao).hasClass('variacao-ativa')) {
            return false;
        } else {
            $("#modalCarregando").modal({
                backdrop: false,
                show: true
            });
            $.ajax({
                url: "<?= HOME_URL ?>/produto/ajaxVariacao",
                type: "POST",
                data: {
                    produtoFK: idProduto,
                    variacaoFK: idVariacao,
                    variacaoPaiFK: idVariacaoPai
                },
                timeout: 2000,
                success: function (retorno) {
                    /** RESETAR CAMPOS **/
                    $(".liberado").each(function () {
                        $(this).addClass("aguarda-liberacao");
                        $(this).removeClass("liberado");
                    });
                    if (valorDe != "") {
                        $("#valorDe").html(valorDe);
                        $("#valorPor").html(valorPor);
//                        if (textoParcelamento) {
//                            $(".textoParcelamento").html(textoParcelamento);
//                        }
                    }
                    $("#quadro-botao-compra").removeAttr("data-toggle");
                    $("#quadro-botao-compra").removeAttr("data-placement");
                    $("#quadro-botao-compra").removeAttr("data-original-title");
                    $("#idVariacao").val("");
                    $("#area-parcelamento").html("");
                    dados = "";
                    if (liberado === true) {
                        $(".icone-variacao").removeClass('variacao-ativa');
                    }
                    if (indisponivel === true) {
                        $(".produto-indisponivel").hide();
                        $("#compra-flutuante").hide();
                        $(".produto-disponivel").show();
                        indisponivel = false;
                    }
                    liberado = false;
                    $(".variacao-pai-" + idVariacaoPai).removeClass('variacao-ativa');
                    $("#variacao-valor-" + idVariacao).addClass('variacao-ativa');
                    var dados = jQuery.parseJSON(retorno);
                    if (dados.imagemCorte) {
                        $(".quadro-imagem-produto-camisa").css({'background': 'url(' + dados.imagemCorte + ')'});
                        $(".quadro-imagem-produto-camisa").css({'background-position': 'center'});
                        $(".quadro-imagem-produto-camisa").css({'background-repeat': 'no-repeat'});
                        $(".quadro-imagem-produto-camisa").css({'background-size': '100% auto'});
                    }

                    if (dados.liberar === true) {
                        if (dados.indisponivel === true) {
                            $(".produto-indisponivel").show();
                            $(".produto-disponivel").hide();
                            indisponivel = true;
                        } else {
                            liberado = true;
                            $("#idVariacao").val(dados.variacao);
                            $("#botao-comprar").removeAttr("disabled");
                            $(".aguarda-liberacao").each(function () {
                                $(this).removeClass("aguarda-liberacao");
                                $(this).addClass("liberado");
                            });
                            $(".spinner").spinner({
                                min: 1,
                                max: dados.estoque
                            });
                            valorDe = $("#valorDe").html();
                            valorPor = $("#valorPor").html();
                            textoParcelamento = $("#textoParcelamento").html();
                            $("#valorDe").html(dados.valorDe);
                            $("#valorPor").html(dados.valorPor);
                            $("#textoParcelamento").html(dados.textoParcelamento);
                            $("#resultadoFrete").html("");
                            $("#area-parcelamento").html(dados.parcelamento);
                        }
                    }
                    $('#modalCarregando').modal('hide');
                    var selecionado = dados.selecionadas;
                    selecionado = JSON.parse(selecionado);
                    var tamanho = selecionado.length;
                    for (var i = 0; i < tamanho; i++) {
                        $("#variacao-valor-" + selecionado[i]).addClass('variacao-ativa');
                    }

                },
                error: function () {
                    $('#modalCarregando').modal('hide');
                    $("#alerta-erro").slideToggle();
                }
            });
        }
    }

    /** MUDAR A QUANTIDADE DO PRODUTO NO CARRINHO **/
    function mudarQuantidade(qtd, idCombinacao, indice) {
        $.ajax({
            url: "<?= HOME_URL ?>/carrinho/mudarQuantidade",
            type: "POST",
            data: {
                quantidade: qtd,
                variacaoFK: idCombinacao,
                indice: indice
            },
            success: function (retorno) {
                var dados = jQuery.parseJSON(retorno);
                if (dados.resultado === true) {
                    window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;
                } else {
                    $("#resposta-quantidade-" + indice).show();
                    $("#quadro-quantidade-" + indice).addClass("has-error");
                }
            },
            error: function () {
            }
        });
    }

    /** TELA DE FINALIZAÇÃO DE PEDIDO PARA MUDAR O ENDEREÇO DE ENTREGA **/
    function mudarEndereco(valor) {
        $('#preloader-status').fadeIn();
        $('#preloader').fadeIn();
        $.ajax({
            url: "<?= HOME_URL ?>/carrinho/mudarEndereco",
            type: "POST",
            data: {
                valor: valor
            },
            success: function () {
                location.reload();
            },
            error: function () {
            }
        });
    }

    /** TELA DE FINALIZAÇÃO DE PEDIDO PARA MUDAR O ENDEREÇO DE ENTREGA **/
    function mudarFrete(frete, valor, prazoDias) {
//        $("#modalCarregando").modal('show');
        $('#preloader-status').fadeIn();
        $('#preloader').fadeIn();
        $.ajax({
            url: "<?= HOME_URL ?>/carrinho/selecionarFrete",
            type: "POST",
            data: {
                valor: valor,
                prazoDias: prazoDias,
                frete: frete
            },
            success: function () {
                location.reload();
            },
            error: function () {
            }
        });
    }

    $(function () {
        $("button#submitEsqueciSenha").click(function () {
            if ($("#emailEsqueciSenha").val()) {
                $.ajax({
                    type: "POST",
                    url: "<?= HOME_URL ?>/cliente/esqueciSenha",
                    data: {
                        email: $("#emailEsqueciSenha").val()
                    },
                    success: function (retorno) {
                        if (retorno == "ENVIADO") {
                            $("#resultado-modal-erro").hide();
                            $("#resultado-modal-sucesso").show();
                        } else {
                            $("#resultado-modal-sucesso").hide();
                            $("#resultado-modal-erro").html("E-mail não encontrado!");
                            $("#resultado-modal-erro").show();
                        }
                    },
                    error: function () {
                        $("#resultado-modal-sucesso").hide();
                        $("#resultado-modal-erro").html("Houve um erro ao enviar o e-mail, por favor tente novamente mais tarde ou entre em contato.");
                        $("#resultado-modal-erro").show();
                    }
                });
            } else {
                $("#resultado-modal-sucesso").hide();
                $("#resultado-modal-erro").html("Por favor, informe o e-mail cadastrado em nosso site.");
                $("#resultado-modal-erro").show();
            }
        });
        $("button#submitIndicarSite").click(function () {
            if ($("#nomeAmigo2").val() && $("#emailAmigo2").val() && $("#nomeCliente2").val() && $("#emailCliente2").val()) {
                $.ajax({
                    type: "POST",
                    url: "<?= HOME_URL ?>/home/indicarSite",
                    data: $('form.indicarSite').serialize(),
                    success: function (retorno) {
                        $("#resultado-modal-erro2").hide();
                        $("#resultado-modal-sucesso2").show();
                    },
                    error: function () {
                        $("#resultado-modal-sucesso2").hide();
                        $("#resultado-modal-erro2").html("Houve um erro ao enviar o e-mail, por favor tente novamente mais tarde ou entre em contato.");
                        $("#resultado-modal-erro2").show();
                    }
                });
            } else {
                $("#resultado-modal-sucesso2").hide();
                $("#resultado-modal-erro2").html("Por favor, informe seus dados e os dados de seu amigo.");
                $("#resultado-modal-erro2").show();
            }
        });
        $("button#submitIndicarProduto").click(function () {
            if ($("#nomeAmigo").val() && $("#emailAmigo").val() && $("#nomeCliente").val() && $("#emailCliente").val()) {
                $.ajax({
                    type: "POST",
                    url: "<?= HOME_URL ?>/produto/indicarProduto",
                    data: $('form.indicarProduto').serialize(),
                    success: function (retorno) {
                        if (retorno == "ENVIADO") {
                            $("#resultado-modal-erro").hide();
                            $("#resultado-modal-sucesso").show();
                        } else {
                            $("#resultado-modal-sucesso").hide();
                            $("#resultado-modal-erro").html("E-mail não encontrado!");
                            $("#resultado-modal-erro").show();
                        }
                    },
                    error: function () {
                        $("#resultado-modal-sucesso").hide();
                        $("#resultado-modal-erro").html("Houve um erro ao enviar o e-mail, por favor tente novamente mais tarde ou entre em contato.");
                        $("#resultado-modal-erro").show();
                    }
                });
            } else {
                $("#resultado-modal-sucesso").hide();
                $("#resultado-modal-erro").html("Por favor, informe seus dados e os dados de seu amigo.");
                $("#resultado-modal-erro").show();
            }
        });
    });
</script>

<!-- FACEBOOK BUTTON -->
<div id="fb-root"></div>
<script>
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.3";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<!-- GOOGLE PLUS BUTTON -->
<script src="https://apis.google.com/js/platform.js" async defer>
    {
        lang: 'pt-BR'
    }
</script>

<? if (isset($analytics[0]["codigo"])) { ?>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', '<?= $analytics[0]["codigo"] ?>', 'auto');
        ga('require', 'displayfeatures');
        ga('send', 'pageview');</script>   
<? } ?>

<!-- GOOGLE ANALYTICS -->
<? if (isset($seo[0])) { ?>
    <? if ($seo[0]["codigo"] != "") { ?>
        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
            ga('create', '<?= $seo[0]["codigo"] ?>', 'auto');
            ga('require', 'displayfeatures');
            ga('send', 'pageview');</script>  
    <? } ?>
<? } ?>
<div id="fb-root"></div>
<script>
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.5";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>