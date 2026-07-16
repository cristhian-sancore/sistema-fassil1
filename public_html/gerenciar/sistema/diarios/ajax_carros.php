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
$Carros = new Carros($Conn);

if($_POST) {
	$b = (isset($_POST['busca']))?$_POST['busca']:null;
	$dd = $Carros->ListaAutoComplete($b);
	echo json_encode($dd);
}
