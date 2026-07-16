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
$Categoria = new Categoria($Conn);

include($diretorio."_config_permissao.inc.php");

$ac = (isset($_REQUEST['ac']))?$_REQUEST['ac']:null;
$id = (isset($_REQUEST['id']))?strip_tags(trim($_REQUEST['id'])):0;

if($_POST){
	if($ac=='i' && in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])){
        $Categoria->Grava($_POST);
	}elseif($ac=='e' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
        $Categoria->Edita($_POST, $id);
	}

	header("Location:".urlBase."$Mod[0]/$Mod[1]/categorias.php");
}else{
	//TOKEN
	include($diretorio . "_token.inc.php");
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);

$smarty->assign('categoria_ie', $Categoria->ListaIE());
$smarty->assign('ac', $ac);
$smarty->assign('dados', $Categoria->Dados($id));
$smarty->display('website/categorias/categorias_ie.html');