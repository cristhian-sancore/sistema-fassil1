<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();

if($_POST) {
	$cad = new Cadastro($Conn);
	$permissao = $cad->ValidaEmail($_POST['email']);
	echo json_encode(array('permissao' => $permissao));
}