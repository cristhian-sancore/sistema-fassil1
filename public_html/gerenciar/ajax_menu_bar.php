<?php
$diretorio = "";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Menu = new Menu($Conn);

if(!isset($_SESSION['_user'])){
	echo("<script>location.href = '".urlBase."index.php';</script>");
}

if($_POST){
	$m = explode("-", $_POST['id']);

	$_SESSION['_m']['n1'] = (isset($m[0]))?$m[0]:null;
	$_SESSION['_m']['n2'] = (isset($m[1]))?$m[1]:null;
	$_SESSION['_m']['n3'] = (isset($m[2]))?$m[2]:null;
	$_SESSION['_m']['n4'] = (isset($m[3]))?$m[3]:null;

	echo json_encode(true);
}