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

//include($diretorio."_config_permissao.inc.php");
if (!isset($_SESSION['_user']) || $_SESSION['_user']['Usuario_Tipo'] != 'G') {
    header("Location:".urlBase."index.php");
}


$ajax = "ajax_menu_acao.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('ajax', $ajax);

$smarty->assign('lista', $Menu->Lista());
$smarty->display('sistema/menu/menu.html');