function resizeToRatio(imagem, wid, hei, centralizar) {
    var largura = $(imagem).width();
    var altura = $(imagem).height();
    if (largura >= altura && wid !== 0) {
        $(imagem).width(wid);
    } else {
        $(imagem).height(hei);
    }
    if (centralizar) {
        $(imagem).css({'position': 'absolute', 'top': '50%', 'left': '50%', 'margin-top': '-' + $(imagem).height() / 2 + 'px', 'margin-left': '-' + $(imagem).width() / 2 + 'px'});
    }
}
function resizeToRatioCut(imagem, wid, hei, centralizarH, centralizarV) {
    $(imagem).width("");
    $(imagem).height("");
    var largura = $(imagem).width();
    var altura = $(imagem).height();
    if (largura <= altura) {
        $(imagem).width(wid);
        if ($(imagem).height() < hei) {
            $(imagem).css({'width': 'auto'});
            $(imagem).height(hei);
        }
    } else {
        $(imagem).height(hei);
        if ($(imagem).width() < wid) {
            $(imagem).css({'height': 'auto'});
            $(imagem).width(wid);
        }
    }
    $(imagem).addClass('resized');
    $(imagem).attr('data-center-h', centralizarH);
    $(imagem).attr('data-center-v', centralizarV);
    var top = '0';
    var left = '0';
    var marginTop = '0';
    var marginLeft = '0';
    if (centralizarH) {
        left = '50%';
        marginLeft = '-' + $(imagem).width() / 2 + 'px';
    }
    if (centralizarV) {
        top = '50%';
        marginTop = '-' + $(imagem).height() / 2 + 'px';
    }
    if (centralizarH) {
        $(imagem).css({
            'position': 'absolute',
            'top': top,
            'left': left,
            'margin-top': marginTop,
            'margin-left': marginLeft
        });
    }
}
function resizeToRatioUncut(imagem, wid, hei, centralizarH, centralizarV) {
    $(imagem).width("");
    $(imagem).height("");
    var largura = $(imagem).width();
    var altura = $(imagem).height();
    if (largura >= altura) {
        $(imagem).width(wid);
        if ($(imagem).height() > hei) {
            $(imagem).css({'width': 'auto'});
            $(imagem).height(hei);
        }
    } else {
        $(imagem).height(hei);
        if ($(imagem).width() > wid) {
            $(imagem).css({'height': 'auto'});
            $(imagem).width(wid);
        }
    }
    $(imagem).addClass('resized');
    $(imagem).attr('data-center-h', centralizarH);
    $(imagem).attr('data-center-v', centralizarV);
    var top = '0';
    var left = '0';
    var marginTop = '0';
    var marginLeft = '0';
    if (centralizarH) {
        left = '50%';
        marginLeft = '-' + $(imagem).width() / 2 + 'px';
    }
    if (centralizarV) {
        top = '50%';
        marginTop = '-' + $(imagem).height() / 2 + 'px';
    }
    if (centralizarH) {
        $(imagem).css({
            'position': 'absolute',
            'top': top,
            'left': left,
            'margin-top': marginTop,
            'margin-left': marginLeft
        });
    }
}