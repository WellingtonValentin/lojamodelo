<?

function converte($term, $tp) {
    if ($tp == "1") {
        $palavra = strtr(strtoupper($term), "������������������������������", "������������������������������");
    } elseif ($tp == "0") {
        $palavra = strtr(strtolower($term), "������������������������������", "������������������������������");
    }
    return $palavra;
}

function minuscula($cadeia) {
    $cadeia = strtolower($cadeia);
    $min = array(0 => "�", 1 => "�", 2 => "�", 3 => "�", 4 => "�", 5 => "�", 6 => "�", 7 => "�",
        8 => "�", 9 => "�", 10 => "�", 11 => "�", 12 => "�", 13 => "�", 14 => "�",
        15 => "�", 16 => "�", 17 => "�", 18 => "�", 19 => "�", 20 => "�", 21 => "�", 22 => "�",
    );
    $mai = array(0 => "�", 1 => "�", 2 => "�", 3 => "�", 4 => "�", 5 => "�", 6 => "�", 7 => "�",
        8 => "�", 9 => "�", 10 => "�", 11 => "�", 12 => "�", 13 => "�", 14 => "�",
        15 => "�", 16 => "�", 17 => "�", 18 => "�", 19 => "�", 20 => "�", 21 => "�", 22 => "�",
    );

    for ($i = 0; $i < 23; $i++) {
        $cadeia = str_replace($mai[$i], $min[$i], $cadeia);
    }
    return $cadeia;
}

function retiraAcentos($name) {
    $array1 = array("�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�"
        , "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", " ", "'", "�", "`", "/", "\\", "~", "^", "�");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
        , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "-", "_", "_", "_", "_", "_", "_", "_", "_");
    return str_replace($array1, $array2, $name);
}

function invalidCaracter($var) {
    $tam = 250;
    $sizeName = strlen($var);
    $a = "����������������������������& -!@#$%�&*()_+}=}{[]^~?/:;><,'�`\"";
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
