<?php
$diretorio = "";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Menu = new Menu($Conn);
$Estatisticas = new Estatisticas($Conn);
$OrdemServicos = new OrdemServicos($Conn);

if(!isset($_SESSION['_user'])){
	header("Location:".urlBase."index.php");
}

$pg = (isset($_REQUEST['pg']))?$_REQUEST['pg']:1;
$ac = (isset($_REQUEST['ac']))?$_REQUEST['ac']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['link'] = urlBase."dashboard.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);

$smarty->assign('os', $Estatisticas->Lista('OS_ID', 'os'));
$smarty->assign('os2', $Estatisticas->Lista('OS_ID', 'os', 'Status_ID=1'));
$smarty->assign('os3', $Estatisticas->Lista('OS_ID', 'os', null, 'OS_DataInicio'));

$smarty->assign('cl', $Estatisticas->Lista('Cliente_ID', 'clientes'));
$smarty->assign('cl2', $Estatisticas->Lista('Cliente_ID', 'clientes', 'Cliente_Status=1'));
$smarty->assign('cl3', $Estatisticas->Lista('Cliente_ID', 'clientes', 'Cliente_Status=0'));

$smarty->assign('ac', $ac);
$smarty->assign('lista', $OrdemServicos->Lista($_REQUEST));

$smarty->display('dashboard.html');