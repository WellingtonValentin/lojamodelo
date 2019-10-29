<?php

/**
 * Inverte datas 
 *
 * Obtém a data e inverte seu valor.
 * De: d-m-Y H:i:s para Y-m-d H:i:s ou vice-versa.
 *
 * @access public
 * @param string $data A data
 */
function inverteData($data = null) {

// Configura uma variável para receber a nova data
    $nova_data = null;

// Se a data for enviada
    if ($data) {

// Explode a data por -, /, : ou espaço
        $data = preg_split('/\-|\/|\s|:/', $data);

// Remove os espaços do começo e do fim dos valores
        $data = array_map('trim', $data);

// Cria a data invertida
        $nova_data .= chkArray($data, 2) . '-';
        $nova_data .= chkArray($data, 1) . '-';
        $nova_data .= chkArray($data, 0);

// Configura a hora
        if (chkArray($data, 3)) {
            $nova_data .= ' ' . chkArray($data, 3);
        }

// Configura os minutos
        if (chkArray($data, 4)) {
            $nova_data .= ':' . chkArray($data, 4);
        }

// Configura os segundos
        if (chkArray($data, 5)) {
            $nova_data .= ':' . chkArray($data, 5);
        }
    }

// Retorna a nova data
    return $nova_data;
}

function dataMysql($data) {
    if ($data) {
        $data = explode("/", $data);
        $data = $data[2] . "-" . $data[1] . "-" . $data[0];
    } else {
        $data = "0000-00-00";
    }
    return($data);
}

function dataSite($data) {
    if ($data) {
        $data = explode("-", $data);
        $data = $data[2] . "/" . $data[1] . "/" . $data[0];
    } else {
        $data = "";
    }
    return($data);
}

function dataHoraMysql($dataHora) {
    if ($dataHora <> "") {
        $dataHora = explode(" ", $dataHora);
        $data = explode("/", $dataHora[0]);
        $data = $data[2] . "-" . $data[1] . "-" . $data[0];
        $dataHora = $data . " " . $dataHora[1];
    } else {
        $dataHora = "NULL";
    }
    return $dataHora;
}

function dataHoraSite($dataHora) {
    if ($dataHora <> "") {
        $dataHora = explode(" ", $dataHora);
        $dataHora["0"] = dataSite($dataHora["0"]);
        $dataHora = implode(" ", $dataHora);
    } else {
        $dataHora = "";
    }
    return $dataHora;
}

function dataPorExtenso($cidade) {
    $texto .= $cidade . ", " . date("d") . " de " . mes(date("m")) . " de " . date("Y");
    return $texto;
}

function mes($mes) {
    switch ($mes) {
        case 1: $mes = "Janeiro";
            break;
        case 2: $mes = "Fevereiro";
            break;
        case 3: $mes = "Março";
            break;
        case 4: $mes = "Abril";
            break;
        case 5: $mes = "Maio";
            break;
        case 6: $mes = "Junho";
            break;
        case 7: $mes = "Julho";
            break;
        case 8: $mes = "Agosto";
            break;
        case 9: $mes = "Setembro";
            break;
        case 10: $mes = "Outubro";
            break;
        case 11: $mes = "Novembro";
            break;
        case 12: $mes = "Dezembro";
            break;
    }
    return $mes;
}

function mesCurto($mes) {
    switch ($mes) {
        case 1: $mes = "Jan";
            break;
        case 2: $mes = "Fev";
            break;
        case 3: $mes = "Mar";
            break;
        case 4: $mes = "Abr";
            break;
        case 5: $mes = "Mai";
            break;
        case 6: $mes = "Jun";
            break;
        case 7: $mes = "Jul";
            break;
        case 8: $mes = "Ago";
            break;
        case 9: $mes = "Set";
            break;
        case 10: $mes = "Out";
            break;
        case 11: $mes = "Nov";
            break;
        case 12: $mes = "Dez";
            break;
    }
    return $mes;
}