<?php
$diretorio = "";
include($diretorio . "_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Conn = new Database();
$Sessao = new Sessao();
$OrdemServicos = new OrdemServicos($Conn);

if($_POST) {
	$smarty->assign("urlBase", urlBase);
	$msg = $OrdemServicos->Mensagens($_POST['os'], 0);

	$total = count($msg);
	if($total == $_POST['os_qtd']){
		die;
	}

	$toca = false;
	if($msg[$total-1]['Msg_Recebimento']==1){
		$toca = true;
	}

	$smarty->assign('msg', $msg);
	$template = $smarty->fetch('_mensagem.html');
	echo json_encode(array("template" => utf8_encode($template), "total" => $total, "toca" => $toca));
}
