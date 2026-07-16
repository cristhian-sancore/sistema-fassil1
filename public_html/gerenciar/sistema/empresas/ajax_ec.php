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
$Estados = new Estados($Conn);

if($_POST) {
	$smarty->assign('estados', $Estados->Lista());
	$template = $smarty->fetch('sistema/empresas/_ajax_ec.html');
	echo utf8_encode($template);
}
?>