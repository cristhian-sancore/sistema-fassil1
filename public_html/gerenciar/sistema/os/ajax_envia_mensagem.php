<?php
$diretorio = "../../";
include($diretorio . "_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Conn = new Database();
$Sessao = new Sessao();
$OrdemServicos = new OrdemServicos($Conn);

if($_FILES) {
	$_REQUEST['diretorio'] = "../../midias/";
	$OrdemServicos->Mensagem_Upload($_FILES, $_REQUEST);
}elseif($_POST) {
	$_POST['recebimento'] = 1;
	$Msg_ID = $OrdemServicos->Envia_Mensagem_OS($_POST);
	echo json_encode(array('id' => $Msg_ID));
}
