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
$Estados = new Estados($Conn);
$Email = new Email($Conn);



include($diretorio."_config_permissao.inc.php");

$ac = (isset($_REQUEST['ac']))?$_REQUEST['ac']:null;
$id = (isset($_REQUEST['id']))?strip_tags(trim($_REQUEST['id'])):0;

//VERIFICA STATUS DA NOTA
$dd = $OrdemServicos->Dados($id);
$s = (isset($dd['Status_ID']))?$dd['Status_ID']:0;
if($s==3){
	header("Location:".urlBase."$Mod[0]/$Mod[1]/os.php");
}

if($_POST){
	if($ac=='i' && in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])){
		$_POST['empresa_id'] = $_SESSION['_user']['Empresa_ID'];
		$id = $OrdemServicos->Grava($_POST);

		/*ENVIA EMAIL*/
		if($id && $_SERVER['SERVER_NAME']!='localhost'){
			$dd = $OrdemServicos->Dados($id);
			$dd['url'] = urlSite;

			$smarty->assign('dados', $dd);
			$template = $smarty->fetch('sistema/os/_email_ticket.html');

			$Email->Ticket($dd, $template);
		}
	}elseif($ac=='e' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
		$OrdemServicos->Edita($_POST, $id);
	}

	if(isset($_POST['finalizar'])){
		header("Location:".urlBase."sistema/os/os_view.php?ac=v&id=$id");
		die;
	}

	header("Location:".urlBase."$Mod[0]/$Mod[1]/os.php");
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
$smarty->assign('prioridades', $OrdemServicos->Prioridades($_SESSION['_user']['Empresa_ID']));
$smarty->assign('grupos', $OrdemServicos->Grupos($_SESSION['_user']['Empresa_ID']));
$smarty->assign('tipos', $OrdemServicos->Tipos($_SESSION['_user']['Empresa_ID']));
$smarty->assign('status', $OrdemServicos->Status());

// Gera numero de protocolo
$smarty->assign('protocolo', $OrdemServicos->Protocolo() . $protocolo = date('AHis') );

$smarty->assign('data', date('d/m/Y'));
$smarty->assign('horas', date('H:i'));
$smarty->assign('dados', $OrdemServicos->Dados($id));
$smarty->display('sistema/os/os_ie.html');