<?php
$diretorio = "../../";
include($diretorio . "_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Menu = new Menu($Conn);
$Clientes = new Clientes($Conn);
$Estados = new Estados($Conn);
$Email = new Email($Conn);

include($diretorio."_config_permissao.inc.php");

$ac = strip_tags(trim($_REQUEST['ac']));
$id = 0;
if(isset($_REQUEST['id'])){
	$id = strip_tags(trim($_REQUEST['id']));
}

if($_POST){
	if($ac=='i' && in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])){
		$Cliente_ID = $Clientes->Grava($_POST);

		/*ENVIA EMAIL*/
		if($Cliente_ID && $_SERVER['SERVER_NAME']!='localhost') {
			$dd = $Clientes->Dados($Cliente_ID);

			$dd['url'] = urlBase;
			$dd['senha'] = $_POST['senha'];
			$smarty->assign('dados', $dd);
			$template = $smarty->fetch('sistema/clientes/_email_cadastro.html');

			$teste = $Email->Cadastro($dd, $template);
		}
	}elseif($ac=='e' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
		$Clientes->Edita($_POST, $id);
	}

	header("Location:".urlBase."$Mod[0]/$Mod[1]/clientes.php");
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
$smarty->assign('estados', $Estados->Lista());
$smarty->assign('dados', $Clientes->Dados($id));
$smarty->display('sistema/clientes/clientes_ie.html');