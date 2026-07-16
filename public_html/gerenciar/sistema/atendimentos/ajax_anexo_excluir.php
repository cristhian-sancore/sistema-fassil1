<?php
$diretorio = "../../";
include($diretorio . "_config.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$SaidasExpedientes = new SaidasExpedientes($Conn);

if(!isset($_SESSION['_user'])){
	header("Location:".urlBase."index.php");
}

if($_POST) {
	$SaidasExpedientes->Anexo_Excluir($_POST['id'], $diretorio);
	echo json_encode(array('d' => true));
}