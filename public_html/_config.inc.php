<?php
//------------------- CLASSE SMARTY
include("libs/Smarty.class.php");
//------------------- CAMINHO SESSION SERVIDOR
if (!is_dir("/tmp/sessions")) {
    @mkdir("/tmp/sessions", 0777, true);
}
session_save_path("/tmp/sessions");

//------------------- SETA FORMATO HORARIO
date_default_timezone_set('America/Cuiaba');
header("Content-Type: text/html; charset=utf-8");
if (function_exists('mb_internal_encoding')) { mb_internal_encoding("UTF-8"); }

//------------------- INFO SISTEMA
$Info = array(
    'title' => 'Grupo Fassil',
);

//------------------- CONFIGURACOES DO BANCO
define('HOST', getenv('DB_HOST') ?: 'db');
define('USER', getenv('DB_USER') ?: 'grupofas_admin');
define('PASS', getenv('DB_PASS') ?: 'fassil3017#');
define('DBSA', getenv('DB_NAME') ?: 'grupofas_sistema');

//------------------- DEFINE A BASE DO SITE
$protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? "https" : "http";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8080';
define('urlSite', $protocol . '://' . $host . '/');
define('urlBase', $protocol . '://' . $host . '/');

//------------------- DEFINE A BASE DA IMAGEM
define('urlMidia', 'midias');

//------------------- CAMINHOS URL ACTIVE
$Mod = explode('gerenciar/', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
$Mod = isset($Mod[1]) ? explode('/', $Mod[1]) : array('');

//------------------- AUTOLOAD DE CLASSES
function grupofas_autoload($Class) {
    $diretorios = array(__DIR__ . '/libs/', 'libs/');
    foreach ($diretorios as $valor) {
        if (file_exists($valor . $Class . '.class.php')) {
            require_once $valor . $Class . '.class.php';
            break;
        }
    }
}
spl_autoload_register('grupofas_autoload');

//------------------- TRATAMENTO DE ERROS
//CSS constantes :: Mensagens de Erro
define('ACCEPT', 'accept');
define('INFOR', 'infor');
define('ALERT', 'alert');
define('ERROR', 'error');

//------------------- PHPErro :: personaliza o gatilho do PHP
function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? INFOR : ($ErrNo == E_USER_WARNING ? ALERT : ($ErrNo == E_USER_ERROR ? ERROR : $ErrNo)));
    echo "<p class=\"trigger {$CssClass}\">";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class=\"ajax_close\"></span></p>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}
set_error_handler('PHPErro');

mb_internal_encoding("UTF-8");
ob_start("mb_output_handler");