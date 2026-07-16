<?php
//------------------- CLASSE SMARTY
include("libs/Smarty.class.php");
//------------------- CONFIG ACESSO BANCO DE DADOS
include("_config_db.inc.php");
//------------------- CONFIG ACESSO BANCO DE DADOS
session_save_path("/home/grupofas/tmp");

//------------------- SETA FORMATO HORARIO
date_default_timezone_set('America/Cuiaba');

//------------------- INFO SISTEMA
$Info = array(
    'title' => 'Grupo Fassil - Gerenciador',
    'title-topo' => 'Gerenciador'
);

//------------------- DEFINE IDENTIDADE DO SITE
define('SITENAME', 'Fassil - Ordem de Servi�0�4os');
define('SITEDESC', 'Descri�0�4�0�0o');

//------------------- DEFINE A BASE DO SITE
define('urlSite', 'https://www.grupofassil.com.br/');
define('urlBase', 'https://www.grupofassil.com.br/gerenciar/');

//------------------- DEFINE A BASE DA IMAGEM
define('urlMidia', 'https://www.grupofassil.com.br/gerenciar/midias');

//------------------- CAMINHOS URL ACTIVE
$Mod = explode('gerenciar/', $_SERVER['REQUEST_URI']);
$Mod = explode('/', $Mod[1]);
/*$Mod = array(
    $Mod[1],
    $Mod[2],
    $Mod[3]
);*/

//------------------- AUTO LOAD DE CLASSES
function __autoload($Class) {
     include('libs/'.$Class.'.class.php');
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

ob_start("mb_output_handler");

//------------------- REDIRECIONAMENTO URL
if ((explode('.', $_SERVER['HTTP_HOST'])[0] != 'www') && $_SERVER['SERVER_NAME'] != 'localhost'){
    header("Location:".urlBase);
}