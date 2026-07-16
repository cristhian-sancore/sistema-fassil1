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
$Empresas = new Empresas($Conn);
$Permissoes = new Permissoes($Conn);

include($diretorio."_config_permissao.inc.php");

$ac = strip_tags(trim($_REQUEST['ac']));
$id = 0;
if(isset($_REQUEST['id'])){
	$id = strip_tags(trim($_REQUEST['id']));
}

if($_POST){
	if($ac=='i' && in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])){
		$Permissoes->Grava($_POST);
	}elseif($ac=='e' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
		$Permissoes->Edita($_POST, $id);
	}

	header("Location:".urlBase."$Mod[0]/$Mod[1]/permissoes.php");
}else{
	//TOKEN
	include($diretorio . "_token.inc.php");
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);

$smarty->assign('ac', $ac);
$smarty->assign('modulos', $Menu->Modulos());
$smarty->assign('empresas', $Empresas->ListaEmpresas());
$smarty->assign('dados', $Permissoes->Dados($id));
$smarty->display('sistema/permissoes/permissoes_ie.html');