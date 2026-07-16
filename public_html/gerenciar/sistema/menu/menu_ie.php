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

$ac = strip_tags(trim($_REQUEST['ac']));
$id = 0;
if(isset($_REQUEST['id'])){
	$id = strip_tags(trim($_REQUEST['id']));
}

if($_POST){
	if($ac=='i' /*&& in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])*/){
		$Menu->Grava($_POST);
	}elseif($ac=='e' /*&& in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])*/){
		$Menu->Edita($_POST, $id);
	}

	header("Location:".urlBase."$Mod[0]/$Mod[1]/menu.php");
}else{
	//TOKEN
	include($diretorio . "_token.inc.php");
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);

$smarty->assign('menu_ie', $Menu->ListaIE());
$smarty->assign('ac', $ac);
$smarty->assign('dados', $Menu->Dados($id));
$smarty->display('sistema/menu/menu_ie.html');