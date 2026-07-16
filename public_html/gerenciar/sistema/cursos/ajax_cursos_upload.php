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
$Cursos = new Cursos($Conn);

if($_FILES) {
	$_REQUEST['dir'] = $diretorio;
	$_REQUEST['envio'] = 1;
	
	$dd = $Cursos->Upload($_FILES, $_REQUEST);
	echo json_encode($dd);
}