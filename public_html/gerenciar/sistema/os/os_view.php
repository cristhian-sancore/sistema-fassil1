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
$id = (isset($_REQUEST['id']))?$_REQUEST['id']:0;
$Cliente_ID = (isset($_REQUEST['c']))?$_REQUEST['c']:0;

if($_POST){
	$d = $OrdemServicos->Dados($id);

	$email = false;
	if($ac=='a' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
		$OrdemServicos->Edita_OS($_POST, $id);

		/*ENVIA EMAIL*/
		if($id && $_SERVER['SERVER_NAME']!='localhost' && ($d['Status_ID']!=$_POST['status'] || $d['OS_NotaUsuario']!=$_POST['nota'])){
			$email = true;
		}
	}elseif($ac=='f' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
		$_POST['Cliente_ID'] = $Cliente_ID;
		$OrdemServicos->Confirmacao_OS($_POST, $id);

		/*ENVIA EMAIL*/
		if($id && $_SERVER['SERVER_NAME']!='localhost'){
			$email = true;
		}
	}

	if($email){
		$dd = $OrdemServicos->Dados($id);
		$dd['url'] = urlSite;

		$smarty->assign('dados', $dd);
		$template = $smarty->fetch('sistema/os/_email_ticket.html');

		$Email->Ticket($dd, $template);
	}

	if($ac=='f'){
		header("Location:".urlBase."$Mod[0]/$Mod[1]/os_relatorio.php?ac=i&id=$id");
	}
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
$smarty->assign('status', $OrdemServicos->Status(true));
$smarty->assign('protocolo', $OrdemServicos->Protocolo());
$smarty->assign('data', date('d/m/Y'));
$smarty->assign('horas', date('h:i'));
$smarty->assign('dados', $OrdemServicos->Dados($id));
$smarty->assign('msg', $OrdemServicos->Mensagens($id, 1));

if($ac=='i'){
	$smarty->display('sistema/os/os_invoice_imprimir.html');

}else{
	$smarty->display('sistema/os/os_view.html');
}