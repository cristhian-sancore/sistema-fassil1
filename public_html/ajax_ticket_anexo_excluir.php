<?php
$diretorio = "";
include($diretorio . "_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");


$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$OrdemServicos = new OrdemServicos($Conn);

if($_POST) {
	$OrdemServicos->Anexo_Excluir($_POST['id'], 'gerenciar/');
	echo json_encode(array('d' => true));
}