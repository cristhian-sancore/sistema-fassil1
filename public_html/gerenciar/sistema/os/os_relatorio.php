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
$OrdemServicos = new OrdemServicos($Conn);
$Relatorios = new Relatorios($Conn);

include($diretorio."_config_permissao.inc.php");

$ac = (isset($_REQUEST['ac']))?$_REQUEST['ac']:null;
$id = (isset($_REQUEST['id']))?strip_tags(trim($_REQUEST['id'])):0;

//VERIFICA STATUS DA NOTA
/*$dd = $OrdemServicos->Dados($id);
if($dd['Status_ID']<3){
	header("Location:".urlBase."$Mod[0]/$Mod[1]/os.php");
}*/

if($_POST){
	if(($ac=='i' || $ac=='e') && in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])){
		$Relatorios->Verifica_Relatorio($_POST);
	}

	//header("Location:".urlBase."$Mod[0]/$Mod[1]/os.php");
}else{
	//TOKEN
	include($diretorio . "_token.inc.php");
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('urlSite', urlSite);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);

$smarty->assign('ac', $ac);
$smarty->assign('data', date('d/m/Y'));
$smarty->assign('horas', date('H:i'));
$smarty->assign('dados', $OrdemServicos->Dados($id));
$smarty->assign('rel', $Relatorios->Dados($id));
$smarty->display('sistema/os/os_relatorio.html');