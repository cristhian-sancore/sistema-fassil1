<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();

if($_POST) {
	$cad = new Cadastro($Conn);
	$permissao = $cad->ValidaLogin($_POST['login']);
	echo json_encode(array('permissao' => $permissao));
}