function Loader() {
    this.timeout = null;
    $('#divLoadAll').dialog({
        modal: true,
        height: 110,
        closeOnEscape: false,
        autoOpen: false
    });
    this.loading = function () {
        this.timeout = setTimeout(function () {
            $('#divLoadAll').dialog("open");
            $(".ui-dialog-titlebar-close").hide();
        }, 300);
    }
    this.loaded = function () {
        clearTimeout(this.timeout);
        if ($("#divLoadAll").dialog("isOpen") === true) {
            $('#divLoadAll').dialog("close");
        }
    }
}
function carregaCidades(estadoFK, pathSite, cidadeSelecionada) {
    var loader = new Loader();
    loader.loading();
    return $.ajax({
        url: pathSite + 'ajax/cidades.php',
        type: 'POST',
        data: {estadoFK: estadoFK, ajax: 1},
        timeout: 2000,
        success: function (retorno) {
            $('#cidades').html(retorno);
        },
        error: function () {
            alert('Erro ao consultar cidades. Tente novamente.');
        },
        complete: function () {
            loader.loaded();
            if (!cidadeSelecionada) {
                carregaBairros('', pathSite);
            }
        }
    });
}
function carregaBairros(cidadeFK, pathSite, bairroSelecionado) {
    var loader = new Loader();
    loader.loading();
    trocaCidade(cidadeFK);
    return $.ajax({
        url: pathSite + 'ajax/bairros.php',
        type: 'POST',
        data: {cidadeFK: cidadeFK, ajax: 1},
        timeout: 2000,
        success: function (retorno) {
            $('#bairros').html(retorno);
        },
        error: function () {
            alert('Erro ao consultar bairros. Tente novamente.');
        },
        complete: function () {
            loader.loaded();
            if (!bairroSelecionado) {
                trocaBairro('', pathSite);
            }
        }
    });
}

function trocaCidade(cidadeFK) {
    if (cidadeFK != "-1") {
        $('#outraCidade').hide();
    } else {
        $('#outraCidade').show();
    }
}
function trocaBairro(bairroFK) {
    if (bairroFK != "-1") {
        $('#outroBairro').hide();
    } else {
        $('#outroBairro').show();
    }
}

function carregaMarcas(tipo, pathSite) {
    var loader = new Loader();
    loader.loading();
    $.ajax({
        url: pathSite + 'ajax/marcas.php',
        type: 'POST',
        data: {tipo: tipo, ajax: 1},
        timeout: 2000,
        success: function (retorno) {
            $('#marcas').html(retorno);
        },
        error: function () {
            alert('Erro ao consultar modelos. Tente novamente.');
        },
        complete: function () {
            loader.loaded();
            carregaModelos('', pathSite);
        }
    });
}

function trocaEmpresaCategoria(empresaCategoriaFK) {
    if (empresaCategoriaFK != "-1") {
        $('#outroImovelTipo').hide();
    } else {
        $('#outroImovelTipo').show();
    }
}
function addOpcional() {
    var clone = $('#outrosOpcionais li').first().clone();
    clone.find('input').val('');
    clone.appendTo('#outrosOpcionais');
}

function initializeDestaques() {
    $("#container").wtRotator({
        width: 340,
        height: 225,
        thumb_width: 24,
        thumb_height: 24,
        button_width: 24,
        button_height: 24,
        button_margin: 5,
        auto_start: true,
        delay: 5000,
        play_once: false,
        transition: "fade",
        transition_speed: 800,
        auto_center: true,
        easing: "",
        cpanel_position: "inside",
        cpanel_align: "BR",
        timer_align: "top",
        display_thumbs: false,
        display_dbuttons: false,
        display_playbutton: false,
        display_thumbimg: false,
        display_side_buttons: true,
        display_numbers: true,
        display_timer: true,
        mouseover_select: false,
        mouseover_pause: true,
        cpanel_mouseover: false,
        text_mouseover: false,
        text_effect: "fade",
        text_sync: true,
        tooltip_type: "text",
        shuffle: false,
        block_size: 75,
        vert_size: 55,
        horz_size: 50,
        block_delay: 25,
        vstripe_delay: 75,
        hstripe_delay: 180
    });
}
function completaEndereco(cep, pathSite) {
    var loader = new Loader();
    loader.loading();
    $.ajax({
        url: pathSite + 'ajax/buscaCep.php',
        timeout: 5000,
        type: 'POST',
        dataType: 'JSON',
        data: {'cep': cep},
        success: function (retorno) {
            if (retorno) {
                $('.completaLogradouro').val(retorno.logradouro);
                $('.completaOutroBairro').val(retorno.bairro);
                $('.completaOutraCidade').val(retorno.cidade);

                $('.completaEstado').val(retorno.estadoFK);
                var carregandoCidade = carregaCidades(retorno.estadoFK, pathSite, true);
                carregandoCidade.done(function () {
                    $('.completaCidade').val(retorno.cidadeFK);
                    trocaCidade(retorno.cidadeFK);
                    var carregandoBairro = carregaBairros(retorno.cidadeFK, pathSite, true);
                    carregandoBairro.done(function () {
                        $('.completaBairro').val(retorno.bairroFK);
                        trocaBairro(retorno.bairroFK);
                    })
                });
            } else {
                alert('Cep Inválido. Impossível Completar Endereço.');
            }
        },
        error: function () {
            alert('Erro ao buscar CEP. Tente Novamente.');
        },
        complete: function () {
            loader.loaded();
        }
    });
}
function marcaTodos(classe, checked) {
    $('.' + classe).each(function (cont, element) {
        $(element).prop("checked", checked)
    })
}

function number_format(number, decimals, dec_point, thousands_sep) {

    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}

function maiuscula(z) {
    v = z.value.toUpperCase();
    z.value = v;
}