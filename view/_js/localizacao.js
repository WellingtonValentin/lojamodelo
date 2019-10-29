function carregarLocalizacao(endereco, titulo, descricao, idElemento, lat, long) {
    if ($("#" + idElemento).length === 0) {
        return false;
    }
    var myOptions = {
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false
    };
    if (lat && long) {
        var latlong = new google.maps.LatLng(parseFloat(lat), parseFloat(long));
    }
    map = new google.maps.Map(document.getElementById(idElemento), myOptions);

    geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': endereco}, function(results, status) {
        if (!latlong) {
            latlong = results[0].geometry.location;
        }
        map.setCenter(latlong);
    });
    marcaMapa(endereco, titulo, descricao, callback, latlong);
    function callback(valor, title, content) {
        var mark = new google.maps.Marker({
            position: valor,
            map: map,
            title: title,
            icon: ''
        });
        var infowindow = new google.maps.InfoWindow({
            content: content
        });
        google.maps.event.addListener(mark, 'click', function(event) {
            infowindow.open(map, mark);
        });
        infowindow.open(map, mark);
    }
}

function marcaMapa(endereco, title, content, callback, latlong) {
    geocoder = new google.maps.Geocoder();
    if (!latlong) {
        geocoder.geocode({'address': endereco}, function(results, status) {
            if (status = google.maps.GeocoderStatus.OK) {
                callback(results[0].geometry.location, title, content);
            }
        });
    } else {
        callback(latlong, title, content);
    }
}