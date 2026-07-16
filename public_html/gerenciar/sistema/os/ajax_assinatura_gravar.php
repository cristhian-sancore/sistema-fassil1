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
$Relatorios = new Relatorios($Conn);

if(!isset($_SESSION['_user'])){
	header("Location:".urlBase."index.php");
}

if($_POST) {
	$Relatorios->Assinatura_Gravar($_POST);

	/*ENVIA EMAIL*/
	/*if($_SERVER['SERVER_NAME']!='localhost') {
		$dd = $OrdemServicos->Dados($id);
		$dd['url'] = urlSite;

		$smarty->assign('dados', $dd);
		$template = $smarty->fetch('sistema/os/_email_ticket.html');

		$Email->Ticket($dd, $template);
	}*/

	echo json_encode(array('d' => true));
}