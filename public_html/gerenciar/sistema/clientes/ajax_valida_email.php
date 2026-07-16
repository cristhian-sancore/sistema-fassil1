<?php
$diretorio = "../../";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Conn = new Database();
$Sessao = new Sessao();

if($_POST) {
	$cad = new Clientes($Conn);
	$permissao = $cad->ValidaEmail($_POST['email']);
	echo json_encode(array('permissao' => $permissao));
}