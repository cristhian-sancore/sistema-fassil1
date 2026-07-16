<?php
//------------------- CLASSE SMARTY
include("libs/Smarty.class.php");
//------------------- CONFIG ACESSO BANCO DE DADOS
include("_config_db.inc.php");
//------------------- CAMINHO SESSION SERVIDOR
if (!is_dir("/tmp/sessions")) {
    @mkdir("/tmp/sessions", 0777, true);
}
session_save_path("/tmp/sessions");

//------------------- SETA FORMATO HORARIO
date_default_timezone_set('America/Cuiaba');

//------------------- INFO SISTEMA
$Info = array(
    'title' => 'Grupo Fassil - Gerenciador',
    'title-topo' => 'Gerenciador'
);

//------------------- DEFINE IDENTIDADE DO SITE
define('SITENAME', 'Fassil - Ordem de Serviços');
define('SITEDESC', 'Descrição');

//------------------- DEFINE A BASE DO SITE (Dinamico para suportar proxy reverso SSL/nginx e Docker)
$protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? "https" : "http";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8055';
define('urlSite', $protocol . '://' . $host . '/');
define('urlBase', $protocol . '://' . $host . '/gerenciar/');

//------------------- DEFINE A BASE DA IMAGEM
define('urlMidia', $protocol . '://' . $host . '/gerenciar/midias');

//------------------- CAMINHOS URL ACTIVE
$Mod = explode('gerenciar/', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
$Mod = isset($Mod[1]) ? explode('/', $Mod[1]) : array('');

//------------------- AUTO LOAD DE CLASSES
function __autoload($Class) {
     if (file_exists('libs/'.$Class.'.class.php')) {
         include('libs/'.$Class.'.class.php');
     }
}
spl_autoload_register('__autoload');

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