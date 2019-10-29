<?php

require_once '../../function/function-global.php';

// Inicia a sessão
session_start();

if (chkArray($_POST, "limite")) {
    $_SESSION["FILTRAR"]["LIMITE"] = $_POST["limite"];
} elseif (chkArray($_POST, "ordem")) {
    $_SESSION["FILTRAR"]["ORDEM"] = $_POST["ordem"];
} elseif (chkArray($_POST, "faixa")) {
    if ($_POST["faixa"] == "t") {
        unset($_SESSION["FILTRAR"]["FAIXA"]);
    } else {
        $_SESSION["FILTRAR"]["FAIXA"] = $_POST["faixa"];
    }
} elseif (chkArray($_POST, "prontaEntrega")) {
    if ($_POST["prontaEntrega"] == "t") {
        unset($_SESSION["FILTRAR"]["PRONTAENTREGA"]);
    } else {
        $_SESSION["FILTRAR"]["PRONTAENTREGA"] = $_POST["prontaEntrega"];
    }
}