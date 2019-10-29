<?php
/**
 * Configuraчѕes Gerais
 */

// Caminho da pasta raiz
define("ABSPATH", dirname(__FILE__));

// Caminho para a pasta de uploads
define("UP_ABSPATH", ABSPATH . "/view/_upload");

// Caminho para a pasta de imagens
define("IMG_ABSPATH", ABSPATH . "/view/_image");

// URL da home
//define("HOME_URL", "http://192.168.1.206/lojamodelo/");
define("HOME_URL", "http://www.lojamodelo.com.br");

// URL da pasta de upload
define("UPLOAD_URL", HOME_URL . "/view/_upload");

// URL da pasta de imagens
define("IMG_URL", HOME_URL . "/view/_image");

// Nome do host da base de dados
define("HOSTNAME", "mysql.bancoloja.com.br");

// Nome do banco
define("DB_NAME", "dbname");

// Usuсrio do banco
define("DB_USER", "user");

// Senha do banco
define("DB_PASSWORD", "secret");

// Charset da conexуo
define("DB_CHARSET", "utf8");

// Se vocъ estiver desenvolvendo, modifique o valor para true
define("DEBUG", TRUE);

// Carrega o loader que vai carregar a aplicaчуo inteira
require_once ABSPATH . '/loader.php';
