<?

function converte($term, $tp) {
    if ($tp == "1") {
        $palavra = strtr(strtoupper($term), "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß");
    } elseif ($tp == "0") {
        $palavra = strtr(strtolower($term), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß", "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
    }
    return $palavra;
}

function minuscula($cadeia) {
    $cadeia = strtolower($cadeia);
    $min = array(0 => "á", 1 => "à", 2 => "ã", 3 => "â", 4 => "ä", 5 => "é", 6 => "è", 7 => "ê",
        8 => "ë", 9 => "í", 10 => "ì", 11 => "î", 12 => "ï", 13 => "ó", 14 => "ò",
        15 => "ô", 16 => "õ", 17 => "ö", 18 => "ú", 19 => "ù", 20 => "û", 21 => "ü", 22 => "ç",
    );
    $mai = array(0 => "Á", 1 => "À", 2 => "Ã", 3 => "Â", 4 => "Ä", 5 => "É", 6 => "È", 7 => "Ê",
        8 => "Ë", 9 => "Í", 10 => "Ì", 11 => "Î", 12 => "Ï", 13 => "Ó", 14 => "Ò",
        15 => "Ô", 16 => "Õ", 17 => "Ö", 18 => "Ú", 19 => "Ù", 20 => "Û", 21 => "Ü", 22 => "Ç",
    );

    for ($i = 0; $i < 23; $i++) {
        $cadeia = str_replace($mai[$i], $min[$i], $cadeia);
    }
    return $cadeia;
}

function retiraAcentos($name) {
    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
        , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", " ", "'", "´", "`", "/", "\\", "~", "^", "¨");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
        , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "-", "_", "_", "_", "_", "_", "_", "_", "_");
    return str_replace($array1, $array2, $name);
}

function invalidCaracter($var) {
    $tam = 250;
    $sizeName = strlen($var);
    $a = "ÁáÉéÍíÓóÚúÇçÃãÀàÂâÊêÎîÔôÕõÛû& -!@#$%¨&*()_+}=}{[]^~?/:;><,'´`\"";
    $b = "AaEeIiOoUuCcAaAaAaEeIiOoOoUue--_______________________________";
    $var = strtr($var, $a, $b);
    $var = strtolower($var);
    if ($sizeName > $tam) {
        $var = substr($var, 0, $tam);
    }
    return $var;
}

function quebrarTexto($texto, $tamanho) {
    $texto = str_replace("&agrave;", "a`", $texto);
    $texto = strip_tags($texto);
    $texto = html_entity_decode($texto);
    $texto = trim($texto);
    $texto = str_replace("a`", "&agrave;", $texto);

    $vetor = explode(" ", $texto);

    $cont = 0;
    $resTxt = "";
    foreach ($vetor as $valor) {
        $cont += strlen($valor) + 1;

        if ($tamanho >= $cont)
            $resTxt .= $valor . " ";
        else
            break;
    }
    return $resTxt . ((strlen($texto) > $tamanho) ? ". . ." : "");
}

function arrumaString($nome) {
    return retiraAcentos(minuscula(invalidCaracter($nome)));
}

function corrigeAcentos($string) {

    $eUTF = mb_detect_encoding($string, 'UTF-8', true);
    if (!$eUTF) {
        return utf8_encode($string);
    }
    
    return $string;
}

function leString($string, $array) {
    foreach ($array as $ind => $valor) {
        $$ind = $valor;
    }
    $pattern = "/<#(\w+)#>/e";
    $replace = "$$1";
    return preg_replace($pattern, $replace, $string);
}
