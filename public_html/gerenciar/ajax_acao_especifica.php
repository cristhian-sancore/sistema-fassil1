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

if(!isset($_SESSION['_user'])){
	echo("<script>location.href = '".urlBase."index.php';</script>");
}

if(isset($_REQUEST['ac'])){
	$ac = strip_tags(trim($_REQUEST['ac']));
	$id = strip_tags(trim($_REQUEST['id']));

	if($ac=='d'){
		$Menu->Deleta($id);
	}elseif($ac=='a'){
		$Menu->Ativar($id);
	}

	echo("<script>location.href = '".urlBase."$Mod[0]/$Mod[1]/menu.php';</script>");
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);

$smarty->assign('lista', $Menu->Lista());
$smarty->display('sistema/menu/menu.html');