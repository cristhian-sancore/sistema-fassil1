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
$Usuarios = new Usuarios($Conn);
$Permissoes = new Permissoes($Conn);

include($diretorio."_config_permissao.inc.php");

$id = $_SESSION['_user']['Usuario_ID'];

$dados = $Usuarios->Dados($id);

if($_POST){
	if(in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
		$_POST['permissao'] = $dados['Permissao_ID'];
		$_POST['login'] = $dados['Usuario_Login'];
		$Usuarios->Edita($_POST, $id);
	}

	header("Location:".urlBase."$Mod[0]/$Mod[1]/dados.php");
}else{
	//TOKEN
	include($diretorio . "_token.inc.php");
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);

$smarty->assign('dados', $dados);
$smarty->display('sistema/dados/dados.html');