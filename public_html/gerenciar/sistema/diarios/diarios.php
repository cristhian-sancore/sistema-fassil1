<?php
$diretorio = "../../";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Menu = new Menu($Conn);
$Diarios = new Diarios($Conn);

include($diretorio."_config_permissao.inc.php");

$ajax = "ajax_diarios_acao.php";

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parametros = explode('?', $url);
if(isset($parametros[1])) {
    $parametros = $parametros[1];
}else{
    $parametros = null;
}

$pg = 1;
if(isset($_REQUEST['pg'])){
    $pg = $_REQUEST['pg'];
}

//$busca = (isset($_REQUEST['a']))?$_REQUEST['a']:null;
$cidade = (isset($_REQUEST['c']))?$_REQUEST['c']:null;
$automovel = (isset($_REQUEST['a']))?$_REQUEST['a']:null;
$usuario = (isset($_REQUEST['u']))?$_REQUEST['u']:null;
$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;
$data1 = (isset($_REQUEST['d1']))?$_REQUEST['d1']:null;
$data2 = (isset($_REQUEST['d2']))?$_REQUEST['d2']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['a'] = $automovel;
$_REQUEST['c'] = $cidade;
$_REQUEST['u'] = $usuario;
$_REQUEST['s'] = $status;
$_REQUEST['d1'] = $data1;
$_REQUEST['d2'] = $data2;
$_REQUEST['link'] = urlBase."$Mod[0]/$Mod[1]/diarios.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('ajax', $ajax);

$smarty->assign('Status', $status);
$smarty->assign('automoveis', $Diarios->Auto($_SESSION['_user']['Empresa_ID']));
$smarty->assign('usuarios', $Diarios->Usuarios($_SESSION['_user']['Empresa_ID']));
$smarty->assign('cidades', $Diarios->Cidades($_SESSION['_user']['Empresa_ID']));
$smarty->assign('Params', $parametros);
$smarty->assign('Automovel', $automovel);
$smarty->assign('Usuario', $usuario);
$smarty->assign('Cidade', $cidade);
$smarty->assign('Status', $status);
$smarty->assign('Data1', $data1);
$smarty->assign('Data2', $data2);
$smarty->assign('lista', $Diarios->Lista($_REQUEST));
$smarty->display('sistema/diarios/diarios.html');