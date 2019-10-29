<?

/**
 * Verifica chaves do array
 * 
 * Verifica se a chave existe no array e se ela tem algum valor.
 * 
 * @param array $array O array
 * @param string $key A chave do array
 * @return string/null O valor da chave do array ou nulo
 */
function chkArray($array, $key) {
    if (isset($array[$key]) && !empty($array[$key])) {
        return $array[$key];
    }

    return NULL;
}


/**
 * Verifica dados sobre o navegador e sistema do cliente
 * 
 * @return type
 */
function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

//First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    } else {
        $platform = 'Não Encontrada';
    }

// Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } else {
        $bname = 'Não encontrado';
        $ub = "Não encontrado";
    }

// finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        return array(
            'userAgent' => "Não encontrado",
            'name' => "Não encontrado",
            'version' => 0,
            'platform' => $platform,
            'pattern' => "Não encontrado"
        );
    }

// see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
//we will have two since we are not using 'other' argument yet
//see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

// check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern
    );
}

/**
 * Função pega o link que não esteja com http:// e retorna a URL completa
 * 
 * @param string $link Link sem o http://
 * @return string Link com o http://
 */
function corrigeLink($link) {
    if ($link) {
        if (!strstr($link, "http://")) {
            $link = "http://" . $link;
        }
        return $link;
    }
}

/**
 * Configura uma mensagem de boas vindas de acordo com o 
 * horario do dia
 * 
 * @return string Mensagem de boas vindas
 */
function boasVindas() {
    $hora = date("H");
    switch ($hora) {
        case ($hora > "18"):
            return "Boa noite";
            break;
        case ($hora > "12"):
            return "Boa tarde";
            break;
        default:
            return "Bom dia";
            break;
    }
}

/**
 * Função que deixa o valor em formato numérico de dinheiro
 * 
 * @param type $valor Valor
 * @param type $decimal Número de casas decimais
 * @return type Retorna o valor formatado
 */
function dinheiro($valor, $decimal = 2) {
    return number_format($valor, $decimal, ",", ".");
}

/**
 * Pega o link do vídeo no youtube e retorna o código do video
 * 
 * @param type $link Link completo do vídeo
 * @return array Código
 */
function video($link) {
    $str = $link;
    $regex = "#youtu(be.com|.b)(/v/|/watch\\?v=|e/|/watch(.+)v=)(.{11})#";
    preg_match_all($regex, $str, $matches);
    if (!empty($matches[4])) {
        $codigos_unicos = array();
        $quantidade_videos = count($matches[4]);
        foreach ($matches[4] as $code) {
            if (!in_array($code, $codigos_unicos))
                array_push($codigos_unicos, $code);
        }
        $codigo = $codigos_unicos[0];
    }
    return $codigo;
}

/**
 * Pega o id do vídeo e retorno o thumb
 * 
 * @param type $idVideo
 * @param type $fonte
 * @param type $titulo
 * @param type $largura
 * @param type $padding
 * @param type $center
 * @param type $return
 * @return type
 */
function videoThumb($idVideo, $fonte, $titulo = "", $largura = "", $padding = "", $center = false, $return = false) {
    if ($idVideo) {
        switch ($fonte) {
            case "YOUTUBE":
                $src = "http://img.youtube.com/vi/$idVideo/0.jpg";
                break;
            case "VIMEO":
                $dados = @simplexml_load_file("http://vimeo.com/api/v2/video/$idVideo.xml");
                $src = $dados->video->thumbnail_medium;
                break;
        }
        if ($src) {
            if ($padding) {
                $padding = "padding: $padding";
            }
            if ($return) {
                return $src;
            } else {
                if ($center) {
                    $resize = "onload='resizeToRatio(this, $largura, " . ($largura / 2) . ", 1, 1)'";
                } else {
                    $resize = "width = '$largura'";
                }
                ?>
                <img alt="<?= $titulo ?>" title="<?= $titulo ?>" src="<?= $src ?>" style="<?= $padding ?>" <?= $resize ?> class="videoThumb"/>
                <?
            }
        }
    }
}

function vtop($valor) {
    if ($valor <> "") {
        if (strstr($valor, ",")) {
            $valor = str_replace(".", "", $valor);
            $valor = str_replace(",", ".", $valor);
        }
    } else {
        $valor = 0;
    }

    return($valor);
}


/**
 * Função php para a validação de CEP
 * 
 * @param type $cep CEP
 * @return string/false Retorna o CEP caso esteja validado ou falso caso não
 */
function validaCep($cep) {
    $cep = trim($cep);
    $avaliaCep = ereg("^[0-9]{5}-[0-9]{3}$", $cep);
    if ($avaliaCep != true) {
        $avaliaCep = ereg("^[0-9]{8}$", $cep);
        if ($avaliaCep != true) {
            return FALSE;
        } else {
            return $cep;
        }
    } else {
        return $cep;
    }
}

/**
 * Função de validação de CNPJ
 * 
 * @param type $cnpj
 * @return boolean
 */
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

// Valida tamanho
    if (strlen($cnpj) != 14)
        return false;

// Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj{$i} * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
        return false;

// Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj{$i} * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}

/**
 * Função de validação de CPF
 * 
 * @param type $cpf
 * @return boolean
 */
function validarCPF($cpf = null) {

// Verifica se um cpf foi informado
    if (empty($cpf)) {
        return FALSE;
    }

// Elimina possivel mascara
    $cpf = preg_replace('[^0-9]', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
    $cpf = str_replace(array(".", "-"), "", $cpf);

// Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return FALSE;
    }
// Verifica se nenhuma das sequências invalidas abaixo 
// foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
        return FALSE;
// Calcula os digitos verificadores para verificar se o
// CPF é válido
    } else {

        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return FALSE;
            }
        }

        return TRUE;
    }
}

/**
 * Função para validação de email
 * 
 * @param type $email
 * @return boolean
 */
function validaEmail($email) {
    $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
    if (preg_match($er, $email)) {
        return TRUE;
    } else {
        return FALSE;
    }
}